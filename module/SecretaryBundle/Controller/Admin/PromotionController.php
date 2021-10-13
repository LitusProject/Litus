<?php

namespace SecretaryBundle\Controller\Admin;

use CommonBundle\Component\Util\AcademicYear as AcademicYearUtil;
use CommonBundle\Entity\General\AcademicYear;
use Laminas\View\Model\ViewModel;
use SecretaryBundle\Entity\Promotion;
use SecretaryBundle\Entity\Promotion\Academic;
use SecretaryBundle\Entity\Promotion\External;

/**
 * PromotionController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PromotionController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $paginator = null;
        if ($this->getParam('field') !== null) {
            $paginator = $this->paginator()->createFromArray(
                $this->search($academicYear),
                $this->getParam('page')
            );
        }

        if ($paginator === null) {
            $paginator = $this->paginator()->createFromEntity(
                'SecretaryBundle\Entity\Promotion',
                $this->getParam('page'),
                array(
                    'academicYear' => $academicYear,
                ),
                array()
            );
        }

        return new ViewModel(
            array(
                'academicYears'      => $academicYears,
                'activeAcademicYear' => $academicYear,
                'paginator'          => $paginator,
                'paginationControl'  => $this->paginator()->createControl(true),
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

        $promotions = $this->search($academicYear);

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($promotions, $numResults);

        $result = array();
        foreach ($promotions as $promotion) {
            $item = (object) array();
            $item->id = $promotion->getId();
            $item->fullName = $promotion->getFullName();
            $item->universityIdentification = $promotion instanceof Academic ? $promotion->getAcademic()->getUniversityIdentification() : '';
            $item->email = $promotion->getEmailAddress();

            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function addAction()
    {
        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $form = $this->getForm('secretary_promotion_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                if ($formData['academic_add']) {
                    $formData = $formData['academic'];

                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneById($formData['academic']['id']);

                    $promotion = $this->getEntityManager()
                        ->getRepository('SecretaryBundle\Entity\Promotion\Academic')
                        ->findOneByAcademicAndAcademicYear($academic, $academicYear);

                    if ($promotion) {
                        $this->flashMessenger()->warn(
                            'WARNING',
                            'The academic is already in this promotion list!'
                        );

                        $this->redirect()->toRoute(
                            'secretary_admin_promotion',
                            array(
                                'action'       => 'manage',
                                'academicyear' => $academicYear->getCode(),
                            )
                        );

                        return new ViewModel();
                    }

                    $this->getEntityManager()->persist(new Academic($academicYear, $academic));
                } else {
                    $formData = $formData['external'];

                    $promotion = $this->getEntityManager()
                        ->getRepository('SecretaryBundle\Entity\Promotion\External')
                        ->findOneByEmailAndAcademicYear($formData['external_email'], $academicYear);

                    if ($promotion) {
                        $this->flashMessenger()->warn(
                            'WARNING',
                            'The email is already in this promotion list!'
                        );

                        $this->redirect()->toRoute(
                            'secretary_admin_promotion',
                            array(
                                'action'       => 'manage',
                                'academicyear' => $academicYear->getCode(),
                            )
                        );

                        return new ViewModel();
                    }

                    $this->getEntityManager()->persist(
                        new External(
                            $academicYear,
                            $formData['external_first_name'],
                            $formData['external_last_name'],
                            $formData['external_email']
                        )
                    );
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The promotion was successfully added!'
                );

                $this->redirect()->toRoute(
                    'secretary_admin_promotion',
                    array(
                        'action'       => 'manage',
                        'academicyear' => $academicYear->getCode(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'academicYears'      => $academicYears,
                'activeAcademicYear' => $academicYear,
                'form'               => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $promotion = $this->getPromotionEntity();
        if ($promotion === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($promotion);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function updateAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $promotions = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Promotion')
            ->findAllByAcademicYear($academicYear);

        foreach ($promotions as $promotion) {
            $this->getEntityManager()->remove($promotion);
        }

        $studies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findAllByAcademicYearQuery($academicYear)->getResult();

        $academics = array();

        foreach ($studies as $study) {
            if (strpos(strtolower($study->getCombination()->getTitle()), 'master') === false || $study->getCombination()->getPhase() != 2) {
                continue;
            }

            $enrollments = $this->getEntityManager()
                ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Study')
                ->findAllByStudy($study);

            foreach ($enrollments as $enrollment) {
                $academics[$enrollment->getAcademic()->getId()] = $enrollment->getAcademic();
            }
        }

        foreach ($academics as $academic) {
            $this->getEntityManager()->persist(new Academic($academicYear, $academic));
        }

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'SUCCESS',
            'The promotion list is successfully updated!'
        );

        $this->redirect()->toRoute(
            'secretary_admin_promotion',
            array(
                'action'       => 'manage',
                'academicyear' => $academicYear->getCode(),
            )
        );

        return new ViewModel();
    }

    /**
     * @return array
     */
    private function search(AcademicYear $academicYear)
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Promotion')
                    ->findAllByName($this->getParam('string'), $academicYear);
            case 'mail':
                return $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Promotion')
                    ->findAllByEMail($this->getParam('string'), $academicYear);
        }

        return array();
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear|null
     */
    private function getAcademicYearEntity()
    {
        $date = null;
        if ($this->getParam('academicyear') !== null) {
            $date = AcademicYearUtil::getDateTime($this->getParam('academicyear'));
        }
        $academicYear = AcademicYearUtil::getUniversityYear($this->getEntityManager(), $date);

        if ($academicYear === null) {
            $this->flashMessenger()->error(
                'Error',
                'No academic year was found!'
            );

            $this->redirect()->toRoute(
                'secretary_admin_promotion',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $academicYear;
    }

    /**
     * @return Promotion|null
     */
    private function getPromotionEntity()
    {
        $promotion = $this->getEntityById('SecretaryBundle\Entity\Promotion');

        if (!($promotion instanceof Promotion)) {
            $this->flashMessenger()->error(
                'Error',
                'No promotion was found!'
            );

            $this->redirect()->toRoute(
                'secretary_admin_promotion',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $promotion;
    }
}
