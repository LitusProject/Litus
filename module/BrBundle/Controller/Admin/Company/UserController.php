<?php

namespace BrBundle\Controller\Admin\Company;

use BrBundle\Entity\Company;
use BrBundle\Entity\User\Person\Corporate;
use Laminas\View\Model\ViewModel;

/**
 * ContactController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class UserController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $company = $this->getCompanyEntity();
        if ($company === null) {
            return;
        }

        $paginator = $this->paginator()->createFromEntity(
            'BrBundle\Entity\User\Person\Corporate',
            $this->getParam('page'),
            array(
                'canLogin' => 'true',
                'company'  => $company->getId(),
            ),
            array(
                'username' => 'ASC',
            )
        );

        return new ViewModel(
            array(
                'company'           => $company,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function addAction()
    {
        $company = $this->getCompanyEntity();
        if ($company === null) {
            return;
        }

        $form = $this->getForm('br_company_user_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $user = $form->hydrateObject();
                $user->setCompany($company);

                if ($formData['activate']) {
                    $user->activate(
                        $this->getEntityManager(),
                        $this->getMailTransport(),
                        false,
                        'br.account_activated_mail',
                        86400 * 90,
                    );
                }

                $this->getEntityManager()->persist($user);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The corporate user was successfully created!'
                );

                $this->redirect()->toRoute(
                    'br_admin_company_user',
                    array(
                        'action' => 'manage',
                        'id'     => $company->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'company' => $company,
                'form'    => $form,
            )
        );
    }

    public function editAction()
    {
        $user = $this->getCorporateEntity();
        if ($user === null) {
            return;
        }

        $form = $this->getForm('br_company_user_edit', $user);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The corporate user was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'br_admin_company_user',
                    array(
                        'action' => 'manage',
                        'id'     => $user->getCompany()->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'user' => $user,
                'form' => $form,
            )
        );
    }

    public function activateAction()
    {
        $user = $this->getCorporateEntity();
        if ($user === null) {
            return new ViewModel();
        }

        $user->activate(
            $this->getEntityManager(),
            $this->getMailTransport(),
            false,
            'br.account_activated_mail',
            86400 * 30
        );

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'Success',
            'The user was successfully activated!'
        );

        $this->redirect()->toRoute(
            'br_admin_company_user',
            array(
                'action' => 'edit',
                'id'     => $user->getId(),
            )
        );

        return new ViewModel();
    }

    public function deleteAction()
    {
        $this->initAjax();

        $user = $this->getCorporateEntity();
        if ($user === null) {
            return new ViewModel();
        }
        $this->getEntityManager()
            ->remove($user);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Company|null
     */
    private function getCompanyEntity()
    {
        $company = $this->getEntityById('BrBundle\Entity\Company');

        if (!($company instanceof Company)) {
            $this->flashMessenger()->error(
                'Error',
                'No company was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $company;
    }

    /**
     * @return Corporate|null
     */
    private function getCorporateEntity()
    {
        $corporate = $this->getEntityById('BrBundle\Entity\User\Person\Corporate');

        if (!($corporate instanceof Corporate)) {
            $this->flashMessenger()->error(
                'Error',
                'No corporate was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $corporate;
    }
}
