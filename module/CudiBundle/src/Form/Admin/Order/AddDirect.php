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
 
namespace CudiBundle\Form\Admin\Order;

use Doctrine\ORM\EntityManager,
	Zend\Form\Form;

class AddDirect extends \CudiBundle\Form\Admin\Order\AddItem
{

    public function __construct(EntityManager $entityManager, $options = null)
    {
        parent::__construct($entityManager, $options);

        $this->removeElement('stockArticle');
		$this->getElement('submit')->setName('addOrder');
    }
}