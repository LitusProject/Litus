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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace LogisticsBundle\Controller\Admin;

use Imagick;
use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\Article;

/**
 * ArticleController
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 */
class ArticleController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if ($this->getParam('field') !== null) {
            $articles = $this->search();
        }

        if (!isset($articles)) {
            $articles = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Article')
                ->findAllQuery();
        }

        $paginator = $this->paginator()->createFromQuery(
            $articles,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'           => $paginator,
                'paginationControl'   => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('logistics_article_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The item was successfully added!'
                );

                $this->redirect()->toRoute(
                    'logistics_admin_article',
                    array(
                        'action' => 'manage',
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
        $article = $this->getArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }

        $form = $this->getForm('logistics_article_edit', $article);

        $pictureForm = $this->getForm('logistics_admin_article_picture');
        $pictureForm->setAttribute(
            'action',
            $this->url()->fromRoute(
                'logistics_admin_article',
                array(
                    'action' => 'uploadProfileImage',
                )
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The item was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'logistics_admin_article',
                    array(
                        'action' => 'manage',
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'article' => $article,
                'picturePath' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('logistics.article_picture_path'),
                'pictureForm' => $pictureForm,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $article = $this->getArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($article);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function ordersAction()
    {
        $article = $this->getArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }

        $mappings = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findAllActiveByArticleQuery($article)->getResult();
        
        return new ViewModel(
            array(
                'orders'               => $mappings,
                'article'             => $article,
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $articles = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($articles as $article) {
            $item = (object) array();
            $item->id = $article->getId();
            $item->name = $article->getName();
            $item->amountOwned = $article->getAmountOwned();
            $item->amountAvailable = $article->getAmountAvailable();
            $item->category = $article->getCategory();
            $item->location = $article->getLocation();
            $item->spot = $article->getSpot();
            $item->status = $article->getStatus();
            $item->visibility = $article->getVisibility();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return Article|null
     */
    private function getArticleEntity()
    {
        $article = $this->getEntityById('LogisticsBundle\Entity\Article');

        if (!($article instanceof Article)) {
            $this->flashMessenger()->error(
                'Error',
                'No article was found!'
            );

            $this->redirect()->toRoute(
                'logistics_admin_article',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $article;
    }

    /**
     * @return \Doctrine\ORM\Query|null
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Article')
                    ->findAllByNameQuery($this->getParam('string'));
            case 'location':
                return $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Article')
                    ->findAllByLocationQuery($this->getParam('string'));
            case 'status':
                return $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Article')
                    ->findAllByStatusQuery($this->getParam('string'));
            case 'visibility':
                return $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Article')
                    ->findAllByVisibilityQuery($this->getParam('string'));
        }
        return;
    }

    public function uploadImageAction()
    {
        $article = $this->getArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }

        $form = $this->getForm('logistics_admin_article_picture');

        if ($this->getRequest()->isPost()) {
            $form->setData(
                array_merge_recursive(
                    $this->getRequest()->getPost()->toArray(),
                    $this->getRequest()->getFiles()->toArray()
                )
            );

            $filePath = 'public' . $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('logistics.article_picture_path');

            if ($form->isValid()) {
                $formData = $form->getData();

                if ($formData['picture']) {
                    $image = new Imagick($formData['picture']['tmp_name']);
                } else {
                    $image = new Imagick($filePath . '/' . $article->getPhotoPath());
                }

                if ($formData['x'] == 0 && $formData['y'] == 0 && $formData['x2'] == 0 && $formData['y2'] == 0 && $formData['w'] == 0 && $formData['h'] == 0) {
                    $image->cropThumbnailImage(320, 240);
                } else {
                    $ratio = $image->getImageWidth() / 320;
                    $x = $formData['x'] * $ratio;
                    $y = $formData['y'] * $ratio;
                    $w = $formData['w'] * $ratio;
                    $h = $formData['h'] * $ratio;

                    $image->cropImage($w, $h, $x, $y);
                    $image->cropThumbnailImage(320, 240);
                }

                do {
                    $newFileName = sha1(uniqid());
                } while (file_exists($filePath . '/' . $newFileName));

                if ($article->getPhotoPath() != '' || $article->getPhotoPath() !== null) {
                    $fileName = $article->getPhotoPath();

                    if (file_exists($filePath . '/' . $fileName)) {
                        unlink($filePath . '/' . $fileName);
                    }
                }

                $image->writeImage($filePath . '/' . $newFileName);
                $article->setPhotoPath($newFileName);

                $this->getEntityManager()->flush();

                return new ViewModel(
                    array(
                        'result' => array(
                            'status' => 'success',
                            'picture' => $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\General\Config')
                                ->getConfigValue('logistics.article_picture_path') . '/' . $newFileName,
                        ),
                    )
                );
            } else {
                return new ViewModel(
                    array(
                        'result' => array(
                            'status' => 'error',
                            'form' => array(
                                'errors' => $form->getMessages(),
                            ),
                        ),
                    )
                );
            }
        }
    }
}
