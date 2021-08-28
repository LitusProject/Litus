<?php

namespace LogisticsBundle\Controller\Admin;

use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\Request;

/**
 * RequestController
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 */
class RequestController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $requests = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Request')
            ->findNewRequests();

        return new ViewModel(
            array(
                'requests'    => $requests,
            )
        );
    }

    public function viewAction()
    {
        $request = $this->getRequestEntity();
        if ($request === null) {
            return new ViewModel();
        }

        $newOrder = $request->getEditOrder();
        $oldOrder = $request->getOrder();

        $mappings = array();

        if ($newOrder === null) {
            $newMaps = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
                ->findAllByOrderQuery($oldOrder)->getResult();
        } else {
            $oldMaps = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
                ->findAllByOrderQuery($oldOrder)->getResult();
            $newMaps = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
                ->findAllByOrderQuery($newOrder)->getResult();

            foreach ($oldMaps as $map) {
                $id = $map->getArticle()->getId();
                $mappings[$id] = array(
                    'name'  => $map->getArticle()->getName(),
                    'old'   => $map->getAmount(),
                    'new'   => 0,
                );
            }
        }

        foreach ($newMaps as $map) {
            $id = $map->getArticle()->getId();
            if (array_key_exists($id, $mappings)) {
                $mappings[$id]['new'] = $map->getAmount();
            } else {
                $mappings[$id] = array(
                    'name'  => $map->getArticle()->getName(),
                    'new'   => $map->getAmount(),
                    'old'   => 0,
                );
            }
        }

        $diffs = array(
            'Name' => array($oldOrder->getName()),
            'Location' => array($oldOrder->getLocation()->getName()),
            'Creator' => array($oldOrder->getCreator()->getFullName()),
            'Contact' => array($oldOrder->getContact()),
            'Start Date' => array($oldOrder->getStartDate()->format('d/m/Y H:i')),
            'End Date' => array($oldOrder->getEndDate()->format('d/m/Y H:i')),
            'Description' => array($oldOrder->getDescription()),
        );
        if ($newOrder !== null) {
            $diffs['Name'][] = $newOrder->getName();
            $diffs['Location'][] = $newOrder->getLocation()->getName();
            $diffs['Creator'][] = $newOrder->getCreator()->getFullName();
            $diffs['Contact'][] = $newOrder->getContact();
            $diffs['Start Date'][] = $newOrder->getStartDate()->format('d/m/Y H:i');
            $diffs['End Date'][] = $newOrder->getEndDate()->format('d/m/Y H:i');
            $diffs['Description'][] = $newOrder->getDescription();
        }

        return new ViewModel(
            array(
                'request'    => $request,
                'newOrder'   => $newOrder,
                'oldOrder'   => $oldOrder,
                'diffs'      => $diffs,
                'mappings'   => $mappings,
            )
        );
    }

    public function approveAction()
    {
        $request = $this->getRequestEntity();
        if ($request === null) {
            return new ViewModel();
        }

        $request->approveRequest();
        $request->handled();
        $request->setRemoved(true);

        $this->getEntityManager()->flush();

        $this->sendMailToContact($request);
        $this->flashMessenger()->success(
            'Success',
            'The request was succesfully approved.'
        );

        $this->redirect()->toRoute(
            'logistics_admin_request',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

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
     * @param Request $request
     */
    private function sendMailToContact(Request $request, $rejected = false)
    {
        $order = $request->getEditOrder() ?? $request->getOrder();
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
