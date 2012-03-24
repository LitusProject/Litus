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
 
namespace CudiBundle\Form\Admin\Supplier;

use CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	CommonBundle\Entity\Users\Person,
	Doctrine\ORM\EntityManager,
	Zend\Form\Element\Select;

class EditUser extends \CommonBundle\Form\Admin\User\Edit
{

	/**
	 * @var \Doctrine\ORM\EntityManager The EntityManager instance
	 */
	protected $_entityManager = null;

    public function __construct(EntityManager $entityManager, Person $person, $opts = null)
    {
        parent::__construct($entityManager, $person, $opts);
        
        $this->removeElement('roles');
    }
}