<?php

namespace LogisticsBundle\Controller\Admin;

use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use CommonBundle\Entity\User\Person\Academic;
use DateTime;
use Laminas\Http\Headers;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\Order;
use LogisticsBundle\Entity\Order\OrderArticleMap;
use LogisticsBundle\Entity\Request;

/**
 * RequestController
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class RequestController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $requests = $this->getUnhandledRequests();

        $now = new DateTime();
        // Gets last order for every request
        $lastOrders = array();
        foreach ($requests as $request) {
            $lastOrder = $this->getLastOrderByRequest($request);
            if ($lastOrder && $lastOrder->getEndDate() >= $now) {
                $lastOrders[] = $lastOrder;
            }
        }

        return new ViewModel(
            array(
                'requests' => $lastOrders,
            )
        );
    }

    public function approvedAction()
    {
        $requests = $this->getHandledRequests();

        $now = new DateTime();
        // Gets last order for every request
        $lastOrders = array();
        foreach ($requests as $request) {
            $lastOrder = $this->getLastOrderByRequest($request);
            if ($lastOrder->getEndDate() >= $now) {
                $lastOrders[] = $lastOrder;
            }
        }

        return new ViewModel(
            array(
                'requests' => $lastOrders,
            )
        );
    }

    // gets all requests till next monday
    public function comingAction()
    {
        $requests = $this->getAllRequests();

        $now = new DateTime();
        $nextMonday = new DateTime();
        $nextMonday->modify('next monday');
        $interval = $now->diff($nextMonday);
        if ($interval->days <= 3) {
            $nextMonday->modify('next monday');
        }

        // Gets last order for every request
        $lastOrders = array();
        foreach ($requests as $request) {
            $lastOrder = $this->getLastOrderByRequest($request);
            if ($lastOrder && $now <= $lastOrder->getStartDate() && $lastOrder->getStartDate() <= $nextMonday) {
                $lastOrders[] = $lastOrder;
            }
        }

        return new ViewModel(
            array(
                'requests' => $lastOrders,
            )
        );
    }

    public function csvAction()
    {
        $order = $this->getLastOrderByRequest($this->getRequestEntity());

        return new ViewModel(
            array(
                'data' => $this->generateCsvFile($order),
            )
        );
    }

    public function exportAction()
    {
        $requests = $this->getAllRequests();

        $now = new DateTime();
        $nextMonday = new DateTime();
        $nextMonday->modify('next monday');
        $interval = $now->diff($nextMonday);
        if ($interval->days <= 3) {
            $nextMonday->modify('next monday');
        }

        $comingRequests = array();
        // Gets last order for every request
//        $lastOrders = array();
        foreach ($requests as $request) {
            $lastOrder = $this->getLastOrderByRequest($request);
            if ($lastOrder && $now <= $lastOrder->getStartDate() && $lastOrder->getStartDate() <= $nextMonday) {
                $comingRequests[] = $lastOrder->getRequest();
            }
        }

        foreach ($requests as $request) {
            $order = $this->getLastOrderByRequest($request);
            return new ViewModel(
                array(
                    'data' => $this->generateCsvFile($order),
                )
            );
        }

//        $now = new DateTime();
//        $nextMonday = new DateTime();
//        $nextMonday->modify('next monday');
//        $interval = $now->diff($nextMonday);
//        if ($interval->days <= 3) {
//            $nextMonday->modify('next monday');
//        }
//
//        // Gets last order for every request
//        foreach ($requests as $request) {
//            $lastOrder = $this->getLastOrderByRequest($request);
//            if ($lastOrder && $now <= $lastOrder->getStartDate() && $lastOrder->getStartDate() <= $nextMonday) {
//                $file = new TmpFile();
//                $document = new PdfGenerator($this->getEntityManager(), $lastOrder, $file);
//                $document->generate();
//
//                $headers = new Headers();
//                $headers->addHeaders(
//                    array(
//                        'Content-Disposition' => 'attachment; filename="' . $lastOrder->getName() . '.pdf"',
//                        'Content-Type'        => 'application/pdf',
//                    )
//                );
//                $this->getResponse()->setHeaders($headers);
//            }
//        }
//
//        return new ViewModel(
//            array(
//                'data' => $file->getContent(),
//            )
//        );
    }

    public function oldAction()
    {
        $requests = $this->getAllRequests();

        // Gets last order for every request
        $lastOrders = array();
        $now = new DateTime();
        foreach ($requests as $request) {
            $lastOrder = $this->getLastOrderByRequest($request);
            if ($lastOrder && $lastOrder->getEndDate() < $now) {
                $lastOrders[] = $lastOrder;
            }
        }

        return new ViewModel(
            array(
                'requests' => $lastOrders,
            )
        );
    }

    public function conflictingAction()
    {
        $requests = $this->getAllRequests();

        $now = new DateTime();
        // Gets last order for every request
        $lastOrders = array();
        foreach ($requests as $request) {
            $lastOrder = $this->getLastOrderByRequest($request);
            if ($lastOrder && $lastOrder->getEndDate() >= $now) {
                $lastOrders[] = $this->getLastOrderByRequest($request);
            }
        }

        $mappings = array();
        foreach ($lastOrders as $order) {
            $mappings = array_merge(
                $mappings,
                $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
                    ->findAllByOrderQuery($order)->getResult()
            );
        }

        $conflicts = array();
        // Loop over all made mappings
        foreach ($mappings as $map) {
            // Find overlaps
            $overlapping_maps = $this->findOverlapping($mappings, $map);
            // Delete map so it doesn't pop up twice
            $mappings = array_udiff(
                $mappings,
                $overlapping_maps,
                function ($obj_a, $obj_b) {
                    return $obj_a->getArticle()->getName() === $obj_b->getArticle()->getName();
                }
            );

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
                    'total'    => $total,
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
     * @return array
     */
    private function getAllRequests()
    {
        return array_merge($this->getHandledRequests(), $this->getUnhandledRequests());
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
        $margin_hours = @unserialize($this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('logistics.request_margin_hours'));
        $start = $mapping->getOrder()->getStartDate();
        $start->modify('-' . $margin_hours . ' hour');
        $end = $mapping->getOrder()->getEndDate();
        $end->modify('+' . $margin_hours . ' hour');

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
     * @param Order $order
     */
    private function generateCsvFile($order)
    {
        $file = new CsvFile();
        $heading = array('ORDER');

        $unitNameOrder = '';
        if ($order->getUnit()) {
            $unitNameOrder = $order->getUnit()->getName();
        }

        $results = array(
            array(
                'Naam',
                'Verantwoordelijke',
                'Post',
            ),
            array(
                $order->getName(),
                $order->getContact(),
                $unitNameOrder,
            ),
            array(
                'Van',
                'Tot',
                'Locatie',
            ),
            array(
                $order->getStartDate()->format('l d/m/Y H:i'),
                $order->getEndDate()->format('l d/m/Y H:i'),
                $order->getLocation()->getName(),
            ),
            array('',),
            array('ARTICLES'),
            array(
                'Naam',
                'Wie',
                'Categorie',
                'Locatie',
                'Plek',
                'Aantal',
                'Klaar',
                'Mee',
                'Terug',
                'Opmerkingen',
            ),
        );

        $articles = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findAllByOrderQuery($order)->getResult();
        foreach ($articles as $article) {
            $unitNameArticle = '';
            if ($article->getArticle()->getUnit()) {
                $unitNameArticle = $article->getArticle()->getUnit()->getName();
            }

            $results[] = array(
                $article->getArticle()->getName(),
                $unitNameArticle,
                $article->getArticle()->getCategory(),
                $article->getArticle()->getLocation(),
                $article->getArticle()->getSpot(),
                $article->getAmount(),
            );
        }

        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        if ($unitNameOrder) {
            $unitNameOrder = '_' . $unitNameOrder;
        }

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="Materiaal_' . $order->getStartDate()->format('Y-m-d') . $unitNameOrder . '_' . $order->getName() . '.csv"',
                'Content-Type'        => 'text/csv',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return $file->getContent();
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
