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

namespace ShopBundle\Controller\Admin;

use ShopBundle\Entity\ReservationPermission,
    Zend\View\Model\ViewModel;

/**
 * ReservationPermissionController
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class ReservationPermissionController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('ShopBundle\Entity\ReservationPermission')
                ->findAllQuery(),
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
        $form = $this->getForm('shop_reservationPermission_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $reservationPermission = $form->hydrateObject();
                $this->getEntityManager()->persist($reservationPermission);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The reservation permission was successfully created!'
                );

                $this->redirect()->toRoute(
                    'shop_admin_shop_reservationpermission',
                    array(
                        'action' => 'add',
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

    public function togglepermissionAction()
    {
        $this->initAjax();
        if (!($reservationPermission = $this->getReservationPermissionEntity())) {
            return new ViewModel();
        }
        $reservationPermission->setReservationsAllowed(!$reservationPermission->getReservationsAllowed());
        $this->getEntityManager()->persist($reservationPermission);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();
        if (!($product = $this->getReservationPermissionEntity())) {
            return new ViewModel();
        }
        $this->getEntityManager()->remove($product);

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
     * @return ReservationPermission|null
     */
    private function getReservationPermissionEntity()
    {
        $person = $this->getEntityById('CommonBundle\Entity\User\Person');

        $reservationPermission = $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\ReservationPermission')
            ->findOneByPerson($person);

        if (!($reservationPermission instanceof ReservationPermission)) {
            $this->flashMessenger()->error(
                'Error',
                'No reservation permission was found!'
            );

            return;
        }

        return $reservationPermission;
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $reservationPermissions = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($reservationPermissions as $reservationPermission) {
            $item = (object) array();
            $item->id = $reservationPermission->getPerson()->getId();
            $item->name = $reservationPermission->getPerson()->getFullName();
            $item->reservationsAllowed = $reservationPermission->getReservationsAllowed();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return \Doctrine\ORM\Query|null
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('ShopBundle\Entity\ReservationPermission')
                    ->findByNameQuery($this->getParam('string'));
        }
    }
}
