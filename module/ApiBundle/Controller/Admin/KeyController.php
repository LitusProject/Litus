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

namespace ApiBundle\Controller\Admin;

use ApiBundle\Entity\Key,
    ApiBundle\Form\Admin\Key\Add as AddForm,
    ApiBundle\Form\Admin\Key\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * KeyController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class KeyController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('ApiBundle\Entity\Key')
                ->findAllActiveQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);
                do {
                    $code = md5(uniqid(rand(), true));
                    $found = $this->getEntityManager()
                        ->getRepository('ApiBundle\Entity\Key')
                        ->findOneByCode($code);
                } while (isset($found));

                $roles = array();
                $roles[] = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Role')
                    ->findOneByName('student');

                if (isset($formData['roles'])) {
                    foreach ($formData['roles'] as $role) {
                        if ('student' == $role) continue;
                        $roles[] = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName($role);
                    }
                }

                $key = new Key(
                    $formData['host'],
                    $code,
                    $formData['check_host'],
                    $roles
                );
                $this->getEntityManager()->persist($key);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The key was successfully created!'
                );

                $this->redirect()->toRoute(
                    'api_admin_key',
                    array(
                        'action' => 'manage'
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

    public function editAction()
    {
        if (!($key = $this->_getKey()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $key);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $roles = array();
                $roles[] = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Role')
                    ->findOneByName('student');

                if (isset($formData['roles'])) {
                    foreach ($formData['roles'] as $role) {
                        if ('student' == $role) continue;
                        $roles[] = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName($role);
                    }
                }

                $key->setHost($formData['host'])
                    ->setCheckHost($formData['check_host'])
                    ->setRoles($roles);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The key was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'api_admin_key',
                    array(
                        'action' => 'manage'
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

    public function deleteAction()
    {
        $this->initAjax();

        if (!($key = $this->_getKey()))
            return new ViewModel();

        $key->revoke();

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    /**
     * @return Key|null
     */
    private function _getKey()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the key!'
            );

            $this->redirect()->toRoute(
                'api_admin_key',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $key = $this->getEntityManager()
            ->getRepository('ApiBundle\Entity\Key')
            ->findOneById($this->getParam('id'));

        if (null === $key) {
            $this->flashMessenger()->error(
                'Error',
                'No key with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'api_admin_key',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $key;
    }
}
