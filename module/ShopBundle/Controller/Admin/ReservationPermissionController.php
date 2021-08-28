<?php

namespace ShopBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use ShopBundle\Entity\Reservation\Permission as ReservationPermission;

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
                ->getRepository('ShopBundle\Entity\Reservation\Permission')
                ->findAllQuery(),
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

        $reservationPermission = $this->getReservationPermissionEntity();
        if ($reservationPermission === null) {
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

        $product = $this->getReservationPermissionEntity();
        if ($product === null) {
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
            ->getRepository('ShopBundle\Entity\Reservation\Permission')
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
                    ->getRepository('ShopBundle\Entity\Reservation\Permission')
                    ->findByNameQuery($this->getParam('string'));
        }
    }
}
