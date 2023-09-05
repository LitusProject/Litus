<?php

namespace LogisticsBundle\Controller;

use CommonBundle\Entity\User\Person\Academic;
use Laminas\Mail\Headers;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\Article;
use LogisticsBundle\Entity\Order;
use LogisticsBundle\Entity\Order\OrderArticleMap as Map;
use LogisticsBundle\Entity\Request;

/**
 * CatalogController
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class CatalogController extends \LogisticsBundle\Component\Controller\LogisticsController
{
    public function overviewAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $requests = $this->getOpenRequestsByAcademic($academic);
        $unit = $academic->getUnit($this->getCurrentAcademicYear(true));

        if ($unit) {
            $unitRequests = $this->getOpenRequestsByUnit($unit);
            $requests = $this->mergeArraysUnique($requests, $unitRequests);
        }

        // Gets last order for every request
        $lastOrders = array();
        foreach ($requests as $request) {
            $lastOrders[] = $this->getLastOrderByRequest($request);
        }

        return new ViewModel(
            array(
                'lastOrders'    => $lastOrders,
            )
        );
    }

    public function catalogAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return $this->notFoundAction();
        }

        $order = $this->getOrderEntity();
        if ($order === null) {
            return $this->notFoundAction();
        }

        $request = $order->getRequest();

        // Check if authenticated to modify order articles
        if ($academic !== $order->getCreator()
            && (!$academic->isPraesidium($this->getCurrentAcademicYear())
                || $academic->getUnit($this->getCurrentAcademicYear()) !== $order->getUnit())
        ) {
            return $this->notFoundAction();
        }

        $mappings = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findAllByOrderQuery($order)->getResult();

        $mapped = array();
        foreach ($mappings as $map) {
            if (!isset($mapped[$map->getArticle()->getId()])) {
                $mapped[$map->getArticle()->getId()] = 0;
            }

            $mapped[$map->getArticle()->getId()] += $map->getAmount();
        }
        $allArticles = array();
        if ($academic->isPraesidium($this->getCurrentAcademicYear())) {
            $articles = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Article')
                ->findAllQuery()->getResult();
        } else {
            $articles = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Article')
                ->findAllByVisibilityQuery('external')->getResult();
        }

        foreach ($articles as $art) {
            $articleInfo = array(
                'article' => $art,
                'mapped'  => $mapped[$art->getId()] ?? 0,
            );

            $allArticles[] = $articleInfo;
        }

        $form = $this->getForm(
            'logistics_catalog_catalog_catalog',
            array(
                'articles' => $allArticles,
            )
        );
        $searchForm = $this->getForm('logistics_catalog_catalog_search');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $newOrder = $this->recreateOrder($order, $academic->getFullName());
                $this->getEntityManager()->persist($newOrder);

//                $order->overwrite();
//                $this->getEntityManager()->flush();

                $formData = $form->getData();
                $total = 0;
                foreach ($formData as $formKey => $formValue) {
                    $articleId = substr($formKey, 8, strlen($formKey));
                    if (substr($formKey, 0, 8) == 'article-' && $formValue != '' && $formValue != '0') {
                        $total += $formValue;

                        $article = $this->getEntityManager()
                            ->getRepository('LogisticsBundle\Entity\Article')
                            ->findOneById($articleId);

                        $oldAmount = $mapped[$articleId]?: 0;
                        $booking = new Map($newOrder, $article, $formValue, $oldAmount);

                        $this->getEntityManager()->persist($booking);
                    }
                }

                if ($total == 0) {
                    $this->flashMessenger()->warn(
                        'Warning',
                        'You have not booked any articles!'
                    );
                } else {
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'Success',
                        'The articles have been booked!'
                    );
                    $this->sendAlertMails($request);
                    $this->sendMailToLogi($request);
                }
                $this->redirect()->toRoute(
                    'logistics_catalog',
                    array(
                        'action' => 'view',
                        'order'  => $newOrder->getId()
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'isPraesidium'  => $academic->isPraesidium($this->getCurrentAcademicYear()),
                'articles'      => $allArticles,
                'categories'    => Article::$POSSIBLE_CATEGORIES,
                'units'         => $this->getAllActiveUnits($articles),
                'form'          => $form,
                'searchForm'    => $searchForm,
                'order'         => $order,
            )
        );
    }

    public function addOrderAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $form = $this->getForm('logistics_catalog_order_add', array('academic' => $academic, 'academicYear' => $this->getCurrentAcademicYear(true)));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $order = $form->hydrateObject(
                    new Order($academic, new Request($academic), $academic->getFullName())
                );
                $order->approve();
                $this->getEntityManager()->persist($order);
                $this->getEntityManager()->flush();

                $this->redirect()->toRoute('logistics_catalog', array('action' => 'catalog', 'order'  => $order->getId(),));
                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function editOrderAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        if ($academic !== $order->getCreator()
            && (!$academic->isPraesidium($this->getCurrentAcademicYear())
                || $academic->getUnit($this->getCurrentAcademicYear()) !== $order->getUnit())
        ) {
            return $this->notFoundAction();
        }

        $mappings = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findAllByOrderQuery($order)->getResult();

        if (isset($unfinishedRequestsOrders[$order->getId()])) {
            $this->flashMessenger()->error(
                'Error',
                'You cannot edit a Job that has an open request.'
            );

            $this->redirect()->toRoute(
                'logistics_catalog',
                array(
                    'action' => 'overview',
                )
            );
        }

        $form = $this->getForm('logistics_catalog_order_edit', array('academic' => $academic, 'academicYear' => $this->getCurrentAcademicYear(true), 'order' => $order));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $newOrder = $form->hydrateObject(
                    $this->recreateOrder($order, $academic->getFullName())
                );
                $newOrder->pending();
                $this->getEntityManager()->persist($newOrder);

                foreach ($mappings as $map) {
                    $booking = new Map($newOrder, $map->getArticle(), $map->getAmount());
                    $this->getEntityManager()->persist($booking);
                }

                $request = $newOrder->getRequest();
                $this->getEntityManager()->flush();

                $this->sendMailToLogi($request);

                $this->flashMessenger()->success(
                    'Success',
                    'The request has been sent to our administrators for approval.'
                );

                $this->redirect()->toRoute('logistics_catalog', array('action' => 'view', 'order'  => $newOrder->getId()));
                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'  => $form,
                'order' => $order,
            )
        );
    }

    public function viewAction()
    {
        $person = $this->getAcademicEntity();
        if ($person === null) {
            return new ViewModel();
        }

        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        if ($person !== $order->getCreator()
            &&(!$person->isPraesidium($this->getCurrentAcademicYear())
                ||$person->getUnit($this->getCurrentAcademicYear()) !== $order->getUnit())
        ) {
            return $this->notFoundAction();
        }

        $articles = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findAllByOrderQuery($order)->getResult();

        // Gets last order for every request
        $lastOrders = $this->getAllOrdersByRequest($order->getRequest());

        return new ViewModel(
            array(
                'order'         => $order,
                'articles'      => $articles,
                'lastOrders'    => $lastOrders,
            )
        );
    }

    public function cancelRequestAction()
    {
        $this->initAjax();

        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $order = $this->getOrderEntity();
        $request = $order->getRequest();
        if ($request === null) {
            return $this->notFoundAction();
        }

        if ($academic !== $order->getCreator()
            && (!$academic->isPraesidium($this->getCurrentAcademicYear())
                || $academic->getUnit($this->getCurrentAcademicYear()) !== $order->getUnit())
        ) {
            return $this->notFoundAction();
        }

        $request->cancel();
        $order->cancel();
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function removeRequestAction()
    {
        $this->initAjax();

        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $order = $this->getOrderEntity();
        if ($order === null) {
            return $this->notFoundAction();
        }

        $request = $order->getRequest();
        if ($request === null) {
            return $this->notFoundAction();
        }

        if ($academic !== $order->getCreator()
            && (!$academic->isPraesidium($this->getCurrentAcademicYear())
                || $academic->getUnit($this->getCurrentAcademicYear()) !== $order->getUnit())
        ) {
            return $this->notFoundAction();
        }

        $request->remove();
        $order->remove();
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return $this->notFoundAction();
        }

        $order = $this->getOrderEntity();
        if ($order === null) {
            return $this->notFoundAction();
        }

        if ($academic !== $order->getCreator()
            && (!$academic->isPraesidium($this->getCurrentAcademicYear())
                || $academic->getUnit($this->getCurrentAcademicYear()) !== $order->getUnit())
        ) {
            return $this->notFoundAction();
        }

        $numResults = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('logistics.catalog_search_max_results')
        );

        $articles = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Article')
            ->findAllByNameQuery($this->getParam('string'))
            ->setMaxResults($numResults)
            ->getResult();

        $mappings = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findAllByOrderQuery($order)->getResult();

        $mapped = array();
        foreach ($mappings as $map) {
            if (!isset($mapped[$map->getArticle()->getId()])) {
                $mapped[$map->getArticle()->getId()] = 0;
            }

            $mapped[$map->getArticle()->getId()] += $map->getAmount();
        }

        $result = array();

        foreach ($articles as $article) {
            if ($article->getStatus() == 'private') {
                continue;
            }

            $item = (object) array();
            $item->id = $article->getId();
            $item->title = $article->getName();
            $item->status = $article->getStatus();
            $item->category = $article->getCategory();
            $item->amt = $article->getAmountAvailable();
            $item->mapped = $mapped[$article->getId()] ?? 0;
            $item->additionalInfo = $article->getAdditionalInfo();

            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
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
        if (!($order instanceof Order)) {
            $this->flashMessenger()->error(
                'Error',
                'No Order was found!'
            );

            $this->redirect()->toRoute(
                'logistics_catalog',
                array(
                    'action' => 'overview',
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
        $request = $this->getEntityById('LogisticsBundle\Entity\Request', 'request');

        if (!($request instanceof Request)) {
            $this->flashMessenger()->error(
                'Error',
                'No Request was found!'
            );

            $this->redirect()->toRoute(
                'logistics_catalog',
                array(
                    'action' => 'overview',
                )
            );

            return;
        }

        return $request;
    }

    /**
     * @return array
     */
    private function getAllActiveUnits($articles)
    {
        $unitsArray = array();
        foreach ($articles as $article) {
            if ($article->getUnit()) {
                $unitsArray[] = $article->getUnit()->getName();
            }
        }
        $unitsArray = array_unique($unitsArray);

        if (count($unitsArray) == 0) {
            throw new RuntimeException('There needs to be at least one unit');
        }

        return $unitsArray;
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

    /**
     * @return array
     */
    private function getOpenRequestsByUnit($unit)
    {
        $activeOrders = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order')
            ->findAllActiveByUnit($unit);

        $requests = array();
        foreach ($activeOrders as $activeOrder) {
            $request = $activeOrder->getRequest();
            if (!($request->isRemoved()) and !(in_array($request, $requests, true))) {
                $requests[] = $activeOrder->getRequest();
            }
        }

        return $requests;
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getAllOrdersByRequest($request)                  // Gets all orders except oldest (dummy order)
    {
        $orders = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order')
            ->findAllByRequest($request);
        array_pop($orders);
        return $orders;
    }

    /**
     * @param Request $request
     * @return Order
     */
    private function getLastOrderByRequest($request)                  // Gets the most recent order
    {
        $order = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order')
            ->findAllByRequest($request);
        return current($order);                                       // Gets the first element of an array
    }

    /**
     * @param Request $request
     * @return Order
     */
    private function getFirstOrderByRequest($request)                // Gets the oldest order, by default an empty order
    {
        $order = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order')
            ->findAllByRequest($request);
        return end($order);                                    // Gets the last element of an array
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

    /**
     * @param Order $order
     * @return Order
     */
    private function recreateOrder(Order $order, string $updator)
    {
        $new = new Order($order->getContact(), $order->getRequest(), $updator);
        $new->setCreator($order->getCreator());
        $new->setLocation($order->getLocation());
        $new->setDescription($order->getDescription());
        $new->setEmail($order->getEmail());
        $new->setStartDate($order->getStartDate());
        $new->setEndDate($order->getEndDate());
        $new->setName($order->getName());
        $new->setUnit($order->getUnit());
        $new->pending();
        # In comment: should be fixed later on when adding van system
        # $new->setNeedsRide($order->needsRide());

        return $new;
    }

    /**
     * @param Request $request
     */
    private function sendMailToLogi(Request $request) // Mail for Logistiek
    {
        $order = $this->getLastOrderByRequest($request);

        $mailAddress = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('logistics.order_mail');

        $mailName = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('logistics.order_mail_name');

        $mailData = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('logistics.order_request')
        );

        $message = $mailData['content'];
        $subject = $mailData['subject'];

        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody(
                str_replace(
                    array('{{ name }}', '{{ type }}', '{{ person }}', '{{ end }}', '{{ start }}'),
                    array($order->getName(), $request->Handled(), $order->getCreator()->getFullName(), $order->getEndDate()->format('d/m/Y H:i'), $order->getStartDate()->format('d/m/Y H:i')),
                    $message
                )
            )
            ->setFrom($mailAddress, $mailName)
            ->addTo($mailAddress, $mailName)
            ->setSubject(str_replace('{{ name }}', $order->getName(), $subject));

        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }
    }

    /**
     * @param Request $request
     */
    private function sendAlertMails(Request $request) // Extra mails for specific items (ex. to Theokot)
    {
        $order = $this->getLastOrderByRequest($request);

        $mappings = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findAllByOrderQuery($order)->getResult();

        $mailAddress = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('logistics.order_mail');

        $mailName = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('logistics.order_mail_name');

        $mailData = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('logistics.order_alert_mail')
        );

        $reviewLink = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('logistics.order_link') . $order->getId();

        $message = $mailData['content'];
        $subject = $mailData['subject'];

        $alertMailArray = array();
        foreach ($mappings as $map) {
            $alertMail = $map->getArticle()->getAlertMail();
            if (!($alertMail == 'logistiek@vtk.be')) {
                if ($alertMailArray[$alertMail]) {
                    $alertMailArray[$alertMail][] = $map;
                } else {
                    $alertMailArray[$alertMail] = array($map);
                }
            }
        }

        foreach ($alertMailArray as $alertMail => $mappings) {
            if ($alertMail != Null && $alertMail !== '') {
                $articleBody = '';
                foreach ($mappings as $map) {
                    error_log($map->getArticle()->getName());
                    error_log($alertMail);
                    error_log('_');
                    $articleBody .= "\t* " . $map->getArticle()->getName() . ", aantal: " . $map->getAmount() . "\r\n";
                }

                error_log($articleBody);

                $headers = new Headers();
                $headers->addHeaders(
                    array(
                        'Content-Type' => 'text/plain',
                    )
                );

                $mail = new Message();
                $mail->setEncoding('UTF-8')->setHeaders($headers)
                    ->setBody(
                        str_replace(
                            array('{{ name }}', '{{ article }}', '{{link}}', '{{ person }}', '{{ end }}', '{{ start }}'),
                            array($order->getName(), $articleBody, $reviewLink, $order->getCreator()->getFullName(), $order->getEndDate()->format('d/m/Y H:i'), $order->getStartDate()->format('d/m/Y H:i')),
                            $message
                        )
                    )
                    ->setFrom($mailAddress, $mailName)
                    ->addTo($map->getArticle()->getAlertMail(), $map->getArticle()->getUnit()->getName())
                    ->setSubject(str_replace(array('{{ name }}',), array($order->getName(),), $subject));

                error_log($mail->getHeaders());

                if (getenv('APPLICATION_ENV') != 'development') {
                    $this->getMailTransport()->send($mail);
                }
            }
        }

//        foreach ($mappings as $map) {
//            $alertMail = $map->getArticle()->getAlertMail();
//            if ($alertMail != Null && $alertMail !== '') {
//                $mail = new Message();
//                $mail->setEncoding('UTF-8')
//                    ->setBody(
//                        str_replace(
//                            array('{{ name }}', '{{ article }}', '{{ person }}', '{{ end }}', '{{ start }}'),
//                            array($order->getName(), $map->getArticle()->getName(), $map->getAmount(), $order->getCreator()->getFullName(), $order->getEndDate()->format('d/m/Y H:i'), $order->getStartDate()->format('d/m/Y H:i')),
//                            $message
//                        )
//                    )
//                    ->setFrom($mailAddress, $mailName)
//                    ->addTo($map->getArticle()->getAlertMail(), $mailName)
//                    ->setSubject(str_replace(array('{{ name }}',), array($order->getName(),), $subject));
//
//
//                if (getenv('APPLICATION_ENV') != 'development') {
//                    $this->getMailTransport()->send($mail);
//                }
//            }
//        }
    }
}