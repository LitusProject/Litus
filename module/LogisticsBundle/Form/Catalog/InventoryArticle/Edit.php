<?php

namespace LogisticsBundle\Form\Catalog\InventoryArticle;

use LogisticsBundle\Entity\Order;

/**
 * Form used to edit articles of an order
 *
 * @author Pedro Devogelaere
 */
class Edit extends \LogisticsBundle\Form\Catalog\InventoryArticle\Add
{
    /**
     * @var array
     */
    private array $articles = array();

    /**
     * @var Order
     */
    private Order $order;

    public function init(): void
    {
        parent::init();

        foreach ($this->articles as $article) {
            $mapping = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\OrderInventoryArticleMap')
                ->findOneByOrderAndArticle($this->order, $article);

            $this->add(
                array(
                    'type'       => 'text',
                    'name'       => 'article-' . $article->getId(),
                    'attributes' => array(
                        'class'       => 'input-very-mini',
                        'style'       => 'float: left; width: 35%; height: 22px; max-width: 50px; min-width: 35px; margin-left:20%',
                        'id'          => 'article-' . $article->getId(),
                        'placeholder' => $mapping ? $mapping->getAmount() : '0',
                    ),
                    'options'    => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array('name' => 'Int'),
                            ),
                        ),
                    ),
                )
            );
        }

        $this->addSubmit('Update', 'btn btn-primary pull-right');
    }

    /**
     * @param  array $articles
     * @return self
     */
    public function setArticles(array $articles): self
    {
        $this->articles = $articles;

        return $this;
    }

    /**
     * @param Order $order
     * @return self
     */
    public function setOrder(Order $order): self
    {
        $this->order = $order;

        return $this;
    }
}
