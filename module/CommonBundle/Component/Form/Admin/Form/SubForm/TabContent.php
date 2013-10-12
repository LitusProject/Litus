<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Form\Admin\Form\SubForm;

/**
 * Add tab content sub form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class TabContent extends \CommonBundle\Component\Form\Admin\Fieldset
{
    /**
     * Constructor
     *
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setAttribute('class', 'tab_content');
    }

    /**
     * Recursively populate values of attached elements and fieldsets
     *
     * @param  array|Traversable $data
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function populateValues($data)
    {
        parent::populateValues($data);

        $fieldsets = $this->getFieldsets();
        foreach($fieldsets as $fieldset) {
            $fieldset->populateValues($data);
        }

        return $this;
    }
}
