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

namespace BrBundle\Controller\Corporate;

use BrBundle\Entity\Company,
    BrBundle\Entity\Company\Job,
    BrBundle\Entity\Company\Request\RequestVacancy,
    BrBundle\Entity\User\Person\Corporate,
    Zend\View\Model\ViewModel;

/**
 * VacancyController
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Incalza Dario <dario.incalza@litus.cc>
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class VacancyController extends \BrBundle\Component\Controller\CorporateController
{
    public function overviewAction()
    {
        if (!($person = $this->getCorporateEntity())) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Company\Job')
                ->findAllActiveByCompanyAndTypeQuery($person->getCompany(), 'vacancy'),
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
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('br_corporate_job_add');

        if (!($person = $this->getCorporateEntity())) {
            return new ViewModel();
        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $job = $form->hydrateObject(
                    new Job($person->getCompany(), 'vacancy')
                );

                $job->pending();

                $this->getEntityManager()->persist($job);

                $request = new RequestVacancy($job, 'add', $person);

                $this->getEntityManager()->persist($request);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The request has been sent to our administrators for approval.'
                );

                $this->redirect()->toRoute(
                    'br_corporate_vacancy',
                    array(
                        'action' => 'overview',
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
        if (!($oldJob = $this->getVacancyEntity())) {
            return new ViewModel();
        }

        if (!($person = $this->getCorporateEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('br_corporate_job_edit', array('job' => $oldJob));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $job = $form->hydrateObject(
                    new Job($person->getCompany(), 'vacancy')
                );

                $job->pending();

                $this->getEntityManager()->persist($job);

                $request = new RequestVacancy($job, 'edit', $person, $oldJob);

                $this->getEntityManager()->persist($request);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The request has been sent to our administrators for approval.'
                );

                $this->redirect()->toRoute(
                    'br_corporate_vacancy',
                    array(
                        'action' => 'overview',
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
        if (!($vacancy = $this->getVacancyEntity())) {
            return new ViewModel();
        }

        if (!($person = $this->getCorporateEntity())) {
            return new ViewModel();
        }

        $request = new RequestVacancy($vacancy, 'delete', $person);

        $this->getEntityManager()->persist($request);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
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
                'br_career_internship',
                array(
                    'action' => 'overview',
                )
            );

            return;
        }

        return $job;
    }

    /**
     * @return array
     */
    private function getSectors()
    {
        $sectorArray = array();
        foreach (Company::$possibleSectors as $key => $sector) {
            $sectorArray[$key] = $sector;
        }

        return $sectorArray;
    }

    /**
     * @return Corporate|null
     */
    private function getCorporateEntity()
    {
        $person = $this->getAuthentication()->getPersonObject();

        if ($person === null || !($person instanceof Corporate)) {
            $this->flashMessenger()->error(
                'Error',
                'Please login to view the CV book.'
            );

            $this->redirect()->toRoute(
                'br_corporate_index',
                array(
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        return $person;
    }
}
