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

namespace BrBundle\Controller\Admin\Company;

use BrBundle\Entity\Company\Job,
    Zend\View\Model\ViewModel;

/**
 * JobController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class JobController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($company = $this->_getCompany())) {
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
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'company' => $company,
            )
        );
    }

    public function addAction()
    {
        if (!($company = $this->_getCompany())) {
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
                        'id' => $company->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'company' => $company,
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        if (!($job = $this->_getJob())) {
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
                        'id' => $job->getCompany()->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'company' => $job->getCompany(),
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($job = $this->_getJob())) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($job);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return \BrBundle\Entity\Company
     */
    private function _getCompany()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the company!'
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findOneById($this->getParam('id'));

        if (null === $company) {
            $this->flashMessenger()->error(
                'Error',
                'No company with the given ID was found!'
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
     * @return Job
     */
    private function _getJob()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the job!'
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $job = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findOneById($this->getParam('id'));

        if (null === $job) {
            $this->flashMessenger()->error(
                'Error',
                'No job with the given ID was found!'
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
