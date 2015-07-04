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

namespace CommonBundle\Controller\Admin;

use CommonBundle\Entity\General\Location,
    Zend\Http\Client,
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
                'active' => true,
            ),
            array(
                'name' => 'ASC',
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
        $form = $this->getForm('common_location_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The location was successfully created!'
                );

                $this->redirect()->toRoute(
                    'common_admin_location',
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
                'geocodingUrl' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.geocoding_api_url'),
            )
        );
    }

    public function editAction()
    {
        if (!($location = $this->getLocationEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('common_location_edit', $location);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The location was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'common_admin_location',
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

        if (!($location = $this->getLocationEntity())) {
            return new ViewModel();
        }

        $location->deactivate();

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function geocodingAction()
    {
        $geocodingUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.geocoding_api_url');

        $client = new Client(
            $geocodingUrl . (substr($geocodingUrl, -1) == '/' ? 'json' : '/json')
        );

        $client->setParameterGet(
            array(
                'sensor'  => 'false',
                'address' => urlencode(
                    $this->getRequest()->getPost()->get('street') . ' ' . $this->getRequest()->getPost()->get('number') . ', '
                        . $this->getRequest()->getPost()->get('postal') . ' ' . $this->getRequest()->getPost()->get('city') . ', '
                        . $this->getRequest()->getPost()->get('country')
                ),
            )
        );

        return new ViewModel(
            array(
                'result' => json_decode($client->send()->getBody()),
            )
        );
    }

    /**
     * @return Location|null
     */
    private function getLocationEntity()
    {
        $location = $this->getEntityById('CommonBundle\Entity\General\Location');

        if (!($location instanceof Location)) {
            $this->flashMessenger()->error(
                'Error',
                'No location was found!'
            );

            $this->redirect()->toRoute(
                'common_admin_location',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $location;
    }
}
