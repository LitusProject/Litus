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
     * @var OrderArticleMap
     */
    private $orderArticleMap;

    protected $hydrator = 'LogisticsBundle\Hydrator\Order\OrderArticleMap';

    public function init()
    {
        parent::init();


        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'amount',
                'label'    => 'Amount',
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

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'status',
                'label'      => 'Status',
                'required'   => true,
                'attributes' => array(
                    'options' => OrderArticleMap::$POSSIBLE_STATUSES,
                ),
            )
        );

        $this->addSubmit('Save Changes', 'article_edit');

        if ($this->orderArticleMap !== null) {
            $this->bind($this->orderArticleMap);
        }
    }

    public function setOrderArticleMap(OrderArticleMap $orderArticleMap)
    {
        $this->orderArticleMap = $orderArticleMap;

        return $this;
    }
}