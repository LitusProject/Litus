<?php

namespace CommonBundle\Component\Form\Admin\Fieldset;

/**
 * Add tab content sub form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class TabContent extends \CommonBundle\Component\Form\Fieldset
{
    public function init()
    {
        $this->addClass('tab_content');
    }
}
