<?php

namespace CommonBundle\Component\Form\Admin\Element;

use CommonBundle\Component\Form\ElementTrait;

/**
 * Tabs
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Tabs extends \Laminas\Form\Element implements \CommonBundle\Component\Form\ElementInterface
{
    use ElementTrait;

    /**
     * @var array
     */
    protected $attributes = array(
        'type'  => 'tabs',
        'tabs'  => array(),
        'class' => '',
    );

    /**
     * @return array
     */
    public function getTabs()
    {
        return $this->attributes['tabs'];
    }

    /**
     * @param  array $tabs
     * @return Tabs
     */
    public function setTabs($tabs = array())
    {
        $this->attributes['tabs'] = $tabs;

        return $this;
    }

    /**
     * @param  array $tab
     * @return Tabs
     */
    public function addTab($tab)
    {
        $this->attributes['tabs'] = array_merge(
            $this->attributes['tabs'],
            $tab
        );

        return $this;
    }
}
