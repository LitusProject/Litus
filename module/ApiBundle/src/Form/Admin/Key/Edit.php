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

namespace ApiBundle\Form\Admin\Key;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    ApiBundle\Entity\Key,
    Zend\Form\Element\Text,
    Zend\Form\Element\Submit;

/**
 * Edit Key
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \ApiBundle\Entity\Key $key The key we're going to modify
     * @param mixed $opts The form's options
     */
    public function __construct(Key $key, $opts = null)
    {
        parent::__construct($opts);

        $field = new Text('code');
        $field->setLabel('Code')
            ->setAttrib('disabled', 'disabled')
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $this->removeElement('submit');

        $field = new Submit('submit');
        $field->setLabel('Save')
            ->setAttrib('class', 'key_edit')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);

        $this->_populateFromKey($key);
    }

    private function _populateFromKey(Key $key)
    {
        $data = array(
            'host' => $key->getHost(),
            'code' => $key->getCode()
        );

        $this->populate($data);
    }
}
