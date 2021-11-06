<?php

namespace BrBundle\Controller\Corporate;

use Laminas\View\Model\ViewModel;

/**
 * CompanyController
 */
class CompanyController extends \BrBundle\Component\Controller\CorporateController
{
    public function editAction()
    {
        $company = $this->getCorporateEntity()->getCompany();
        if ($company === null) {
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
