<?php

namespace CudiBundle\Controller\Prof\Article;

use CudiBundle\Entity\Article;
use CudiBundle\Entity\Article\SubjectMap;
use CudiBundle\Entity\Prof\Action;
use Laminas\View\Model\ViewModel;
use SyllabusBundle\Entity\Subject;
use SyllabusBundle\Entity\Subject\ProfMap;

/**
 * MappingController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class MappingController extends \CudiBundle\Component\Controller\ProfController
{
    public function addAction()
    {
        $subject = $this->getSubjectEntity();
        if ($subject === null) {
            return new ViewModel();
        }

        $academicYear = $this->getCurrentAcademicYear();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_prof_mapping_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $article = $this->getArticleEntity($formData['article']['id']);
                if ($article === null) {
                    return new ViewModel();
                }

                $mapping = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                    ->findOneByArticleAndSubjectAndAcademicYear($article, $subject, $academicYear, true);

                if ($mapping === null) {
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
                        'action'   => 'subject',
                        'id'       => $subject->getId(),
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
                'subject'    => $subject,
                'form'       => $form,
                'nbArticles' => $nbArticles,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $mapping = $this->getSubjectMapEntity();
        if ($mapping === null) {
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

        $mapping = $this->getSubjectMapEntity();
        if ($mapping === null) {
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
        $academicYear = $this->getCurrentAcademicYear();
        if ($academicYear === null) {
            return;
        }

        $mapping = $this->getEntityById('CudiBundle\Entity\Article\SubjectMap');

        $mappingProf = null;

        if ($mapping !== null) {
            $mappingProf = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
                ->findOneBySubjectAndProfAndAcademicYear($mapping->getSubject(), $this->getAuthentication()->getPersonObject(), $academicYear);
        }

        if (!($mapping instanceof SubjectMap) || $mappingProf === null) {
            $this->flashMessenger()->error(
                'Error',
                'No subject map was found!'
            );

            $this->redirect()->toRoute(
                'cudi_prof_subject',
                array(
                    'action'   => 'manage',
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
        $academicYear = $this->getCurrentAcademicYear();
        if ($academicYear === null) {
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
                'cudi_prof_subject',
                array(
                    'action'   => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        return $mapping->getSubject();
    }

    /**
     * @param  integer|null $id
     * @return Article|null
     */
    private function getArticleEntity($id = null)
    {
        $id = $id ?? $this->getParam('id', 0);

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
                    'action'   => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        return $article;
    }
}
