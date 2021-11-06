<?php

namespace CommonBundle\Component\Form\Admin\Fieldset;

/**
 * Add tab pane sub form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class TabPane extends \CommonBundle\Component\Form\Fieldset
{
    public function init()
    {
        $this->addClass('tab_pane');
    }

    public function setName($name)
    {
        $this->setAttribute('id', $name);

        return parent::setName($name);
    }
}
