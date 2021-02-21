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

namespace LogisticsBundle\Controller\Admin;

use Laminas\Mail\Message;
use LogisticsBundle\Entity\Request;
use Laminas\View\Model\ViewModel;

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

        $oldMaps = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findAllByOrderQuery($oldOrder)->getResult();
        $newMaps = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findAllByOrderQuery($newOrder)->getResult();

        $mappings = array();

        foreach ($oldMaps as $map){
            $id = $map->getArticle()->getId();
            $mappings[$id] = array(
                'name'  =>  $map->getArticle()->getName(),
                'old'   =>  $map->getAmount(),
                'new'   =>  0,
            );
        }
        foreach ($newMaps as $map){
            $id = $map->getArticle()->getId();
            if (array_key_exists($id, $mappings)){
                $mappings[$id]['new'] = $map->getAmount();
            }
            else{
                $mappings[$id] = array(
                    'name'  =>  $map->getArticle()->getName(),
                    'new'   =>  $map->getAmount(),
                    'old'   =>  0,
                );
            }
        }

        // Dit is een heeeel vies stukje code, I know
        $diffs = array();

        if ($newOrder->getName()!==$oldOrder->getName())
            {$diffs['Name'] = array($oldOrder->getName(), $newOrder->getName());}

        if ($newOrder->getLocation()!==$oldOrder->getLocation())
            {$diffs['Location'] = array($oldOrder->getLocation()->getName(), $newOrder->getLocation()->getName());}

        if ($newOrder->getCreator()!==$oldOrder->getCreator())
            {$diffs['Creator'] = array($oldOrder->getCreator()->getFullName(), $newOrder->getCreator()->getFullName());}

        if ($newOrder->getContact()!==$oldOrder->getContact())
            {$diffs['Contact'] = array($oldOrder->getContact(), $newOrder->getContact());}

        if ($newOrder->getUnit()!==$oldOrder->getUnit())
            {$diffs['Unit'] = array($oldOrder->getUnit()->getName(), $newOrder->getUnit()->getName());}

        if ($newOrder->getStartDate()->format('d/m/Y H:i')!==$oldOrder->getStartDate()->format('d/m/Y H:i'))
            {$diffs['Start Date'] = array($oldOrder->getStartDate()->format('d/m/Y H:i'), $newOrder->getStartDate()->format('d/m/Y H:i'));}

        if ($newOrder->getEndDate()->format('d/m/Y H:i')!==$oldOrder->getEndDate()->format('d/m/Y H:i'))
            {$diffs['End Date'] = array($oldOrder->getEndDate()->format('d/m/Y H:i'), $newOrder->getEndDate()->format('d/m/Y H:i'));}


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

                $this->sendMailToContact($request);
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
    private function sendMailToContact(Request $request)
    {
        $order = $request->getEditOrder()??$request->getOrder();
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

        $mailData = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('logistics.order_request_mail')
        );

        $message = $mailData[$language->getAbbrev()]['content'];
        $subject = $mailData[$language->getAbbrev()]['subject'];

        $mail = new Message();
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

        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }
    }
}
