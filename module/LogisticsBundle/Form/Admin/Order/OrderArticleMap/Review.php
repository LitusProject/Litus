<?php

namespace LogisticsBundle\Form\Admin\Order\OrderArticleMap;

use LogisticsBundle\Entity\Order\OrderArticleMap;

/**
 * Edit OrderArticleMap
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Review extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var array[]
     */
    private $articles = array();

    protected $hydrator = 'LogisticsBundle\Hydrator\Order\OrderArticleMap';

    public function init()
    {
        parent::init();

        foreach ($this->articles as $mapping) {

            $this->add(
                array(
                    'type'     => 'text',
                    'name'     => 'article-' . $mapping->getId(),
                    'label'    => 'Amount',
                    'attributes' => array(
                        'class'       => 'input-very-mini',
                        'id'          => 'article-' . $mapping->getId(),
                        'placeholder' => $mapping->getAmount(),
                    ),
                    'required' => true,
                    'options'  => array(
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
    }

    public function setArticles(array $articles)
    {
        $this->articles = $articles;

        return $this;
    }
}