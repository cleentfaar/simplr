<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Core\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InstallCmsForm extends AbstractType
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->request = $container->get('request');
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['flow_step']) {
            case 1:
                $builder->add(
                    'site_title',
                    'text',
                    array(
                         'label' => 'form.label.site_title',
                         'data' => 'My Beautiful Website',
                         'attr' => array(
                             'placeholder' => 'My Beautiful Website',
                         ),
                    )
                );
                $builder->add(
                    'site_url',
                    'url',
                    array(
                         'label' => 'form.label.site_url',
                         'data' => $this->request->getSchemeAndHttpHost(),
                         'attr' => array(
                             'placeholder' => $this->request->getSchemeAndHttpHost(),
                         ),
                    )
                );
                break;
            case 2:
                $availableDrivers = \PDO::getAvailableDrivers();
                $choices = array();
                foreach ($availableDrivers as $driver) {
                    $choices[$driver] = 'form.choice.database_driver.' . $driver;
                }
                $builder->add(
                    'database_driver',
                    'choice',
                    array(
                         'label' => 'form.label.database_driver',
                         'empty_value' => 'form.choice.empty_value',
                         'choices' => $choices
                    )
                );
                break;
        }
        $builder->add(
            'button_submit',
            'button',
            array(
                'label' => 'form.button.next',
            )
        );
        $builder->add(
            'button_reset',
            'reset',
            array(
                'label' => 'form.button.reset',
            )
        );
        $builder->add(
            'button_back',
            'button',
            array(
                'label' => 'form.button.back',
            )
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                 'translation_domain' => 'installation'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'installCms';
    }
}
