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

namespace LogisticsBundle\Controller;

use CommonBundle\Entity\User\Person\Academic;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\Article;
use LogisticsBundle\Entity\Order;
use LogisticsBundle\Entity\Order\OrderArticleMap as Map;
use LogisticsBundle\Entity\Request;

/**
 * CatalogController
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 */
class CatalogController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function overviewAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $orders = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order')
            ->findAllActiveByCreator($academic);

        $requests = $this->getOpenRequests($academic);
        $unit = $academic->getUnit($this->getCurrentAcademicYear(true));

        if ($unit) {
            $unitOrders = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Order')
                ->findAllActiveByUnit($unit);
            $unitRequests = $this->getOpenRequestsByUnit($unit);
            $orders = $this->mergeArraysUnique($orders, $unitOrders);
            $requests = $this->mergeArraysUnique($requests, $unitRequests);
        }
        return new ViewModel(
            array(
                'orders' => $orders,
                'requests' => $requests,
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

        if ($academic !== $order->getCreator()
            &&(!$academic->getOrganizationStatus($this->getCurrentAcademicYear())
            ||$academic->getUnit($this->getCurrentAcademicYear()) !== $order->getUnit())
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
        $articles = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Article')
            ->findAllByVisibilityQuery('internal')->getResult();

        foreach ($articles as $art) {
            $articleInfo = array(
                'article'   => $art,
                'mapped'    => $mapped[$art->getId()] ?? 0,
            );

            $allArticles[] = $articleInfo;
        }

        $form = $this->getForm(
            'logistics_catalog_catalog_catalog',
            array(
                'articles' => $allArticles,
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $newOrder = $this->recreateOrder($order);
                $newOrder->pending();
                $this->getEntityManager()->persist($newOrder);

                $formData = $form->getData();
                $total = 0;
                foreach ($formData as $formKey => $formValue) {
                    $articleId = substr($formKey, 8, strlen($formKey));
                    if (substr($formKey, 0, 8) == 'article-' && $formValue != '' && $formValue != '0') {
                        $total += $formValue;

                        $article = $this->getEntityManager()
                            ->getRepository('LogisticsBundle\Entity\Article')
                            ->findOneById($articleId);

                        // TODO: hier checken of amount niet te hoog is?!

                        $booking = new Map($newOrder, $article, $formValue);

                        $this->getEntityManager()->persist($booking);
                    }
                }

                $req = new Request($academic, $order, 'edit', $newOrder);
                $this->getEntityManager()->persist($req);

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
                    $this->sendMailToLogi($req);
                }
                $this->redirect()->toRoute(
                    'logistics_catalog',
                    array(
                        'action' => 'view',
                        'order' => $newOrder->getId()
                    )
                );

                return new ViewModel();
            }
        }

        $searchForm = $this->getForm('logistics_catalog_catalog_search');

        return new ViewModel(
            array(
                'articles' => $allArticles,
                'categories'    => Article::$POSSIBLE_CATEGORIES,
                'form'              => $form,
                'searchForm'        => $searchForm,
                'order'             => $order,
            )
        );
    }

    public function addOrderAction()
    {
        $person = $this->getAcademicEntity();
        if ($person === null) {
            return new ViewModel();
        }

        $form = $this->getForm('logistics_catalog_order_add', array('academic' => $person, 'academicYear' => $this->getCurrentAcademicYear(true)));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $order = $form->hydrateObject(
                    new Order($person)
                );
                $order->pending();
                $this->getEntityManager()->persist($order);
                $request = new Request($person, $order, 'add');
                $this->getEntityManager()->persist($request);
                $this->getEntityManager()->flush();

                $this->sendMailToLogi($request);

                $this->flashMessenger()->success(
                    'Success',
                    'The request has been sent to our administrators for approval.'
                );

                $this->redirect()->toRoute(
                    'logistics_catalog',
                    array(
                        'action' => 'overview',
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

    public function editOrderAction()
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
            &&(!$person->getOrganizationStatus($this->getCurrentAcademicYear())
            ||$person->getUnit($this->getCurrentAcademicYear()) !== $order->getUnit())
        ) {
            return $this->notFoundAction();
        }

        $requests = $this->getOpenRequests($person);

        $mappings = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findAllByOrderQuery($order)->getResult();

        $unfinishedRequestsOrders = array();
        foreach ($requests as $request) {
            if ($request->getRequestType() == 'edit reject' || $request->getRequestType() == 'edit') {
                $unfinishedRequestsOrders[$request->getEditOrder()->getId()] = $request->getRequestType();
            } elseif ($request->getRequestType() == 'delete') {
                $unfinishedRequestsOrders[$request->getOrder()->getId()] = 'delete';
            }
        }

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

        $form = $this->getForm('logistics_catalog_order_edit', array('academic' => $person, 'academicYear' => $this->getCurrentAcademicYear(true), 'order' => $order));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $newOrder = $form->hydrateObject(new Order($person));
                $newOrder->pending();
                foreach ($mappings as $map) {
                    $booking = new Map($newOrder, $map->getArticle(), $map->getAmount());
                        $this->getEntityManager()->persist($booking);
                }
                $this->getEntityManager()->persist($newOrder);
                $request = new Request($person, $order, 'edit', $newOrder);
                $this->getEntityManager()->persist($request);
                $this->getEntityManager()->flush();

                $this->sendMailToLogi($request);

                $this->flashMessenger()->success(
                    'Success',
                    'The request has been sent to our administrators for approval.'
                );

                $this->redirect()->toRoute(
                    'logistics_catalog',
                    array(
                        'action' => 'overview',
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
            &&(!$person->getOrganizationStatus($this->getCurrentAcademicYear())
            ||$person->getUnit($this->getCurrentAcademicYear()) !== $order->getUnit())
        ) {
            return $this->notFoundAction();
        }

        $articles = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findAllByOrderQuery($order)->getResult();

        return new ViewModel(
            array(
                'order'   => $order,
                'articles' => $articles,
            )
        );
    }

    public function removeOrderAction()
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
            &&(!$academic->getOrganizationStatus($this->getCurrentAcademicYear())
            ||$academic->getUnit($this->getCurrentAcademicYear()) !== $order->getUnit())
        ) {
            return $this->notFoundAction();
        }

        if (!$order->isCancellable()) {
            $this->flashMessenger()->error(
                'Error',
                'The given order cannot be removed!'
            );

            $this->redirect()->toRoute(
                'logistics_catalog',
                array(
                    'action' => 'overview',
                )
            );

            return new ViewModel();
        }

        $order->remove();

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function editRequestAction()
    {
        $person = $this->getAcademicEntity();
        if ($person === null) {
            return new ViewModel();
        }

        $request = $this->getRequestEntity();
        if ($request === null) {
            return new ViewModel();
        }

        if ($person !== $request->getContact()) {
            return $this->notFoundAction();
        }

        if (!$request->getEditOrder()) {
            $this->flashMessenger()->error(
                'Error',
                'The given request cannot be edited! Edit the order instead!'
            );
            $this->redirect()->toRoute(
                'logistics_catalog',
                array(
                    'action' => 'overview',
                )
            );
        }

        $form = $this->getForm('logistics_catalog_order_edit', array('academic' => $person, 'academicYear' => $this->getCurrentAcademicYear(true), 'order' => $request->getEditOrder()));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $newOrder = $form->hydrateObject(new Order($person));
                $newOrder->pending();
                $this->getEntityManager()->persist($newOrder);
                $newRequest = new Request($person, $request->getOrder(), 'edit', $newOrder);
                $this->getEntityManager()->persist($newRequest);
                $request->rejectRequest('Overwritten by an edit at ' . $newRequest->getCreationTime()->format('d/m/Y H:m'));
                $request->handled();
                $this->getEntityManager()->flush();

                $this->sendMailToLogi($newRequest);
                $this->flashMessenger()->success(
                    'Success',
                    'The request has been sent to our administrators for approval.'
                );

                $this->redirect()->toRoute(
                    'logistics_catalog',
                    array(
                        'action' => 'overview',
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

    public function removeRequestAction()
    {
        $this->initAjax();

        $person = $this->getAcademicEntity();
        if ($person === null) {
            return new ViewModel();
        }

        $request = $this->getRequestEntity();
        if ($request === null) {
            return $this->notFoundAction();
        }

        if ($person !== $request->getContact()) {
            return $this->notFoundAction();
        }

        if ($request->getStatus() !== 'pending' && $request->getStatus() !== 'rejected') {
            $this->flashMessenger()->error(
                'Error',
                'The given request cannot be removed!'
            );

            $this->redirect()->toRoute(
                'logistics_catalog',
                array(
                    'action' => 'overview',
                )
            );

            return new ViewModel();
        }
        $request->setRemoved(true);
        if ($request->getRequestType() === 'add') {
            $request->getOrder()->remove();
        } else {
            $request->getEditOrder()->remove();
        }

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
            &&(!$academic->getOrganizationStatus($this->getCurrentAcademicYear())
            ||$academic->getUnit($this->getCurrentAcademicYear()) !== $order->getUnit())
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
            $item->mapped = $mapped[$article->getId()];
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
    private function getOpenRequests(Academic $academic)
    {
        $unhandledRequests = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Request')
            ->findAllUnhandledByAcademic($academic);

        $handledRejects = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Request')
            ->findActiveRejectsByAcademic($academic);

        return array_merge($handledRejects, $unhandledRequests);
    }

    /**
     * @return array
     */
    private function getOpenRequestsByUnit($unit)
    {
        $unhandledRequests = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Request')
            ->findAllUnhandledByUnit($unit);

        $handledRejects = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Request')
            ->findActiveRejectsByUnit($unit);

        return array_merge($handledRejects, $unhandledRequests);
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
    private function recreateOrder(Order $order)
    {
        $new = new Order($order->getContact());
        $new->setCreator($order->getCreator());
        $new->setLocation($order->getLocation());
        $new->setDescription($order->getDescription());
        $new->setEmail($order->getEmail());
        $new->setEndDate($order->getEndDate());
        $new->setName($order->getName());
        $new->setStartDate($order->getStartDate());
        $new->setUnit($order->getUnit());

        return $new;
    }

    /**
     * @param Request $request
     */
    private function sendMailToLogi(Request $request)
    {
        $order = $request->getEditOrder() ?? $request->getOrder();
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

        $message = $mailData['nl']['content'];
        $subject = $mailData['nl']['subject'];

        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody(
                str_replace(
                    array('{{ name }}', '{{ type }}', '{{ person }}', '{{ end }}', '{{ start }}'),
                    array($order->getName(), $request->getRequestType(), $order->getCreator()->getFullName(), $order->getEndDate()->format('d/m/Y H:m'), $order->getStartDate()->format('d/m/Y H:m')),
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
}
