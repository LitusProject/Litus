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
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Controller\Admin\Company;

use Zend\View\Model\ViewModel;

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
        if (!($company = $this->_getCompany())) {
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
                'company' => $company,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function addAction()
    {
        if (!($company = $this->_getCompany())) {
            return;
        }

        $form = $this->getForm('br_company_user_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $user = $form->hydrateObject();
                $user->setCompany($company);

                $user->activate(
                    $this->getEntityManager(),
                    $this->getMailTransport(),
                    false
                );

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
                        'id' => $company->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'company' => $company,
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        if (!($user = $this->_getUser())) {
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
                        'id' => $user->getCompany()->getId(),
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
        if (!($user = $this->_getUser())) {
            return new ViewModel();
        }

        $user->activate(
            $this->getEntityManager(),
            $this->getMailTransport(),
            false,
            'br.account_activated_mail',
            86400*30
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
                'id' => $user->getId(),
            )
        );

        return new ViewModel();
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($user = $this->_getUser())) {
            return new ViewModel();
        }

        $user->disableLogin();
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return \BrBundle\Entity\Company
     */
    private function _getCompany()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the company!'
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findOneById($this->getParam('id'));

        if (null === $company) {
            $this->flashMessenger()->error(
                'Error',
                'No company with the given ID was found!'
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
     * @return \CommonBundle\Entity\User\Person\Corporate
     */
    private function _getUser()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the corporate user!'
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $corporate = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\User\Person\Corporate')
            ->findOneById($this->getParam('id'));

        if (null === $corporate) {
            $this->flashMessenger()->error(
                'Error',
                'No corporate user with the given ID was found!'
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
