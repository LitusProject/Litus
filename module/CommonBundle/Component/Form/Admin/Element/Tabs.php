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

namespace CommonBundle\Component\Form\Admin\Element;

use CommonBundle\Component\Form\ElementTrait;

/**
 * Tabs
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Tabs extends \Zend\Form\Element implements \CommonBundle\Component\Form\ElementInterface
{
    use ElementTrait;

    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'tabs',
        'tabs' => array(),
        'class' => '',
    );

    /**
     * @param array $tabs
     *
     * @return Tabs
     */
    public function setTabs($tabs = array())
    {
        $this->attributes['tabs'] = $tabs;

        return $this;
    }

    /**
     * @return array
     */
    public function getTabs()
    {
        return $this->attributes['tabs'];
    }

    /**
     * @param array $tab
     *
     * @return Tabs
     */
    public function addTab($tab)
    {
        $this->attributes['tabs'] = array_merge($this->attributes['tabs'], $tab);

        return $this;
    }
}
