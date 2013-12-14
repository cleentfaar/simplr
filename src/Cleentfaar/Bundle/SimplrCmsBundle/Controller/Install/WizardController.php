<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Bundle\SimplrCmsBundle\Controller\Install;

use Cleentfaar\Simplr\Core\Controller\BaseInstallController;
use Cleentfaar\Simplr\Core\Form\InstallCmsProcessor;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

class WizardController extends BaseInstallController
{
    public function indexAction()
    {
        $formData = new InstallCmsProcessor($this->container); // Your form data class. Has to be an object, won't work properly with an array.

        $flow = $this->get('simplr.form.flow.installCms'); // must match the flow's service id
        $flow->bind($formData);

        /**
         * @var FormInterface $form
         */
        $form = $flow->createForm();
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                // form for the next step
                $form = $flow->createForm();
            } else {
                // flow finished
                $installSuccessful = $formData->install();
                if ($installSuccessful === true) {
                    return $this->redirect($this->generateUrl('simplr_install_successful')); // redirect when done
                }
                foreach ($formData->getFailedReasons() as $reason) {
                    $form->addError(new FormError($reason));
                }
            }
        }

        return $this->render('@CleentfaarSimplrCms/Install/Wizard/index.html.twig', array(
           'form' => $form->createView(),
           'flow' => $flow,
        ));
    }
}
