<?php

namespace Litus\Controller;

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