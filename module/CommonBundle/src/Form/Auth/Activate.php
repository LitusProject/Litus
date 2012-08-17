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

namespace CommonBundle\Form\Auth;

use Zend\Form\Element\Password,
    Zend\Form\Element\Submit,
    Zend\Validator\Identical as IdenticalValidator;

/**
 * Account activate form.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Activate extends \Zend\Form\Form
{
    /**
     * @param mixed $options The form's options
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        $field = new Password('credential');
        $field->setLabel('Password')
            ->setRequired();
        $this->addElement($field);

        $field = new Password('verify_credential');
        $field->setLabel('Repeat Password')
            ->setRequired()
            ->addValidator(new IdenticalValidator('credential'));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Activate');
        $this->addElement($field);
    }
}
