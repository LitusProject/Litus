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
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Controller\Career;

use BrBundle\Entity\Company,
    BrBundle\Form\Career\Search\vacancy as VacancySearchForm,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

/**
 * VacancyController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class VacancyController extends \BrBundle\Component\Controller\CareerController
{
    public function overviewAction()
    {
        $vacancySearchForm = new VacancySearchForm();

        $query = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findAllActiveByTypeQuery('vacancy');

        $formData = $this->getRequest()->getQuery();
        $vacancySearchForm->setData($formData);

        if ($vacancySearchForm->isValid()) {
            $formData = $vacancySearchForm->getFormData($formData);

            $repository = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Company\Job');

            if($formData['sector'] != 'all') {
                if($formData['searchType'] == 'alphabeticalByCompany')
                    $query = $repository->findAllActiveByTypeAndSectorQuery('vacancy', $formData['sector']);
                elseif($formData['searchType'] == 'alphabeticalByVacancy')
                    $query = $repository->findAllActiveByTypeAndSectorByJobNameQuery('vacancy', $formData['sector']);
                elseif($formData['searchType'] == 'mostRecent')
                    $query = $repository->findAllActiveByTypeAndSectorByDateQuery('vacancy', $formData['sector']);
            } else {
                if($formData['searchType'] == 'alphabeticalByVacancy')
                    $query = $repository->findAllActiveByTypeByJobNameQuery('vacancy');
                elseif($formData['searchType'] == 'mostRecent')
                    $query = $repository->findAllActiveByTypeByDateQuery('vacancy');
            }
        }

        $paginator = $this->paginator()->createFromQuery(
            $query,
            $this->getParam('page')
        );

        $logoPath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'logoPath' => $logoPath,
                'vacancySearchForm' => $vacancySearchForm,
            )
        );
    }

    public function viewAction()
    {
        $vacancy = $this->_getVacancy();

        $logoPath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        return new ViewModel(
            array(
                'vacancy' => $vacancy,
                'logoPath' => $logoPath,
            )
        );
    }

    private function _getVacancy()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the vacancy!'
                )
            );

            $this->redirect()->toRoute(
                'br_career_vacancy',
                array(
                    'action' => 'overview'
                )
            );

            return;
        }

        $vacancy = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findOneActiveByTypeAndId('vacancy', $this->getParam('id'));

        if (null === $vacancy) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No vacancy with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'br_career_vacancy',
                array(
                    'action' => 'overview'
                )
            );

            return;
        }

        return $vacancy;
    }

    private function _getSectors()
    {
        $sectorArray = array();
        foreach (Company::$possibleSectors as $key => $sector)
            $sectorArray[$key] = $sector;

        return $sectorArray;
    }
}
