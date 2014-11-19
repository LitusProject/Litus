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

namespace FormBundle\Form\Admin\Field\Field;

use FormBundle\Component\Validator\StringField as StringFieldValidator;

/**
* Add String Field
*
* @author Kristof Mariën <kristof.marien@litus.cc>
*/
class String extends \CommonBundle\Component\Form\Fieldset
{
    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'multiline',
            'label'      => 'Multiline',
            'attributes' => array(
                'data-help' => 'Allow multiple lines in the field (textarea).',
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'charsperline',
            'label'      => 'Max. characters per line (or Infinite)',
            'attributes' => array(
                'data-help' => 'The maximum numbers of characters on one line. Zero is infinite.',
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'digits',
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'lines',
            'label'      => 'Max. number of lines (Multiline fields only)',
            'attributes' => array(
                'data-help' => 'The maximum numbers of lines. Zero is infinite.',
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'digits',
                        ),
                    ),
                ),
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        $specs['charsperline']['options']['input']['validators'][] = new StringFieldValidator(
            isset($this->data['multiline']) ? $this->data['multiline'] : null,
            isset($this->data['lines']) ? $this->data['lines'] : null
        );

        return $specs;
    }
}
