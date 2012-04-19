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
 
namespace CommonBundle\Form\Admin\Academic;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Entity\Users\Person,
	Doctrine\ORM\EntityManager,
	Zend\Form\Element\Submit;

/**
 * EditUser
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \CommonBundle\Form\Admin\Person\Edit
{
	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
	 * @param \CommonBundle\Entity\Users\Person $person The person we're going to modify
	 * @param mixed $opts The form's options
	 */
    public function __construct(EntityManager $entityManager, Person $person, $opts = null)
    {
        parent::__construct($entityManager, $person, $opts);

     	$field = new Submit('submit');
        $field->setLabel('Save')
            ->setAttrib('class', 'academic_edit')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
        
        $this->populate(
            array(
                'university_identification' => $person->getUniversityIdentification()
            )
        );
    }
}
