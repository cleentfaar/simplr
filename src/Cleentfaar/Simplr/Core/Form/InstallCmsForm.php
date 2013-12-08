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
use Symfony\Component\HttpFoundation\Request;
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
            'button'
        );
        $builder->add(
            'button_reset',
            'reset'
        );
        $builder->add(
            'button_back',
            'button'
        );
        /**
         * {% set renderBackButton = flow.getCurrentStepNumber() in (flow.getFirstStepNumber() + 1) .. flow.getLastStepNumber() %}
        <div class="craue_formflow_buttons craue_formflow_button_count_{% if renderBackButton %}3{% else %}2{% endif %}">
        {#
        Default button (the one trigged by pressing the enter/return key) must be defined first.
        Thus, all buttons are defined in reverse order and will be reversed again via CSS.
        See http://stackoverflow.com/questions/1963245/multiple-submit-buttons-specifying-default-button
        #}
        <button type="submit" class="craue_formflow_button_last">
        {%- if flow.getCurrentStepNumber() < flow.getLastStepNumber() -%}
        {{- 'button.next' | trans({}, 'CraueFormFlowBundle') -}}
        {%- else -%}
        {{- 'button.finish' | trans({}, 'CraueFormFlowBundle') -}}
        {%- endif -%}
        </button>

        {% if renderBackButton %}
        {# see http://www.html5rocks.com/en/tutorials/forms/html5forms/ #}
        <button type="submit" name="{{ flow.getFormTransitionKey() }}" value="back" formnovalidate="formnovalidate">
        {{- 'button.back' | trans({}, 'CraueFormFlowBundle') -}}
        </button>
        {% endif %}

        <button type="submit" class="craue_formflow_button_first" name="{{ flow.getFormTransitionKey() }}" value="reset" formnovalidate="formnovalidate">
        {{- 'button.reset' | trans({}, 'CraueFormFlowBundle') -}}
        </button>
        </div>

         */
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                 'translation_domain' => 'installation'
            )
        );
    }

    public function getName()
    {
        return 'installCms';
    }
}
