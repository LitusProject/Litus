<?php

namespace CudiBundle\Controller\Admin\Sale\Session;

use CudiBundle\Entity\Sale\Session\OpeningHour;
use Laminas\View\Model\ViewModel;

/**
 * OpeningHourController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class OpeningHourController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour')
                ->findAllActiveQuery(),
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
                ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour')
                ->findAllOldQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('cudi_sale_session_opening-hour_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The opening hour was successfully added!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_session_openinghour',
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

    public function editAction()
    {
        $openingHour = $this->getOpeningHourEntity();
        if ($openingHour === null) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_sale_session_opening-hour_edit', $openingHour);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The opening hour was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_session_openinghour',
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

        $openingHour = $this->getOpeningHourEntity();
        if ($openingHour === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($openingHour);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return OpeningHour|null
     */
    private function getOpeningHourEntity()
    {
        $openingHour = $this->getEntityById('CudiBundle\Entity\Sale\Session\OpeningHour');

        if (!($openingHour instanceof OpeningHour)) {
            $this->flashMessenger()->error(
                'Error',
                'No opening hour was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_session_openinghour',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $openingHour;
    }
}
