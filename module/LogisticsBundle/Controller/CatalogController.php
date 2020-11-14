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
use Doctrine\Common\Collections\ArrayCollection;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\Article;
use LogisticsBundle\Entity\Request;
use LogisticsBundle\Entity\Order;

/**
 * CatalogController
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 */
class CatalogController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function catalogAction()
    {
        $articleSearchForm = $this->getForm('logistics_catalog_search_article');
//        print_r($articleSearchForm->getElements());die();
        $query = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Article')
            ->findAllQuery(); //TODO: welke query moet hier?

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $articleSearchForm->setData($formData);

            if ($articleSearchForm->isValid()) {
                $formData = $articleSearchForm->getData();

                $repository = $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Article');

                $type = $formData['type'] == 'all' ? null : $formData['type'];
                $location = $formData['location'] == 'all' ? null : $formData['location'];

//                $query = $repository->findAllActiveByTypeAndLocationQuery($type, $location); //TODO: create
                $query = $repository->findAllQuery(); //TODO: make line above functional
            }
        }
        $paginator = $this->paginator()->createFromQuery(
            $query,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'catalogSearchForm' => $articleSearchForm,
            )
        );
    }

    public function ordersAction()
    {
        $academic = $this->getAcademicEntity();

        $orders = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order')
            ->findAllActiveByCreator($academic);
        return new ViewModel(
            array(
                'orders' => $orders,
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

                $this->getEntityManager()->persist($order);
                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'logistics_catalog',
                    array(
                        'action' => 'orders',
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

    public function basketAction()
    {
        $person = $this->getAcademicEntity();
        if ($person === null) {
            return new ViewModel();
        }

        $form = $this->getForm('logistics_catalog_order_basket');

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

                $mailAddress = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('logistics.order_mail');

                $mailName = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('logistics.order_mail_name');

                $link = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('logistics.order_link');

                $mail = new Message();
                $mail->setBody($link)
                    ->setFrom($mailAddress, $mailName)
                    ->addTo($mailAddress, $mailName)
                    ->setSubject('New Order Request ' . $person->getFullName());

                if (getenv('APPLICATION_ENV') != 'development') {
                    $this->getMailTransport()->send($mail);
                }

                $this->flashMessenger()->success(
                    'Success',
                    'The request has been sent to our administrators for approval.'
                );

                $this->redirect()->toRoute(
                    'logistics_catalog',
                    array(
                        'action' => 'orders',
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

    public function removeOrderAction()
    {
        $this->initAjax();

        $order = $this->getOrderEntity();
        if ($order === null) {
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
                    'action' => 'orders',
                )
            );

            return new ViewModel();
        }

        $order->remove();
        $articles = $order->getArticles();
        foreach ($articles as $article){
            $article->getArticle()->removeOrder(new ArrayCollection($article)); //TODO: Is dit correct???
        }
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function viewArticleAction()
    {
        $article = $this->getArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }


        return new ViewModel(
            array(
                'article'  => $article,
            )
        );
    }

    /**
     * @return Article|null
     */
    private function getArticleEntity()
    {
        $article = $this->getEntityById('LogisticsBundle\Entity\Article');

        if (!($article instanceof Article)) {
            $this->flashMessenger()->error(
                'Error',
                'No Article was found!'
            );

            $this->redirect()->toRoute(
                'logistics_catalog',
                array(
                    'action' => 'index',
                )
            );

            return;
        }

        return $article;
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
        $order = $this->getEntityById('LogisticsBundle\Entity\Order');

        if (!($order instanceof Order)) {
            $this->flashMessenger()->error(
                'Error',
                'No Order was found!'
            );

            $this->redirect()->toRoute(
                'logistics_catalog',
                array(
                    'action' => 'index',
                )
            );

            return;
        }

        return $order;
    }
}