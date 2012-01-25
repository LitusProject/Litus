<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CommonBundle\Form\Admin\Config;

use CommonBundle\Entity\Public\Config,
	Zend\Form\Element\Hidden;

/**
 * Edit a configuration entry.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Edit extends Add
{
	/**
	 * @param mixed $options The form's options
	 */
    public function __construct(Config $config, $options = null)
    {
        parent::__construct($options);

        $this->populate(
            array(
                'description' => $config->getDescription(),
                'value' => $config->getValue()
            )
        );

        $field = $this->getElement('submit');
        $field->setLabel('Edit');

        $field = $this->getElement('prefix');
        $this->removeElement($field);

        $field = $this->getElement('name');
        $this->removeElement($field);

        $field = new Hidden('name');
        $field->setValue($config->getKey());
        $this->addElement($field);
    }
}
