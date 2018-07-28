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

namespace SyllabusBundle\Form\Admin\Subject\ModuleGroup;

use CommonBundle\Entity\General\AcademicYear,
    SyllabusBundle\Entity\Study\SubjectMap,
    SyllabusBundle\Entity\Subject;

/**
 * Add ModuleGroup to Subject
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'SyllabusBundle\Hydrator\Study\SubjectMap';

    /**
     * @var SubjectMap|null
     */
    protected $mapping = null;

    /**
     * @var Subject
     */
    private $subject;

    /**
     * @var AcademicYear
     */
    private $academicYear;

    public function init()
    {
        if (null === $this->subject) {
            throw new LogicException('No subject was given to add a module group to');
        }
        if (null === $this->academicYear) {
            throw new LogicException('No academic year was given');
        }

        parent::init();

        $this->add(array(
            'type'       => 'typeahead',
            'name'       => 'module_group',
            'label'      => 'Module Group',
            'required'   => true,
            'attributes' => array(
                'id'    => 'module_group',
                'style' => 'width: 400px;',
            ),
            'options' => array(
                'input' => array(
                    'validators' => array(
                        array(
                            'name'    => 'syllabus_subject_module-group',
                            'options' => array(
                                'subject'       => $this->subject,
                                'academic_year' => $this->academicYear,
                            ),
                        ),
                        array(
                            'name' => 'syllabus_typeahead_study_module-group',
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'mandatory',
            'label' => 'Mandatory',
        ));

        $this->addSubmit('Add', 'add');
    }

    /**
     * @param  Subject $subject
     * @return self
     */
    public function setSubject(Subject $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @param  SubjectMap $map
     * @return self
     */
    public function setMapping(SubjectMap $map)
    {
        $this->mapping = $map;

        $this->setSubject($map->getSubject())
            ->setAcademicYear($map->getAcademicYear());

        return $this;
    }

    /**
     * @param  AcademicYear $academicYear
     * @return self
     */
    public function setAcademicYear(AcademicYear $academicYear)
    {
        $this->academicYear = $academicYear;

        return $this;
    }
}
