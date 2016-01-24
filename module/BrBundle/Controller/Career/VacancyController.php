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
    BrBundle\Entity\Company\Job,
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
        $vacancySearchForm = $this->getForm('br_career_search_vacancy');

        $query = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findAllActiveByTypeByDateQuery('vacancy');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $vacancySearchForm->setData($formData);

            if ($vacancySearchForm->isValid()) {
                $formData = $vacancySearchForm->getData();

                $repository = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company\Job');

                if ('all' != $formData['sector']) {
                    if ('company' == $formData['searchType']) {
                        $query = $repository->findAllActiveByTypeAndSectorQuery('vacancy', $formData['sector']);
                    } elseif ('vacancy' == $formData['searchType']) {
                        $query = $repository->findAllActiveByTypeAndSectorByJobNameQuery('vacancy', $formData['sector']);
                    } elseif ('mostRecent' == $formData['searchType']) {
                        $query = $repository->findAllActiveByTypeAndSectorByDateQuery('vacancy', $formData['sector']);
                    }
                } else {
                    if ('vacancy' == $formData['searchType']) {
                        $query = $repository->findAllActiveByTypeByJobNameQuery('vacancy');
                    } elseif ('mostRecent' == $formData['searchType']) {
                        $query = $repository->findAllActiveByTypeByDateQuery('vacancy');
                    }
                }
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
        if (!($vacancy = $this->getVacancyEntity())) {
            return new ViewModel();
        }

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

    /**
     * @return Job|null
     */
    private function getVacancyEntity()
    {
        $job = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findOneActiveByTypeAndId('vacancy', $this->getParam('id', 0));

        if (!($job instanceof Job)) {
            $this->flashMessenger()->error(
                'Error',
                'No job was found!'
            );

            $this->redirect()->toRoute(
                'br_career_vacancy',
                array(
                    'action' => 'overview',
                )
            );

            return;
        }

        return $job;
    }
}
