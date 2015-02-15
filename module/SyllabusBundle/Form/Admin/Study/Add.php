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

namespace SyllabusBundle\Form\Admin\Study;

use SyllabusBundle\Entity\Study;

/**
 * Add Study
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'SyllabusBundle\Hydrator\Study';

    /**
     * @var Study|null
     */
    protected $study = null;

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'     => 'text',
            'name'     => 'kul_id',
            'label'    => 'KU Leuven Id',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'int'),
                        array(
                            'name' => 'syllabus_study_kul_id',
                            'options' => array(
                                'study' => $this->study,
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'text',
            'name'     => 'title',
            'label'    => 'Title',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'text',
            'name'     => 'phase',
            'label'    => 'Phase',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'text',
            'name'     => 'language',
            'label'    => 'Language',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $validators = array(
            array('name' => 'syllabus_typeahead_study'),
        );

        if (null !== $this->study) {
            $validators[] = array(
                'name' => 'syllabus_study_recursion',
                'options' => array(
                    'study' => $this->study,
                ),
            );
        }

        $this->add(array(
            'type'       => 'typeahead',
            'name'       => 'parent',
            'label'      => 'Parent',
            'required'   => true,
            'attributes' => array(
                'style'        => 'width: 400px',
            ),
            'options'    => array(
                'input' => array(
                    'validators' => $validators,
                ),
            ),
        ));

        $this->addSubmit('Add', 'add');
    }

    /**
     * @param  Study $study
     * @return self
     */
    public function setStudy(Study $study)
    {
        $this->study = $study;

        return $this;
    }
}
