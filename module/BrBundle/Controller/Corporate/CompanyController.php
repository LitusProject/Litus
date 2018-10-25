<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Controller\Corporate;

use BrBundle\Entity\Company;
use BrBundle\Entity\Company\Job;
use BrBundle\Entity\Company\Request\RequestVacancy;
use BrBundle\Entity\User\Person\Corporate;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;

/**
 * CompanyController
 */
class CompanyController extends \BrBundle\Component\Controller\CorporateController
{
    public function editAction()
    {
        if (!($company = $this->getCorporateEntity()->getCompany())) {
            return new ViewModel();
        }

        $form = $this->getForm('br_corporate_company_edit', array('company' => $company));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The company was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'br_corporate_company',
                    array(
                        'action' => 'edit',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }
}
