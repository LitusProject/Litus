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

namespace CudiBundle\Controller\Admin\Prof;

use CudiBundle\Entity\Article\History,
    CudiBundle\Entity\Log\Article\SubjectMap\Added as SubjectMapAddedLog,
    Zend\View\Model\ViewModel;

/**
 * ActionController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ActionController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Prof\Action')
                ->findAllUncompletedQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function completedAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Prof\Action')
                ->findAllCompletedQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function refusedAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Prof\Action')
                ->findAllRefusedQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function viewAction()
    {
        if (!($action = $this->getAction())) {
            return new ViewModel();
        }

        $action->setEntityManager($this->getEntityManager());

        return new ViewModel(
            array(
                'action' => $action,
            )
        );
    }

    public function refuseAction()
    {
        if (!($action = $this->getAction())) {
            return new ViewModel();
        }

        $action->setRefused($this->getAuthentication()->getPersonObject());

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'Success',
            'The action is successfully refused!'
        );

        $this->redirect()->toRoute(
            'cudi_admin_prof_action',
            array(
                'action' => 'refused',
            )
        );

        return new ViewModel();
    }

    public function confirmAction()
    {
        if (!($action = $this->getAction())) {
            return new ViewModel();
        }

        $action->setEntityManager($this->getEntityManager());

        if ($action->getEntityName() == 'article') {
            if ($action->getAction() == 'add') {
                $this->redirect()->toRoute(
                    'cudi_admin_prof_action',
                    array(
                        'action' => 'confirmArticle',
                        'id' => $action->getId(),
                    )
                );

                return new ViewModel();
            } elseif ($action->getAction() == 'delete') {
                $action->getEntity()->setIsHistory(true);
            } else {
                $edited = $action->getEntity();
                $current = $action->getPreviousEntity();
                $duplicate = clone $current;

                $current->setTitle($edited->getTitle())
                    ->setAuthors($edited->getAuthors())
                    ->setPublishers($edited->getPublishers())
                    ->setYearPublished($edited->getYearPublished())
                    ->setISBN($edited->getISBN())
                    ->setURL($edited->getURL())
                    ->setIsDownloadable($edited->isDownloadable())
                    ->setType($edited->getType());

                $edited->setTitle($duplicate->getTitle())
                    ->setAuthors($duplicate->getAuthors())
                    ->setPublishers($duplicate->getPublishers())
                    ->setYearPublished($duplicate->getYearPublished())
                    ->setISBN($duplicate->getISBN())
                    ->setURL($duplicate->getURL())
                    ->setIsProf(false);

                if ($current->isInternal()) {
                    $current->setBinding($edited->getBinding())
                        ->setIsRectoVerso($edited->isRectoVerso())
                        ->setIsPerforated($edited->isPerforated())
                        ->setIsColored($edited->isColored());

                    $edited->setBinding($duplicate->getBinding())
                        ->setIsRectoVerso($duplicate->isRectoVerso())
                        ->setIsPerforated($duplicate->isPerforated())
                        ->setIsColored($duplicate->isColored());
                }

                $history = new History($current, $edited);
                $this->getEntityManager()->persist($history);

                $action->setEntityId($current->getId())
                    ->setPreviousId($edited->getId());
            }
        } elseif ($action->getEntityName() == 'mapping') {
            if ($action->getAction() == 'add') {
                $action->getEntity()->setIsProf(false);
                $this->getEntityManager()->persist(new SubjectMapAddedLog($this->getAuthentication()->getPersonObject(), $action->getEntity()));
            } else {
                $action->getEntity()->setRemoved();
            }
        } elseif ($action->getEntityName() == 'file') {
            if ($action->getAction() == 'add') {
                $this->redirect()->toRoute(
                    'cudi_admin_prof_action',
                    array(
                        'action' => 'confirmFile',
                        'id' => $action->getId(),
                    )
                );

                return new ViewModel();
            } else {
                $action->getEntity()->setRemoved();
            }
        }

        $action->setCompleted($this->getAuthentication()->getPersonObject());

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'Success',
            'The action is successfully confirmed!'
        );

        $this->redirect()->toRoute(
            'cudi_admin_prof_action',
            array(
                'action' => 'completed',
            )
        );

        return new ViewModel();
    }

    public function confirmArticleAction()
    {
        if (!($action = $this->getAction())) {
            return new ViewModel();
        }

        $action->setEntityManager($this->getEntityManager());

        if ($action->getEntity()->isDraft()) {
            $this->flashMessenger()->error(
                'Error',
                'No action with was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_prof_action',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();
        }

        $article = $action->getEntity();

        $form = $this->getForm('cudi_prof_article_confirm', array('article' => $article));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $article->setIsProf(false);

                $action->setCompleted($this->getAuthentication()->getPersonObject());

                if ($article->isInternal()) {
                    $cachePath = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('cudi.front_page_cache_dir');
                    if (null !== $article->getFrontPage() && file_exists($cachePath . '/' . $article->getFrontPage())) {
                        unlink($cachePath . '/' . $article->getFrontPage());
                        $article->setFrontPage();
                    }
                }

                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'cudi_admin_prof_action',
                    array(
                        'action' => 'completed',
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

    public function confirmFileAction()
    {
        if (!($action = $this->getAction())) {
            return new ViewModel();
        }

        $action->setEntityManager($this->getEntityManager());

        $mapping = $action->getEntity();

        $form = $this->getForm('cudi_prof_file_confirm', array('mapping' => $mapping));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $mapping->setIsProf(false)
                    ->setPrintable($formData['printable'])
                    ->getFile()->setDescription($formData['description']);

                $action->setCompleted($this->getAuthentication()->getPersonObject());

                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'cudi_admin_prof_action',
                    array(
                        'action' => 'completed',
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

    private function getAction()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the action!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_prof_action',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $action = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Prof\Action')
            ->findOneById($this->getParam('id'));

        if (null === $action) {
            $this->flashMessenger()->error(
                'Error',
                'No action with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_prof_action',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $action;
    }
}
