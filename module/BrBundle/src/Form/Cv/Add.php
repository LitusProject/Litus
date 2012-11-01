<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Form\Cv;

use CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\Users\People\Academic,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * The form used to add a new cv
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{

    /**
     * The maximum number of additional degrees.
     */
    const MAX_DEGREES = 3;

    /**
     * The entity manager.
     */
    private $_entityManager;

    /**
     * The academic this form is for.
     */
    private $_academic;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Academic $academic, AcademicYear $year, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_academic = $academic;

        $studiesMap = array();
        $studies = $entityManager->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $year);
        foreach($studies as $study) {
            $studiesMap[$study->getStudy()->getId()] = $study->getStudy()->getFullTitle();
        }

        // TODO anticipate people that don't have their studies filled in correctly
        // TODO: set character limit on EVERY manual field
        $field = new Select('studies');
        $field->setLabel('Primary Degree')
            ->setAttribute('options', $studiesMap);
        $this->add($field);

        // TODO: results

        $field = new Text('highschool_studies');
        $field->setLabel('High School Studies')
            ->setRequired();
        $this->add($field);

        for ($i = 0; $i < $this::MAX_DEGREES; $i++) {
            $field = new Text('additional_degrees' . $i);
            if ($i == 0)
                $field->setLabel('Additional Degrees (Max. 3)');
            $this->add($field);
        }

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'btn btn-primary');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'test',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
