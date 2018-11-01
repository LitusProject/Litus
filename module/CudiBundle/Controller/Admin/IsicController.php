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

use CommonBundle\Entity\General\AcademicYear;
use CudiBundle\Entity\IsicCard;
use Zend\Soap\Client as SoapClient;
use Zend\View\Model\ViewModel;

/**
 * IsicController
 */
class IsicController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $cards = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\IsicCard')
            ->findByYearQuery($academicYear);

        $paginator = $this->paginator()->createFromQuery(
            $cards,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $isicCard = $this->getIsicCardEntity();
        if ($isicCard === null) {
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

        $isicCard = $this->getIsicCardEntity();
        if ($isicCard === null) {
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

    public function printAction()
    {
        $this->initAjax();

        $isicCard = $this->getIsicCardEntity();
        if ($isicCard === null) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        if ($isicCard->getBooking()->getStatus() !== 'sold') {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        if (!$isicCard->hasPaid()) {
            $serviceUrl = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.isic_service_url');
            $client = new SoapClient($serviceUrl);
            $config = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config');

            $arguments = array();
            $arguments['username'] = $config->getConfigValue('cudi.isic_username');
            $arguments['password'] = $config->getConfigValue('cudi.isic_password');
            $arguments['userID'] = $isicCard->getCardNumber();

            $client->hasPaid($arguments);
            $isicCard->setPaid(true);

            $this->getEntityManager()->flush();
        } else {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function unassignAction()
    {
        $this->initAjax();

        $isicCard = $this->getIsicCardEntity();
        if ($isicCard === null) {
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

    public function searchAction()
    {
        $this->initAjax();

        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $cards = $this->search($academicYear)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($cards as $card) {
            $item = (object) array();
            $item->id = $card->getId();
            $item->number = $card->getCardNumber();
            $item->person = $card->getPerson()->getFullName();
            $item->status = $card->getBooking()->getStatus();
            $item->year = $card->getAcademicYear()->getStartDate()->format('Y') . ' - ' . $card->getAcademicYear()->getEndDate()->format('Y');
            $item->isPaid = $card->hasPaid();
            $item->booking = $card->getBooking()->getId();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @param  AcademicYear $academicYear
     * @return \Doctrine\ORM\Query|null
     */
    private function search(AcademicYear $academicYear)
    {
        switch ($this->getParam('field')) {
            case 'owner':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\IsicCard')
                    ->findByPersonNameAndYearQuery($this->getParam('string'), $academicYear);
        }
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
