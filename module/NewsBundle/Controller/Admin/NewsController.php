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

namespace NewsBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    NewsBundle\Entity\Node\News,
    NewsBundle\Entity\Node\Translation,
    NewsBundle\Form\Admin\News\Add as AddForm,
    NewsBundle\Form\Admin\News\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * NewsController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class NewsController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'NewsBundle\Entity\Node\News',
            $this->getParam('page'),
            array(),
            array(
                'creationTime' => 'DESC',
            )
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $endDate = DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']);

                $news = new News(
                    $this->getAuthentication()->getPersonObject(),
                    $endDate ? $endDate : null
                );
                $this->getEntityManager()->persist($news);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
                    if ('' != $formData['title_' . $language->getAbbrev()] && '' != $formData['content_' . $language->getAbbrev()]) {
                        $news->addTranslation(
                            new Translation(
                                $news,
                                $language,
                                $formData['title_' . $language->getAbbrev()],
                                str_replace('#', '', $formData['content_' . $language->getAbbrev()])
                            )
                        );
                    }
                }
                $news->updateName();

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The news item was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'news_admin_news',
                    array(
                        'action' => 'manage'
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
        if (!($news = $this->_getNews()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $news);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $endDate = DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']);
                if ($endDate)
                    $news->setEndDate($endDate);
                else
                    $news->setEndDate(null);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
                    $translation = $news->getTranslation($language, false);

                    if (null !== $translation) {
                        $translation->setTitle($formData['title_' . $language->getAbbrev()])
                            ->setContent($formData['content_' . $language->getAbbrev()]);
                    } else {
                        if ('' != $formData['title_' . $language->getAbbrev()] && '' != $formData['content_' . $language->getAbbrev()]) {
                            $news->addTranslation(
                                new Translation(
                                    $news,
                                    $language,
                                    $formData['title_' . $language->getAbbrev()],
                                    str_replace('#', '', $formData['content_' . $language->getAbbrev()])
                                )
                            );
                        }
                    }
                }
                $news->updateName();

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The news item was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'news_admin_news',
                    array(
                        'action' => 'manage'
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
        $this->initAjax();

        if (!($news = $this->_getNews()))
            return new ViewModel();

        $this->getEntityManager()->remove($news);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    private function _getNews()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the news item!'
                )
            );

            $this->redirect()->toRoute(
                'news_admin_news',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $news = $this->getEntityManager()
            ->getRepository('NewsBundle\Entity\Node\News')
            ->findOneById($this->getParam('id'));

        if (null === $news) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No news item with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'news_admin_news',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $news;
    }
}
