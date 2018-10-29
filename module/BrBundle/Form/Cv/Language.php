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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Form\Cv;

/**
 * Add Option
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Language extends \CommonBundle\Component\Form\Fieldset
{
    public static $writtenAndOralSkills = array(
        'Notions'       => 'Notions',
        'Basis'         => 'Basis',
        'Good'          => 'Good',
        'Very good'     => 'Very good',
        'Mother tongue' => 'Mother tongue',
    );

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'text',
            'name'       => 'language_name',
            'label'      => 'Language',
            'required'   => true,
            'attributes' => array(
                'class'      => 'count',
                'data-count' => 30,
            ),
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'language_oral',
            'label'      => 'Oral Skills',
            'required'   => true,
            'attributes' => array(
                'options' => self::$writtenAndOralSkills,
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'language_written',
            'label'      => 'Written Skills',
            'required'   => true,
            'attributes' => array(
                'options' => self::$writtenAndOralSkills,
            ),
        ));
    }
}
