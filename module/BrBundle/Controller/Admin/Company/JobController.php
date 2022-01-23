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

namespace BrBundle\Controller\Admin\Company;

use BrBundle\Entity\Company;
use BrBundle\Entity\Company\Job;
use Laminas\View\Model\ViewModel;

/**
 * JobController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class JobController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $company = $this->getCompanyEntity();
        if ($company === null) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromEntity(
            'BrBundle\Entity\Company\Job',
            $this->getParam('page'),
            array(
                'company' => $company,
            ),
            array(
                'type' => 'ASC',
            )
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'company'           => $company,
            )
        );
    }

    public function addAction()
    {
        $company = $this->getCompanyEntity();
        if ($company === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_company_job_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $job = $form->hydrateObject(
                    new Job($company, $formData['type'])
                );
                $job->approve();

                $this->getEntityManager()->persist($job);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The job was successfully created!'
                );

                $this->redirect()->toRoute(
                    'br_admin_company_job',
                    array(
                        'action' => 'manage',
                        'id'     => $company->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'company' => $company,
                'form'    => $form,
            )
        );
    }

    public function editAction()
    {
        $job = $this->getJobEntity();
        if ($job === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_company_job_edit', $job);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The job was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'br_admin_company_job',
                    array(
                        'action' => 'manage',
                        'id'     => $job->getCompany()->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'company' => $job->getCompany(),
                'form'    => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $job = $this->getJobEntity();
        if ($job === null) {
            return new ViewModel();
        }

        $job->remove();
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Company|null
     */
    private function getCompanyEntity()
    {
        $company = $this->getEntityById('BrBundle\Entity\Company');

        if (!($company instanceof Company)) {
            $this->flashMessenger()->error(
                'Error',
                'No company was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $company;
    }

    /**
     * @return Job|null
     */
    private function getJobEntity()
    {
        $job = $this->getEntityById('BrBundle\Entity\Company\Job');

        if (!($job instanceof Job)) {
            $this->flashMessenger()->error(
                'Error',
                'No job was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $job;
    }
}
