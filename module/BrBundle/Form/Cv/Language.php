<?php

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

        $this->add(
            array(
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
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'language_oral',
                'label'      => 'Oral Skills',
                'required'   => true,
                'attributes' => array(
                    'options' => self::$writtenAndOralSkills,
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'language_written',
                'label'      => 'Written Skills',
                'required'   => true,
                'attributes' => array(
                    'options' => self::$writtenAndOralSkills,
                ),
            )
        );
    }
}
