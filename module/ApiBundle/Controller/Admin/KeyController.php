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

namespace ApiBundle\Controller\Admin;

use ApiBundle\Entity\Key;
use Laminas\View\Model\ViewModel;

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
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('api_key_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $key = $form->hydrateObject();

                $this->getEntityManager()->persist($key);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The key was successfully created!'
                );

                $this->redirect()->toRoute(
                    'api_admin_key',
                    array(
                        'action' => 'manage',
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
        $key = $this->getKeyEntity();
        if ($key === null) {
            return new ViewModel();
        }

        $form = $this->getForm('api_key_edit', $key);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The key was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'api_admin_key',
                    array(
                        'action' => 'manage',
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

        $key = $this->getKeyEntity();
        if ($key === null) {
            return new ViewModel();
        }

        $key->revoke();

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    /**
     * @return Key|null
     */
    private function getKeyEntity()
    {
        $key = $this->getEntityById('ApiBundle\Entity\Key');

        if (!($key instanceof Key)) {
            $this->flashMessenger()->error(
                'Error',
                'No key was found!'
            );

            $this->redirect()->toRoute(
                'api_admin_key',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $key;
    }
}
