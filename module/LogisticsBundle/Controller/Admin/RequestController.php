<?php

namespace LogisticsBundle\Controller\Admin;

use CommonBundle\Entity\User\Person\Academic;
use DateTime;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\Order;
use LogisticsBundle\Entity\Order\OrderArticleMap;
use LogisticsBundle\Entity\Request;

/**
 * RequestController
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 */
class RequestController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $requests = $this->getUnhandledRequests();

        // Gets last order for every request
        $lastOrders = array();
        foreach ($requests as $request) {
            if ($this->getLastOrderByRequest($request)) {
                $lastOrders[] = $this->getLastOrderByRequest($request);
            }
        }

        return new ViewModel(
            array(
                'requests'    => $lastOrders,
            )
        );
    }

    public function approvedAction()
    {
        $requests = $this->getHandledRequests();

        // Gets last order for every request
        $lastOrders = array();
        foreach ($requests as $request) {
            $lastOrders[] = $this->getLastOrderByRequest($request);
        }

        return new ViewModel(
            array(
                'requests'    => $lastOrders,
            )
        );
    }

    public function viewAction()
    {
        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        $articles = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findAllByOrderQuery($order)->getResult();

        $lastOrders = $this->getAllOrdersByRequest($order->getRequest());

        return new ViewModel(
            array(
                'order' => $order,
                'articles' => $articles,
                'lastOrders' => $lastOrders,
            )
        );
    }

    public function conflictingAction()
    {
        $requests = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Request')
            ->findNewRequests();

        // Gets last order for every request
        $lastOrders = array();
        foreach ($requests as $request) {
            $lastOrders[] = $this->getLastOrderByRequest($request);
        }

        $mappings = array();
        foreach ($lastOrders as $order){
            $mappings = array_merge($mappings, $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
                ->findAllByOrderQuery($order)->getResult());
        }

        $conflicts = array();
        // Loop over all made mappings
        foreach ($mappings as $map) {
            // Find overlaps
            $overlapping_maps = $this->findOverlapping($mappings, $map);
            // Delete map so it doesn't pop up twice
            $mappings = array_udiff(array($map), $overlapping_maps, function ($obj_a, $obj_b) {
                return $obj_a->getId() - $obj_b->getId();
            });

            // Look if all overlapping article amounts surpass the amount available
            $total = 0;
            foreach ($overlapping_maps as $overlap) {
                $total += $overlap->getAmount();
            }
            $max = $map->getArticle()->getAmountAvailable();
            if ($total > $max) {
                $conflict = array(
                    'article'  => $map->getArticle(),
                    'mappings' => $overlapping_maps,
                    'total'    => $total
                );
                $conflicts[] = $conflict;
            }
        }

        return new ViewModel(
            array(
                'conflicts' => $conflicts,
            )
        );
    }

