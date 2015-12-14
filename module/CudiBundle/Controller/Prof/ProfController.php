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

namespace CudiBundle\Controller\Prof;

use SyllabusBundle\Entity\Subject,
    SyllabusBundle\Entity\Subject\ProfMap,
    Zend\View\Model\ViewModel;

/**
 * ProfController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ProfController extends \CudiBundle\Component\Controller\ProfController
{
    public function addAction()
    {
        if (!($subject = $this->getSubjectEntity())) {
            return new ViewModel();
        }

        if (!($academicYear = $this->getCurrentAcademicYear())) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_prof_prof_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $docent = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findOneById($formData['prof']['id']);

                $mapping = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
                    ->findOneBySubjectAndProfAndAcademicYear($subject, $docent, $academicYear);

                if (null === $mapping) {
                    $mapping = new ProfMap($subject, $docent, $academicYear);
                    $this->getEntityManager()->persist($mapping);
                    $this->getEntityManager()->flush();
                }

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The docent was successfully added!'
                );

                $this->redirect()->toRoute(
                    'cudi_prof_subject',
                    array(
                        'action' => 'subject',
                        'id' => $subject->getId(),
                        'language' => $this->getLanguage()->getAbbrev(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'subject' => $subject,
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($mapping = $this->getProfMapEntity())) {
            return new ViewModel();
        }

        if ($mapping->getProf()->getId() == $this->getAuthentication()->getPersonObject()->getId()) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        $this->getEntityManager()->remove($mapping);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function typeaheadAction()
    {
        $docents = array_merge(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findAllByName($this->getParam('string')),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findAllByUniversityIdentification($this->getParam('string'))
        );

        $result = array();
        foreach ($docents as $docent) {
            $item = (object) array();
            $item->id = $docent->getId();
            $item->value = $docent->getUniversityIdentification() . ' - ' . $docent->getFullName();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return Subject|null
     */
    private function getSubjectEntity()
    {
        if (!($academicYear = $this->getCurrentAcademicYear())) {
            return;
        }

        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
            ->findOneBySubjectIdAndProfAndAcademicYear(
                $this->getParam('id', 0),
                $this->getAuthentication()->getPersonObject(),
                $academicYear
            );

        if (!($mapping instanceof ProfMap)) {
            $this->flashMessenger()->error(
                'Error',
                'No subject was found!'
            );

            $this->redirect()->toRoute(
                'cudi_prof_article',
                array(
                    'action' => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        return $mapping->getSubject();
    }

    /**
     * @return ProfMap|null
     */
    private function getProfMapEntity()
    {
        if (!($academicYear = $this->getCurrentAcademicYear())) {
            return;
        }

        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
            ->findOneById(
                $this->getParam('id', 0)
            );

        if (!($mapping instanceof ProfMap)) {
            $this->flashMessenger()->error(
                'Error',
                'No subject was found!'
            );

            $this->redirect()->toRoute(
                'cudi_prof_article',
                array(
                    'action' => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        return $mapping;
    }
}
