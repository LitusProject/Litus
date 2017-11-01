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

namespace CudiBundle\Controller\Admin;

use Cudibundle\Entity\IsicCard,
    Zend\View\Model\ViewModel;

/**
 * IsicController
 */
class IsicController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $cards = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\IsicCard')
            ->findAllQuery();

        $paginator = $this->paginator()->createFromQuery(
            $cards,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($isicCard = $this->getIsicCardEntity())) {
            return new ViewModel();
        }

        if ($isicCard->getBooking()->getStatus() == 'assigned') {
            $isicCard->getBooking()->getArticle()->addStockValue(-1);
        }

        $this->getEntityManager()->remove($isicCard->getBooking());
        $this->getEntityManager()->remove($isicCard);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function assignAction()
    {
        $this->initAjax();

        if (!($isicCard = $this->getIsicCardEntity())) {
            return new ViewModel();
        }

        if ($isicCard->getBooking()->getStatus() !== 'booked') {
            return new ViewModel();
        }

        $isicCard->getBooking()->setStatus('assigned', $this->getEntityManager());
        $isicCard->getBooking()->getArticle()->addStockValue(1);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function unassignAction()
    {
        $this->initAjax();

        if (!($isicCard = $this->getIsicCardEntity())) {
            return new ViewModel();
        }

        if ($isicCard->getBooking()->getStatus() !== 'assigned') {
            return new ViewModel();
        }

        $isicCard->getBooking()->setStatus('booked', $this->getEntityManager());
        $isicCard->getBooking()->getArticle()->addStockValue(-1);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    private function getIsicCardEntity()
    {
        $item = $this->getEntityById('CudiBundle\Entity\IsicCard');

        if (!($item instanceof IsicCard)) {
            $this->flashMessenger()->error(
                'Error',
                'No ISIC Card was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_isic',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $item;
    }
}
