<?php

namespace LogisticsBundle\Form\Catalog\Map;

use LogisticsBundle\Entity\Order\OrderArticleMap as Map;

/**
 * Edit OrderArticleMap
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var Map
     */
    private $map;

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

        $this->addSubmit('Save Changes');

        if ($this->map !== null) {
            $this->bind($this->map);
        }
    }

    public function setMap(Map $map)
    {
        $this->map = $map;

        return $this;
    }
}
