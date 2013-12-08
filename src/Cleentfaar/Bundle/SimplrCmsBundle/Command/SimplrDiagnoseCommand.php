<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Bundle\SimplrCmsBundle\Command;

use Cleentfaar\Simplr\Core\Debug\Doctor;
use Cleentfaar\Simplr\Core\Debug\DoctorReport;
use Cleentfaar\Simplr\Core\Entity\Page;
use Cleentfaar\Simplr\Core\Simplr;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Route;

class SimplrDiagnoseCommand extends ContainerAwareCommand
{

    const CURRENT_DIRECTORY = 'current directory';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('simplr:diagnose')
            ->setDescription(
                'Diagnoses your Simplr installation to find '.
                'possible problems or get suggestions for improvements'
            )
            ->setHelp(
                'The <info>%command.name%</info> command diagnoses your Simplr installation, '.
                'to find possible problems or get suggestions for improvements '.
                '<info>php %command.full_name%</info>'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targetPath = getcwd();

        $output->writeln(
            sprintf(
                "Installing the Simplr CMS into <comment>%s</comment>",
                $targetPath
            )
        );

        /**
         * @var Doctor $simplrDoctor
         */
        $simplrDoctor = $this->getContainer()->get('simplr.doctor');
        $report = $simplrDoctor->createReport($input, $output);

        if ($report === null) {
            throw new \Exception(
                "Simplr failed to create a report on your project's current status\n ".
                "Are you sure you have installed all dependencies and read the installation documentation?\n ".
                "You can run <comment>php composer update</comment> to make sure you do."
            );
        }

        return $this->sendReportToOutput($report, $input, $output);
    }

    protected function sendReportToOutput(DoctorReport $report, InputInterface $input, OutputInterface $output)
    {
        $dateCreated = $report->getDateTimeCreated()->format('Y-m-d H:i:s');
        $output->writeln("----------------------------------------------------------------");
        $output->writeln("- SIMPLR REPORT ------------------------------------------------");
        $output->writeln(sprintf("- Created on: %s ------------------------------", $dateCreated));
        $output->writeln("----------------------------------------------------------------");
        $output->writeln("");
        $output->writeln("The following report was made about your installation:");
        $output->writeln("");
        $output->writeln("----------------------------------------------------------------");
        $output->writeln("- ROUTES -------------------------------------------------------");
        $output->writeln("----------------------------------------------------------------");
        foreach ($report->getRoutes() as $routeObject) {
            /**
             * @var Route $routeObject
             */
            $output->writeln($routeObject->getPath().": ".implode("\n", $routeObject->getRequirements()));
        }
        $output->writeln("");
        $output->writeln("----------------------------------------------------------------");
        $output->writeln("- PAGES --------------------------------------------------------");
        $output->writeln("----------------------------------------------------------------");
        foreach ($report->getPages() as $page) {
            /**
             * @var Page $page
             */
            $output->writeln($page->getSlug().": ".$page->getTemplate());
        }
        return true;
    }
}
