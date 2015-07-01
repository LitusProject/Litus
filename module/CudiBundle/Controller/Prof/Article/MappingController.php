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

namespace CudiBundle\Controller\Prof\Article;

use CudiBundle\Entity\Article,
    CudiBundle\Entity\Article\SubjectMap,
    CudiBundle\Entity\Prof\Action,
    SyllabusBundle\Entity\Subject,
    SyllabusBundle\Entity\SubjectProfMap,
    Zend\View\Model\ViewModel;

/**
 * MappingController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class MappingController extends \CudiBundle\Component\Controller\ProfController
{
    public function addAction()
    {
        if (!($subject = $this->getSubjectEntity())) {
            return new ViewModel();
        }

        if (!($academicYear = $this->getCurrentAcademicYear())) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_prof_mapping_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                if (!($article = $this->getArticleEntity($formData['article']['id']))) {
                    return new ViewModel();
                }

                $mapping = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                    ->findOneByArticleAndSubjectAndAcademicYear($article, $subject, $academicYear, true);

                if (null === $mapping) {
                    $mapping = new SubjectMap($article, $subject, $academicYear, $formData['mandatory']);
                    $mapping->setIsProf(true);
                    $this->getEntityManager()->persist($mapping);

                    $action = new Action($this->getAuthentication()->getPersonObject(), 'mapping', $mapping->getId(), 'add');
                    $this->getEntityManager()->persist($action);
                } else {
                    $actions = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Prof\Action')
                        ->findAllByEntityAndEntityIdAndAction('mapping', $mapping->getId(), 'remove');
                    foreach ($actions as $action) {
                        $this->getEntityManager()->remove($action);
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The mapping was successfully added!'
                );

                $this->redirect()->toRoute(
                    'cudi_prof_subject',
                    array(
                        'action' => 'subject',
                        'id' => $subject->getId(),
                        'language' => $this->getLanguage()->getAbbrev(),
                    )
                );
            }
        }

        $nbArticles = count(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article')
                ->findAllByProf($this->getAuthentication()->getPersonObject())
        );

        return new ViewModel(
            array(
                'subject' => $subject,
                'form' => $form,
                'nbArticles' => $nbArticles,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($mapping = $this->getSubjectMapEntity())) {
            return new ViewModel();
        }

        if ($mapping->isProf()) {
            $actions = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Prof\Action')
                ->findAllByEntityAndEntityIdAndAction('mapping', $mapping->getId(), 'add');
            foreach ($actions as $action) {
                $this->getEntityManager()->remove($action);
            }

            $this->getEntityManager()->remove($mapping);
        } else {
            $action = new Action($this->getAuthentication()->getPersonObject(), 'mapping', $mapping->getId(), 'remove');
            $this->getEntityManager()->persist($action);
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function activateAction()
    {
        $this->initAjax();

        if (!($mapping = $this->getSubjectMapEntity())) {
            return new ViewModel();
        }

        $mapping->getArticle()->setIsSameAsPreviousYear($this->getRequest()->getPost()['sameAsPreviousYear']);

        $newMapping = new SubjectMap($mapping->getArticle(), $mapping->getSubject(), $this->getCurrentAcademicYear(), $mapping->isMandatory());
        $newMapping->setIsProf(true);
        $this->getEntityManager()->persist($newMapping);

        $action = new Action($this->getAuthentication()->getPersonObject(), 'mapping', $newMapping->getId(), 'add');
        $this->getEntityManager()->persist($action);

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'SUCCESS',
            'The mapping was successfully activated!'
        );

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return SubjectMap|null
     */
    private function getSubjectMapEntity()
    {
        if (!($academicYear = $this->getCurrentAcademicYear())) {
            return;
        }

        $mapping = $this->getEntityById('CudiBundle\Entity\File\Mapping');
        $mappingProf = null;

        if (null !== $mapping) {
            $mappingProf = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
                ->findOneBySubjectAndProfAndAcademicYear($mapping->getSubject(), $this->getAuthentication()->getPersonObject(), $academicYear);
        }

        if (!($mapping instanceof SubjectMap) || null === $mappingProf) {
            $this->flashMessenger()->error(
                'Error',
                'No subject map was found!'
            );

            $this->redirect()->toRoute(
                'cudi_prof_subject',
                array(
                    'action' => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        return $mapping;
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
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findOneBySubjectIdAndProfAndAcademicYear(
                $this->getParam('id', 0),
                $this->getAuthentication()->getPersonObject(),
                $academicYear
            );

        if (!($mapping instanceof SubjectProfMap)) {
            $this->flashMessenger()->error(
                'Error',
                'No subject was found!'
            );

            $this->redirect()->toRoute(
                'cudi_prof_subject',
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
     * @param  int|null     $id
     * @return Article|null
     */
    private function getArticleEntity($id = null)
    {
        $id = $id === null ? $this->getParam('id', 0) : $id;

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findOneByIdAndProf($id, $this->getAuthentication()->getPersonObject());

        if (!($article instanceof Article)) {
            $this->flashMessenger()->error(
                'Error',
                'No article was found!'
            );

            $this->redirect()->toRoute(
                'cudi_prof_subject',
                array(
                    'action' => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        return $article;
    }
}
