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
}