//        $request = $this->getRequestEntity();
//        if ($request === null) {
//            return new ViewModel();
//        }
//
//        $newOrder = $request->getEditOrder();
//        $oldOrder = $request->getOrder();
//
//        $mappings = array();
//
//        if ($newOrder === null) {
//            $newMaps = $this->getEntityManager()
//                ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
//                ->findAllByOrderQuery($oldOrder)->getResult();
//        } else {
//            $oldMaps = $this->getEntityManager()
//                ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
//                ->findAllByOrderQuery($oldOrder)->getResult();
//            $newMaps = $this->getEntityManager()
//                ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
//                ->findAllByOrderQuery($newOrder)->getResult();
//
//            foreach ($oldMaps as $map) {
//                $id = $map->getArticle()->getId();
//                $mappings[$id] = array(
//                    'name' => $map->getArticle()->getName(),
//                    'old'  => $map->getAmount(),
//                    'new'  => 0,
//                );
//            }
//        }
//
//        foreach ($newMaps as $map) {
//            $id = $map->getArticle()->getId();
//            if (array_key_exists($id, $mappings)) {
//                $mappings[$id]['new'] = $map->getAmount();
//            } else {
//                $mappings[$id] = array(
//                    'name' => $map->getArticle()->getName(),
//                    'new'  => $map->getAmount(),
//                    'old'  => 0,
//                );
//            }
//        }
//
//        $diffs = array(
//            'Name'        => array($oldOrder->getName()),
//            'Location'    => array($oldOrder->getLocation()->getName()),
//            'Creator'     => array($oldOrder->getCreator()->getFullName()),
//            'Contact'     => array($oldOrder->getContact()),
//            'Start Date'  => array($oldOrder->getStartDate()->format('d/m/Y H:i')),
//            'End Date'    => array($oldOrder->getEndDate()->format('d/m/Y H:i')),
//            'Description' => array($oldOrder->getDescription()),
//        );
//        if ($newOrder !== null) {
//            $diffs['Name'][] = $newOrder->getName();
//            $diffs['Location'][] = $newOrder->getLocation()->getName();
//            $diffs['Creator'][] = $newOrder->getCreator()->getFullName();
//            $diffs['Contact'][] = $newOrder->getContact();
//            $diffs['Start Date'][] = $newOrder->getStartDate()->format('d/m/Y H:i');
//            $diffs['End Date'][] = $newOrder->getEndDate()->format('d/m/Y H:i');
//            $diffs['Description'][] = $newOrder->getDescription();
//        }
//
//        return new ViewModel(
//            array(
//                'request'  => $request,
//                'newOrder' => $newOrder,
//                'oldOrder' => $oldOrder,
//                'diffs'    => $diffs,
//                'mappings' => $mappings,
//            )
//        );
//    }
//
//    public function approveAction()
//    {
//        $request = $this->getRequestEntity();
//        if ($request === null) {
//            return new ViewModel();
//        }
//
//        $request->approveRequest();
//        $request->handled();
//        $request->setRemoved(true);
//
//        $this->getEntityManager()->flush();
//
//        $this->sendMailToContact($request);
//        $this->flashMessenger()->success(
//            'Success',
//            'The request was succesfully approved.'
//        );
//
//        $this->redirect()->toRoute(
//            'logistics_admin_request',
//            array(
//                'action' => 'manage',
//            )
//        );
//
//        return new ViewModel();
//    }

    public function rejectAction()
    {
        $request = $this->getRequestEntity();
        if ($request === null) {
            return new ViewModel();
        }

        $form = $this->getForm('logistics_admin_request_reject');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $request->rejectRequest($formData['reject_reason']);
                $request->handled();

                $this->getEntityManager()->flush();

                $this->sendMailToContact($request, true);
                $this->flashMessenger()->success(
                    'Success',
                    'The request was succesfully rejected.'
                );

                $this->redirect()->toRoute(
                    'logistics_admin_request',
                    array(
                        'action' => 'manage',
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'form'    => $form,
                'request' => $request,
            )
        );
    }

    /**
     * @return Academic|null
     */
    private function getAcademicEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return null;
        }

        $academic = $this->getAuthentication()->getPersonObject();

        if (!($academic instanceof Academic)) {
            return;
        }

        return $academic;
    }

    /**
     * @return Order|null
     */
    private function getOrderEntity()
    {
        $order = $this->getEntityById('LogisticsBundle\Entity\Order', 'order');
        error_log($order? 'Order': 'No order');

        if (!($order instanceof Order)) {
            $this->flashMessenger()->error(
                'Error',
                'No Order was found!'
            );

            $this->redirect()->toRoute(
                'logistics_admin_request',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }
        return $order;
    }

    /**
     * @return Request|null
     */
    private function getRequestEntity()
    {
        $request = $this->getEntityById('LogisticsBundle\Entity\Request');

        if (!($request instanceof Request)) {
            $this->flashMessenger()->error(
                'Error',
                'No request was found!'
            );

            $this->redirect()->toRoute(
                'logistics_admin_request',
                array(
                    'action' => 'manage',
                )
            );
        }

        return $request;
    }

    /**
     * @return array
     */
    private function getOpenRequestsByAcademic(Academic $academic)
    {
        $unhandledRequests = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Request')
            ->findUnhandledByAcademic($academic);
        $handledRejects = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Request')
            ->findHandledByAcademic($academic);

        return array_merge($handledRejects, $unhandledRequests);
    }

//    /**
//     * @return array
//     */
//    private function getOpenRequestsByUnit($unit)
//    {
//        $unhandledRequests = $this->getEntityManager()
//            ->getRepository('LogisticsBundle\Entity\Request')
//            ->findAllUnhandledByUnit($unit);
//
//        $handledRejects = $this->getEntityManager()
//            ->getRepository('LogisticsBundle\Entity\Request')
//            ->findActiveRejectsByUnit($unit);
//
//        return array_merge($handledRejects, $unhandledRequests);
//    }

    /**
     * @return array
     */
    private function getUnhandledRequests()
    {
        return $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Request')
            ->findAllUnhandled();
    }

    /**
     * @return array
     */
    private function getHandledRequests()
    {
        return $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Request')
            ->findAllHandled();
    }

    /**
     * @param array $a1
     * @param array $a2
     * @return array
     */
    private function mergeArraysUnique(array $a1, array $a2)
    {
        foreach ($a2 as $e2) {
            if (!in_array($e2, $a1)) {
                array_push($a1, $e2);
            }
        }
        return $a1;
    }

    private function findOverlapping(array $array, OrderArticleMap $mapping)
    {
        $start = $mapping->getOrder()->getStartDate();
        $end = $mapping->getOrder()->getEndDate();

        $overlapping = array();
        foreach ($array as $map) {
            if ($map->getArticle() === $mapping->getArticle()
                && ($start <= $map->getOrder()->getStartDate() && $map->getOrder()->getStartDate() <= $end
                || $start <= $map->getOrder()->getEndDate() && $map->getOrder()->getEndDate() <= $end)
            ) {
                $overlapping[] = $map;
            }
        }
        return $overlapping;
    }

    /**
     * @param Request $request
     * @return Order
     */
    private function getLastOrderByRequest($request)                  // Gets the most recent order
    {
        $orders = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order')
            ->findAllByRequest($request);
        array_pop($orders);                                     // pop dummy order
        return current($orders);                                       // Gets the first element of an array
    }

    /**
     * @param Request $request
     */
    private function sendMailToContact(Request $request, $rejected = false)
    {
        $order = $this->getLastOrderByRequest($request);
        $mailAddress = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('logistics.order_mail');

        $language = $request->getContact()->getLanguage();
        if ($language === null) {
            $language = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev('en');
        }

        $mailName = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('logistics.order_mail_name');

        $mailData = $rejected ? unserialize($this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')->getConfigValue('logistics.order_request_rejected')) : unserialize($this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')->getConfigValue('logistics.order_request_confirmed'));

        $message = $mailData[$language->getAbbrev()]['content'];
        $subject = $mailData[$language->getAbbrev()]['subject'];

        $mail = new Message();
        if ($rejected === true) {
            $mail->setEncoding('UTF-8')
                ->setBody(
                    str_replace(
                        array('{{ name }}', '{{ type }}', '{{ end }}', '{{ start }}', '{{ reason }}'),
                        array($order->getName(), $request->getRequestType(), $order->getEndDate()->format('d/m/Y H:m'), $order->getStartDate()->format('d/m/Y H:m'), $request->getRejectMessage()),
                        $message
                    )
                )
                ->setFrom($mailAddress, $mailName)
                ->addTo($request->getContact()->getPersonalEmail(), $request->getContact()->getFullName())
                ->setSubject(str_replace('{{ name }}', $order->getName(), $subject));
        } else {
            $mail->setEncoding('UTF-8')
                ->setBody(
                    str_replace(
                        array('{{ name }}', '{{ type }}', '{{ end }}', '{{ start }}'),
                        array($order->getName(), $request->getRequestType(), $order->getEndDate()->format('d/m/Y H:m'), $order->getStartDate()->format('d/m/Y H:m')),
                        $message
                    )
                )
                ->setFrom($mailAddress, $mailName)
                ->addTo($request->getContact()->getPersonalEmail(), $request->getContact()->getFullName())
                ->setSubject(str_replace('{{ name }}', $order->getName(), $subject));
        }

        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }
    }
}
