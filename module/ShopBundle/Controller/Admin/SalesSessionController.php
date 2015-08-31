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

use DateTime,
    ShopBundle\Entity\SalesSession,
    Zend\View\Model\ViewModel;

/**
 * SalesSessionController
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class SalesSessionController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('ShopBundle\Entity\SalesSession')
                ->findAllFutureQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('ShopBundle\Entity\SalesSession')
                ->findAllOldQuery(),
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
        $form = $this->getForm('shop_salesSession_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $startDate = self::loadDate($formData['start_date']);
                $endDate = self::loadDate($formData['end_date']);

                $interval = $startDate->diff($endDate);
                $weekStartDate = clone $startDate;

                for ($weeks = 0; $weeks < $formData['duplicate_weeks']; ++$weeks) {
                    $currentStartDate = clone $weekStartDate;
                    for ($days = 0; $days < $formData['duplicate_days']; ++$days) {
                        $salesSession = $form->hydrateObject();

                        $salesSession->setStartDate(clone $currentStartDate);

                        $currentEndDate = clone $currentStartDate;
                        $currentEndDate->add($interval);
                        $salesSession->setEndDate($currentEndDate);

                        $this->getEntityManager()->persist($salesSession);

                        $currentStartDate = $currentStartDate->modify('+1 day');
                    }
                    $weekStartDate = $weekStartDate->modify('+1 week');
                }
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The sales session was successfully created!'
                );

                $this->redirect()->toRoute(
                    'shop_admin_shop_salessession',
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

    public function editAction()
    {
        if (!($salesSession = $this->getSalesSessionEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('shop_salesSession_edit', array('salesSession' => $salesSession));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The session was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'shop_admin_shop_salessession',
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
        if (!($salesSession = $this->getSalesSessionEntity())) {
            return new ViewModel();
        }
        $this->getEntityManager()->remove($salesSession);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $salesSessions = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($salesSessions as $session) {
            $item = (object) array();
            $item->id = $session->getId();
            $item->start_date = $session->getStartDate()->format('d/m/Y H:i');
            $item->end_date = $session->getEndDate()->format('d/m/Y H:i');
            $item->remarks = $session->getRemarks();
            $item->reservations_possible = $session->getReservationsPossible();

            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function searcholdAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $salesSessions = $this->searchold()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($salesSessions as $session) {
            $item = (object) array();
            $item->id = $session->getId();
            $item->start_date = $session->getStartDate()->format('d/m/Y H:i');
            $item->end_date = $session->getEndDate()->format('d/m/Y H:i');
            $item->remarks = $session->getRemarks();
            $item->reservations_possible = $session->getReservationsPossible();

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
            case 'remarks':
                return $this->getEntityManager()
                    ->getRepository('ShopBundle\Entity\SalesSession')
                    ->findAllFutureByRemarksQuery($this->getParam('string'));
        }
    }

    /**
	 * @return \Doctrine\ORM\Query|null
	 */
    private function searchold()
    {
        switch ($this->getParam('field')) {
            case 'remarks':
                return $this->getEntityManager()
                    ->getRepository('ShopBundle\Entity\SalesSession')
                    ->findAllOldByRemarksQuery($this->getParam('string'));
        }
    }

    /**
	 * @return SalesSession|null
	 */
    private function getSalesSessionEntity()
    {
        $salesSession = $this->getEntityById('ShopBundle\Entity\SalesSession');
        if (!($salesSession instanceof SalesSession)) {
            $this->flashMessenger()->error(
                'Error',
                'No session was found!'
            );
            $this->redirect()->toRoute(
                'shop_admin_shop_salessession',
                array(
                    'action' => 'manage',
                )
            );

            return null;
        }

        return $salesSession;
    }

    /**
	 * @param  string $date
	 * @return DateTime|null
	 */
    private static function loadDate($date)
    {
        return DateTime::createFromFormat('d#m#Y H#i', $date) ?: null;
    }
}
