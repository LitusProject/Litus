<?php

namespace CommonBundle\Component\Authentication\Adapter;

use CommonBundle\Component\Authentication\Result\Doctrine as Result;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

/**
 * An authentication adapter using Doctrine.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
abstract class Doctrine implements \Laminas\Authentication\Adapter\AdapterInterface
{
    /**
     * @var EntityManager The EntityManager instance
     */
    private $entityManager = null;

    /**
     * @var string The name of the entity that holds the authentication information
     */
    private $entityName = '';

    /**
     * @var string The name of the column that holds the identity
     */
    private $identityColumn = '';

    /**
     * @var boolean Whether or not the username check is case-sensitive
     */
    private $caseSensitive = false;

    /**
     * @var string The identity value that should be checked
     */
    private $identity = '';

    /**
     * @var string The credential value that should be checked
     */
    private $credential = '';

    /**
     * @var array The result of the authentication (with extended info)
     */
    private $authenticationResult = array();

    /**
     * @var \CommonBundle\Entity\User\Person The object returned by our DQL query
     */
    private $personObject = null;

    /**
     * @param  EntityManager $entityManager  The EntityManager instance
     * @param  string        $entityName     The name of the class in the model that has the authentication information
     * @param  string        $identityColumn The name of the column that holds the identity
     * @param  boolean       $caseSensitive  Whether or not the username check is case-sensitive
     * @throws Exception\InvalidArgumentException The entity name cannot have a leading backslash
     */
    public function __construct(EntityManager $entityManager, $entityName, $identityColumn, $caseSensitive = false)
    {
        $this->entityManager = $entityManager;

        // A bit of a dirty hack to get Laminas's DI to play nice
        $entityName = str_replace('"', '', $entityName);

        if (substr($entityName, 0, 1) == '\\') {
            throw new Exception\InvalidArgumentException(
                'The entity name cannot have a leading backslash'
            );
        }
        $this->entityName = $entityName;

        $this->identityColumn = $identityColumn;
        $this->caseSensitive = $caseSensitive;

        $this->setAuthenticationResult(
            array(
                'code'         => Result::FAILURE,
                'identity'     => '',
                'messages'     => array(),
                'personObject' => null,
            )
        );
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @return string
     */
    protected function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * @return string
     */
    protected function getIdentityColumn()
    {
        return $this->identityColumn;
    }

    /**
     * @return boolean
     */
    protected function getCaseSensitive()
    {
        return $this->caseSensitive;
    }

    /**
     * @param  string $identity
     * @return self
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @param  string $credential
     * @return self
     */
    public function setCredential($credential)
    {
        $this->credential = $credential;

        return $this;
    }

    /**
     * @return string
     */
    protected function getCredential()
    {
        return $this->credential;
    }

    /**
     * @param array $authenticationResult
     */
    protected function setAuthenticationResult(array $authenticationResult)
    {
        $this->authenticationResult = array_merge(
            $this->authenticationResult,
            $authenticationResult
        );
    }

    /**
     * @return \CommonBundle\Entity\User\Person
     */
    protected function getPersonObject()
    {
        return $this->personObject;
    }

    /**
     * Authenticate the user.
     *
     * @return Result
     */
    public function authenticate()
    {
        $this->executeQuery(
            $this->createQuery()
        );

        return $this->createResult();
    }

    /**
     * Execute the DQL query.
     *
     * @param  QueryBuilder $query The DQL query that should be executed
     * @return void
     * @throws Exception\QueryFailedException The adapter failed to execute the query
     */
    protected function executeQuery(QueryBuilder $query)
    {
        try {
            $resultSet = $query->getQuery()->getResult();
        } catch (\Throwable $e) {
            throw new Exception\QueryFailedException(
                'The adapter failed to execute the query',
                0,
                $e
            );
        }

        $this->validateResultSet($resultSet);
    }

    /**
     * Create the Doctrine query.
     *
     * @return QueryBuilder
     */
    abstract protected function createQuery();

    /**
     * Validate the query result: check the number of results.
     *
     * @param  array $resultSet The result set of the DQL query
     * @return Result|void
     */
    protected function validateResultSet(array $resultSet)
    {
        if (count($resultSet) < 1) {
            $this->setAuthenticationResult(
                array(
                    'code'     => Result::FAILURE_IDENTITY_NOT_FOUND,
                    'messages' => array(
                        'A record with the supplied identity could not be found',
                    ),
                )
            );
        } elseif (count($resultSet) > 1) {
            $this->setAuthenticationResult(
                array(
                    'code'     => Result::FAILURE_IDENTITY_AMBIGUOUS,
                    'messages' => array(
                        'More than one record matches the supplied identity',
                    ),
                )
            );
        } else {
            $this->personObject = $resultSet[0];
            $this->validatePersonObject();
        }
    }

    /**
     * Validate the query result: check the credential.
     *
     * @return null
     */
    abstract protected function validatePersonObject();

    /**
     * Create the authentication result.
     *
     * @return Result
     */
    protected function createResult()
    {
        return new Result(
            $this->authenticationResult['code'],
            $this->authenticationResult['identity'],
            $this->authenticationResult['messages'],
            $this->authenticationResult['personObject']
        );
    }
}
