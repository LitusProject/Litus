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

namespace SyllabusBundle\Form\Admin\Subject\Study;

use CommonBundle\Entity\General\AcademicYear,
    SyllabusBundle\Component\Validator\Subject\Study as StudyValidator,
    SyllabusBundle\Entity\Subject,
    SyllabusBundle\Entity\StudySubjectMap;

/**
 * Add Study to Subject
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'SyllabusBundle\Hydrator\StudySubjectMap';

    /**
     * @var StudySubjectMap|null
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
            throw new LogicException('No subject was given to add a study to');
        }
        if (null === $this->academicYear) {
            throw new LogicException('No academic year was given');
        }

        parent::init();

        $this->add(array(
            'type'       => 'hidden',
            'name'       => 'study_id',
            'attributes' => array(
                'id' => 'studyId',
            ),
            'options'    => array(
                'input' => array(
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'study',
            'label'      => 'Study',
            'required'   => true,
            'attributes' => array(
                'autocomplete' => 'off',
                'data-provide' => 'typeahead',
                'id'           => 'studySearch',
                'style'        => 'width: 400px;',
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new StudyValidator($this->getEntityManager(), $this->subject, $this->academicYear),
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
     * @param  StudySubjectMap $map
     * @return self
     */
    public function setMapping(StudySubjectMap $map)
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
