<?php

namespace BrBundle\Form\Cv;

/**
 * Add Option
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Experience extends \CommonBundle\Component\Form\Fieldset
{
    public function init()
    {
        parent::init();

        list($currentYear, $allYears) = $this->getYears();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'experience_type',
                'label'      => 'Type',
                'required'   => true,
                'attributes' => array(
                    'options' => array(
                        'Internship' => 'Internship',
                        'Job Student' => 'Job Student',
                        'Volunteer'  => 'Volunteer',
                        'Other'      => 'Other',
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'experience_function',
                'label'      => 'Function',
                'required'   => true,
                'attributes' => array(
                    'class'      => 'count',
                    'data-count' => 50,
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'FieldLength',
                                'options' => array(
                                    'max_length'      => 50,
                                    'new_line_length' => 75,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'experience_start',
                'label'      => 'Start',
                'required'   => true,
                'value'      => $currentYear - 1,
                'attributes' => array(
                    'options' => $allYears,
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'experience_end',
                'label'      => 'End',
                'required'   => true,
                'value'      => $currentYear,
                'attributes' => array(
                    'options' => $allYears,
                ),
            )
        );
    }

    private function getYears()
    {
        $currentYear = date('Y');
        $years = array();
        for ($i = -1; $i < 20; $i++) {
            $year = $currentYear - $i;
            $years[$year] = $year;
        }

        return array($currentYear, $years);
    }
}
