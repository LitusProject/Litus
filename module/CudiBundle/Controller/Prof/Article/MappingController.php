<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Controller\Prof\Article;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Article\SubjectMap,
    CudiBundle\Entity\Prof\Action,
    CudiBundle\Form\Prof\Mapping\Add as AddForm,
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
        if (!($subject = $this->_getSubject()))
            return new ViewModel();

        if (!($academicYear = $this->getAcademicYear()))
            return new ViewModel();

        $form = new AddForm();

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                if (!($article = $this->_getArticle($formData['article_id'])))
                    return;

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
                    foreach ($actions as $action)
                        $this->getEntityManager()->remove($action);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The mapping was successfully added!'
                    )
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

        if (!($mapping = $this->_getMapping()))
            return new ViewModel();

        if ($mapping->isProf()) {
            $actions = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Prof\Action')
                ->findAllByEntityAndEntityIdAndAction('mapping', $mapping->getId(), 'add');
            foreach ($actions as $action)
                $this->getEntityManager()->remove($action);

            $this->getEntityManager()->remove($mapping);
        } else {
            $action = new Action($this->getAuthentication()->getPersonObject(), 'mapping', $mapping->getId(), 'remove');
            $this->getEntityManager()->persist($action);
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    public function activateAction()
    {
        $this->initAjax();

        if (!($mapping = $this->_getMapping()))
            return new ViewModel();

        $mapping->getArticle()->setIsSameAsPreviousYear($this->getRequest()->getPost()['sameAsPreviousYear']);

        $newMapping = new SubjectMap($mapping->getArticle(), $mapping->getSubject(), $this->getAcademicYear(), $mapping->isMandatory());
        $newMapping->setIsProf(true);
        $this->getEntityManager()->persist($newMapping);

        $action = new Action($this->getAuthentication()->getPersonObject(), 'mapping', $newMapping->getId(), 'add');
        $this->getEntityManager()->persist($action);

        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'SUCCESS',
                'The mapping was successfully activated!'
            )
        );

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    private function _getMapping()
    {
        if (!($academicYear = $this->getAcademicYear()))
            return;

        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'ERROR',
                    'No ID was given to identify the mapping!'
                )
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

        $mapping = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article\SubjectMap')
            ->findOneById($this->getParam('id'));

        $mappingProf = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findOneBySubjectAndProfAndAcademicYear($mapping->getSubject(), $this->getAuthentication()->getPersonObject(), $academicYear);

        if (null === $mapping || null === $mappingProf) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'ERROR',
                    'No mapping with the given ID was found!'
                )
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

    private function _getSubject()
    {
        if (!($academicYear = $this->getAcademicYear()))
            return;

        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'ERROR',
                    'No ID was given to identify the subject!'
                )
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

        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findOneBySubjectIdAndProfAndAcademicYear(
                $this->getParam('id'),
                $this->getAuthentication()->getPersonObject(),
                $academicYear
            );

        if (null === $mapping) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'ERROR',
                    'No subject with the given ID was found!'
                )
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

    private function _getArticle($id = null)
    {
        $id = $id == null ? $this->getParam('id') : $id;

        if (null === $id) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'ERROR',
                    'No ID was given to identify the article!'
                )
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

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findOneByIdAndProf($id, $this->getAuthentication()->getPersonObject());

        if (null === $article) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'ERROR',
                    'No article with the given ID was found!'
                )
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
