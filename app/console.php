<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Knp\Provider\ConsoleServiceProvider;
use Simplr\Command\PluginGenerateCommand;
use Simplr\Command\ThemeGenerateCommand;
use Symfony\Component\Console\Helper\HelperSet;

set_time_limit(0);

require_once __DIR__.'/../app/bootstrap.php';
$app = require_once __DIR__.'/../app/services.php';

$app->register(new ConsoleServiceProvider(), array(
    'console.name'              => 'Simplr Console Application',
    'console.version'           => '1.0.0',
    'console.project_directory' => __DIR__.'/..'
));

$input = new \Symfony\Component\Console\Input\ArgvInput();
$env = $input->getParameterOption(array('--env', '-e'), getenv('SYMFONY_ENV') ?: 'dev');


$console = $app['console'];

/*
 * Doctrine CLI
 */
$helperSet = new HelperSet(array(
    'db' => new ConnectionHelper($app['orm.em']->getConnection()),
    'em' => new EntityManagerHelper($app['orm.em'])
));

$console->setHelperSet($helperSet);
ConsoleRunner::addCommands($console);

$simplrCommands = array(
    new PluginGenerateCommand(),
    new ThemeGenerateCommand(),
);
$console->addCommands($simplrCommands);
$console->run();
