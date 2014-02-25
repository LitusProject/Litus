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
    BrBundle\Form\Career\Search\Sector as SectorSearchForm,
    BrBundle\Form\Career\Search\SearchType as SearchTypeForm,
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
        $sectorSearchForm = new SectorSearchForm();
        $searchTypeForm = new SearchTypeForm();

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findAllActiveByTypeQuery('vacancy'),
            $this->getParam('page')
        );

        $searchResults = null;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if (isset($formData['sector'])) {
                $sectorSearchForm->setData($formData);

                if ($sectorSearchForm->isValid() && '' != $formData['sector']) {
                    $formData = $sectorSearchForm->getFormData($formData);

                    if($formData['sector'] != "All")
                        $searchResults = $this->getEntityManager()
                            ->getRepository('BrBundle\Entity\Company\Job')
                            ->findAllActiveByTypeAndSector('vacancy',$formData['sector']);
                    else
                        $searchResults = $paginator;

                } else {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'Error',
                            'The given search query was invalid!'
                        )
                    );
                }
            }
        }

        $logoPath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'logoPath' => $logoPath,
                'sectorSearchForm' => $sectorSearchForm,
                'searchTypeForm' => $searchTypeForm,
                'searchResults' => $searchResults,
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
