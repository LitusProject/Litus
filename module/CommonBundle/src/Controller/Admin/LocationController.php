<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Form\Admin\Location\Add as AddForm,
    CommonBundle\Form\Admin\Location\Edit as EditForm,
    CommonBundle\Entity\General\Address,
    CommonBundle\Entity\General\Location,
    Zend\View\Model\ViewModel;

/**
 * LocationController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class LocationController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'CommonBundle\Entity\General\Location',
            $this->getParam('page'),
            array(
                'active' => true
            ),
            array(
                'name' => 'ASC'
            )
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
        $form = new AddForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $location = new Location(
                    $this->getEntityManager(),
                    $formData['name'],
                    new Address(
                        $formData['address_street'],
                        $formData['address_number'],
                        $formData['address_postal'],
                        $formData['address_city'],
                        $formData['address_country']
                    )
                );

                $this->getEntityManager()->persist($location);

                $this->getEntityManager()->flush();

                $form = new AddForm(
                    $this->getEntityManager()
                );

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The location was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_location',
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
        if (!($location = $this->_getLocation()))
            return new ViewModel();

        $form = new EditForm($location);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $location->setName($formData['name'])
                    ->setAddress(
                        $this->getEntityManager(),
                        new Address(
                            $formData['address_street'],
                            $formData['address_number'],
                            $formData['address_postal'],
                            $formData['address_city'],
                            $formData['address_country']
                        )
                    );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The location was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_location',
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

        if (!($location = $this->_getLocation()))
            return new ViewModel();

        $location->deactivate();

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                )
            )
        );
    }

    private function _getLocation()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the location!'
                )
            );

            $this->redirect()->toRoute(
                'admin_location',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $location = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Location')
            ->findOneById($this->getParam('id'));

        if (null === $location) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No location with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_location',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $location;
    }
}
