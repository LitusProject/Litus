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
 
namespace CommonBundle\Component\Controller;

/**
 * Ensuring database access for all our controllers.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
interface DoctrineAware
{
    /**
     * Singleton implementation for the Entity Manager, retrieved
     * from the Zend Registry.
     *
     * @return \Doctrine\ORM\EntityManager
     */
	public function getEntityManager();
}