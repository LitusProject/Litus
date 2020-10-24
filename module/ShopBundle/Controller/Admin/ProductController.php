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

namespace ShopBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use ShopBundle\Entity\Product;

/**
 * ProductController
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class ProductController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('ShopBundle\Entity\Product')
                ->findAllQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('shop_product_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $product = $form->hydrateObject();
                $this->getEntityManager()->persist($product);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The product was successfully created!'
                );

                $this->redirect()->toRoute(
                    'shop_admin_shop_product',
                    array(
                        'action' => 'add',
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
        $product = $this->getProductEntity();
        if ($product === null) {
            return new ViewModel();
        }

        $form = $this->getForm('shop_product_edit', array('product' => $product));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The product was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'shop_admin_shop_product',
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

    public function deleteAction()
    {
        $this->initAjax();

        $product = $this->getProductEntity();
        if ($product === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($product);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    /**
     * @return Product|null
     */
    private function getProductEntity()
    {
        $product = $this->getEntityById('ShopBundle\Entity\Product');

        if (!($product instanceof Product)) {
            $this->flashMessenger()->error(
                'Error',
                'No product was found!'
            );

            $this->redirect()->toRoute(
                'shop_admin_shop_product',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $product;
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $products = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($products as $product) {
            $item = (object) array();
            $item->id = $product->getId();
            $item->name = $product->getName();
            $item->sellPrice = $product->getSellPrice();
            $item->available = $product->getAvailable();

            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return \Doctrine\ORM\Query|null
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('ShopBundle\Entity\Product')
                    ->findAllByNameQuery($this->getParam('string'));
        }
    }
}
