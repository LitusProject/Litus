<?php

namespace CudiBundle\Controller\Supplier;

use Laminas\View\Model\ViewModel;

/**
 * ArticleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ArticleController extends \CudiBundle\Component\Controller\SupplierController
{
    public function manageAction()
    {
        $articles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findAllBySupplier($this->getSupplierEntity());

        return new ViewModel(
            array(
                'articles' => $articles,
            )
        );
    }
}
