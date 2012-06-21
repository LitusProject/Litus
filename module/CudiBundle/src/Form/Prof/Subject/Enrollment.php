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
 
namespace CudiBundle\Form\Prof\Subject;

use CommonBundle\Component\Form\Bootstrap\Element\Submit,
	CommonBundle\Component\Form\Bootstrap\Element\Text,
	SyllabusBundle\Entity\StudentEnrollment,
	Zend\Validator\Int as IntValidator;

/**
 * Update student enrollment
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Enrollment extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function __construct(StudentEnrollment $enrollment = null, $options = null)
    {
        parent::__construct($options);
		 
		$field = new Text('students');
		$field->setLabel('Students')
		    ->setAttrib('class', $field->getAttrib('class') . ' input-xlarge')
			->setAttrib('autocomplete', 'off')
			->addValidator(new IntValidator())
			->setRequired();
		$this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Update');
        $this->addElement($field);
        
        $this->setActionsGroup(array('submit'));
        
        if (isset($enrollment)) {
            $this->populate(array(
                'students' => $enrollment->getNumber(),
            ));
        }
    }
}
