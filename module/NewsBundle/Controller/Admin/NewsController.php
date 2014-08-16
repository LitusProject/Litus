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

use Zend\View\Model\ViewModel;

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
        $form = $this->getForm('news_news_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $news = $form->hydrateObject();

                $this->getEntityManager()->persist($news);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The news item was successfully added!'
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

        $form = $this->getForm('news_news_edit', $news);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The news item was successfully edited!'
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
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the news item!'
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
            $this->flashMessenger()->error(
                'Error',
                'No news item with the given ID was found!'
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
