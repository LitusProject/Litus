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
 
namespace CudiBundle\Form\Queue;

use CommonBundle\Component\Validator\ValidUsername as UsernameValidator,
	Doctrine\ORM\EntityManager,
	TwitterBootstrapFormDecorators\Form\Element\Submit,
	TwitterBootstrapFormDecorators\Form\Element\Text;
	
class SignIn extends \TwitterBootstrapFormDecorators\Form\Form
{
    public function __construct(EntityManager $entityManager, $opts = null )
    {
        parent::__construct($opts);

        $field = new Text('username');
        $field->setLabel('Student Number')
            ->setRequired()
			->addValidator(new UsernameValidator($entityManager));
        $this->addElement($field);
        
        $field = new Submit('submit');
        $field->setLabel('Sign In');
        $this->addElement($field);
    }
}
