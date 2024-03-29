<?php

namespace CommonBundle\Component\Authentication\Adapter\Doctrine;

use CommonBundle\Component\Authentication\Result\Doctrine as Result;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

/**
 * An authentication adapter using Doctrine.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Shibboleth extends \CommonBundle\Component\Authentication\Adapter\Doctrine
{
    /**
     * @param  EntityManager $entityManager  The EntityManager instance
     * @param  string        $entityName     The name of the class in the model that has the authentication information
     * @param  string        $identityColumn The name of the column that holds the identity
     * @throws \CommonBundle\Component\Authentication\Adapter\Exception\InvalidArgumentException The entity name cannot have a leading backslash
     */
    public function __construct(EntityManager $entityManager, $entityName, $identityColumn)
    {
        parent::__construct($entityManager, $entityName, $identityColumn, false);
    }

    /**
     * Create the Doctrine query.
     *
     * @return QueryBuilder
     */
    protected function createQuery()
    {
        $query = new QueryBuilder($this->getEntityManager());
        $query->from($this->getEntityName(), 'u')
            ->select('u')
            ->where('TRIM(LOWER(u.' . $this->getIdentityColumn() . ')) = :identity')
            ->setParameter('identity', trim(strtolower($this->getIdentity())));

        return $query;
    }

    /**
     * Validate the query result: check the credential.
     *
     * @return null
     */
    protected function validatePersonObject()
    {
        if (!$this->getPersonObject()->canLogin()) {
            $this->setAuthenticationResult(
                array(
                    'code'     => Result::FAILURE,
                    'messages' => array(
                        'The given identity cannot login',
                    ),
                )
            );
        } else {
            $this->setAuthenticationResult(
                array(
                    'code'         => Result::SUCCESS,
                    'identity'     => $this->getIdentity(),
                    'message'      => array(
                        'Authentication successful',
                    ),
                    'personObject' => $this->getPersonObject(),
                )
            );
        }
    }
}
