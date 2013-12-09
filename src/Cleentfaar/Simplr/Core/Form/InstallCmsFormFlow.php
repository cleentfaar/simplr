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

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InstallCmsFormFlow extends FormFlow
{

    /**
     * @var FormTypeInterface
     */
    protected $formType;

    public function setFormType(FormTypeInterface $formType)
    {
        $this->formType = $formType;
    }

    public function getName()
    {
        return 'installCms';
    }

    protected function loadStepsConfig()
    {
        return array(
            array(
                'label' => 'form.step.site_settings',
                'type' => $this->formType,
            ),
            array(
                'label' => 'form.step.database_configuration',
                'type' => $this->formType,
                'skip' => function ($estimatedCurrentStepNumber, FormFlowInterface $flow) {
                        $canSkipStep2 = false;
                        // some logic to skip anyway...
                        return $canSkipStep2;
                    },
            ),
            array(
                'label' => 'form.step.confirmation',
            ),
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                 'translation_domain' => 'installation'
            )
        );
    }
}
