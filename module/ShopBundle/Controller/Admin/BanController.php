<?php

namespace ShopBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use ShopBundle\Repository\Reservation\Ban;

/**
 * BanPermissionController
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class BanController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('ShopBundle\Entity\Reservation\Ban')
                ->findActiveQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('ShopBundle\Entity\Reservation\Ban')
                ->findOldQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

//    shop_reservationPermission_add

    public function addAction()
    {
        $form = $this->getForm('shop_reservation_ban_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                error_log("before hydrate");
                $ban = $form->hydrateObject();
                error_log("before persist");
                $this->getEntityManager()->persist($ban);
                error_log("before flush");
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The ban was successfully created!'
                );

                $this->redirect()->toRoute(
                    'shop_admin_shop_ban',
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

    public function deleteAction()
    {
        $this->initAjax();

        $ban = $this->getBanEntity();
        if ($ban === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($ban);
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
     * @return Ban|null
     */
    private function getBanEntity()
    {
        $ban = $this->getEntityById('ShopBundle\Entity\Reservation\Ban');

        if (!($ban instanceof Ban)) {
            $this->flashMessenger()->error(
                'Error',
                'No ban was found!'
            );

            $this->redirect()->toRoute(
                'shop_admin_shop_ban',
                array(
                    'action' => 'manage',
                )
            );
            return;
        }
        return $ban;
    }

    public function searchAction()
    {
//        $this->initAjax();
//
//        $numResults = $this->getEntityManager()
//            ->getRepository('CommonBundle\Entity\General\Config')
//            ->getConfigValue('search_max_results');
//
//        $reservationPermissions = $this->search()
//            ->setMaxResults($numResults)
//            ->getResult();
//
//        $result = array();
//        foreach ($reservationPermissions as $reservationPermission) {
//            $item = (object) array();
//            $item->id = $reservationPermission->getPerson()->getId();
//            $item->name = $reservationPermission->getPerson()->getFullName();
//            $item->reservationsAllowed = $reservationPermission->getReservationsAllowed();
//            $result[] = $item;
//        }
//
//        return new ViewModel(
//            array(
//                'result' => $result,
//            )
//        );
    }

    /**
     * @return \Doctrine\ORM\Query|null
     */
    private function search()
    {
//        switch ($this->getParam('field')) {
//            case 'name':
//                return $this->getEntityManager()
//                    ->getRepository('ShopBundle\Entity\Reservation\Permission')
//                    ->findByNameQuery($this->getParam('string'));
//        }
    }
}
