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

namespace SecretaryBundle\Controller\Admin;

use CommonBundle\Component\Util\AcademicYear as AcademicYearUtil,
    CommonBundle\Entity\General\AcademicYear,
    SecretaryBundle\Entity\Promotion,
    SecretaryBundle\Entity\Promotion\Academic,
    SecretaryBundle\Entity\Promotion\External,
    Zend\View\Model\ViewModel;

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

        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        if (null !== $this->getParam('field')) {
            $paginator = $this->paginator()->createFromArray(
                $this->search($academicYear),
                $this->getParam('page')
            );
        }

        if (!isset($paginator)) {
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
                'academicYears' => $academicYears,
                'activeAcademicYear' => $academicYear,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        if (!($academicYear = $this->getAcademicYearEntity())) {
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

        if (!($academicYear = $this->getAcademicYearEntity())) {
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
                                'action' => 'manage',
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
                                'action' => 'manage',
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
                        'action' => 'manage',
                        'academicyear' => $academicYear->getCode(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'academicYears' => $academicYears,
                'activeAcademicYear' => $academicYear,
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($promotion = $this->getPromotionEntity())) {
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
        if (!($academicYear = $this->getAcademicYearEntity())) {
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
                ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
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
                'action' => 'manage',
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
        if (null !== $this->getParam('academicyear')) {
            $date = AcademicYearUtil::getDateTime($this->getParam('academicyear'));
        }
        $academicYear = AcademicYearUtil::getUniversityYear($this->getEntityManager(), $date);

        if (null === $academicYear) {
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
