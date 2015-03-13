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

namespace CudiBundle\Controller\Admin\Sale\Article;

use CommonBundle\Component\Controller\Exception\RuntimeException,
    CudiBundle\Entity\Sale\Article\Restriction\Amount as AmountRestriction,
    CudiBundle\Entity\Sale\Article\Restriction\Member as MemberRestriction,
    CudiBundle\Entity\Sale\Article\Restriction\Study as StudyRestriction,
    Zend\View\Model\ViewModel;

/**
 * RestrictionController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class RestrictionController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        if (!($article = $this->_getSaleArticle())) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_sale_article_restriction_add', array('article' => $article));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                if ('amount' == $formData['type']) {
                    $restriction = new AmountRestriction($article, $formData['value']['amount']);
                } elseif ('member' == $formData['type']) {
                    $restriction = new MemberRestriction($article, isset($formData['value']['member']) && $formData['value']['member']);
                } elseif ('study' == $formData['type']) {
                    $restriction = new StudyRestriction($article);

                    foreach ($formData['value']['study'] as $id) {
                        $study = $this->getEntityManager()
                            ->getRepository('SyllabusBundle\Entity\Study')
                            ->findOneById($id);

                        $restriction->addStudy($study);
                    }
                } else {
                    throw new RuntimeException("Unsupported restriction type");
                }

                $this->getEntityManager()->persist($restriction);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The restriction was successfully created!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_article_restriction',
                    array(
                        'action' => 'manage',
                        'id' => $article->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Article\Restriction')
                ->findAllByArticleQuery($article),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'article' => $article,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($restriction = $this->_getRestriction())) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($restriction);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return \CudiBundle\Entity\Sale\Article|null
     */
    private function _getSaleArticle()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the article!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findOneById($this->getParam('id'));

        if (null === $article) {
            $this->flashMessenger()->error(
                'Error',
                'No article with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $article;
    }

    /**
     * @return \CudiBundle\Entity\Sale\Article\Restriction|null
     */
    private function _getRestriction()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the restriction!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $restriction = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article\Restriction')
            ->findOneById($this->getParam('id'));

        if (null === $restriction) {
            $this->flashMessenger()->error(
                'Error',
                'No restriction with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $restriction;
    }
}
