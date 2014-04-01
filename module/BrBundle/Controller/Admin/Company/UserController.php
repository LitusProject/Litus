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

use BrBundle\Entity\User\Person\Corporate as CorporatePerson,
    BrBundle\Form\Admin\Company\User\Add as AddForm,
    BrBundle\Form\Admin\Company\User\Edit as EditForm,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

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
        if (!($company = $this->_getCompany()))
            return;

        $paginator = $this->paginator()->createFromEntity(
            'BrBundle\Entity\User\Person\Corporate',
            $this->getParam('page'),
            array(
                'canLogin' => 'true',
                'company'  => $company->getId()
            ),
            array(
                'username' => 'ASC'
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
        if (!($company = $this->_getCompany()))
            return;

        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $user = new CorporatePerson(
                    $company,
                    $formData['username'],
                    array(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName('corporate')
                    ),
                    $formData['first_name'],
                    $formData['last_name'],
                    $formData['email'],
                    $formData['phone_number'],
                    $formData['sex']
                );
                $this->getEntityManager()->persist($user);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The corporate user was successfully created!'
                    )
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
        if (!($user = $this->_getUser()))
            return;

        $form = new EditForm($this->getEntityManager(), $user);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $user->setFirstName($formData['first_name'])
                    ->setLastName($formData['last_name'])
                    ->setEmail($formData['email'])
                    ->setSex($formData['sex'])
                    ->setPhoneNumber($formData['phone_number']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The corporate user was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'br_admin_company_user',
                    array(
                        'action' => 'manage',
                        'id' => $user->getCompany()->getId()
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
        if (!($user = $this->_getUser()))
            return new ViewModel();

        $user->activate(
            $this->getEntityManager(),
            $this->getMailTransport(),
            false,
            'br.account_activated_mail',
            86400*30
        );

        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Succes',
                'The user was successfully activated!'
            )
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

        if (!($user = $this->_getUser()))
            return new ViewModel();

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
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the company!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findOneById($this->getParam('id'));

        if (null === $company) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No company with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $company;
    }

    /**
     * @return \CommonBundle\Entity\User\Person
     */
    private function _getUser()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the corporate user!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\User\Person\Corporate')
            ->findOneById($this->getParam('id'));

        if (null === $company) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No corporate user with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $company;
    }
}
