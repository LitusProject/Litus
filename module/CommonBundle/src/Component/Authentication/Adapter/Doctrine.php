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
 
namespace CommonBundle\Component\Authentication\Adapter;

use CommonBundle\Component\Authentication\Result\Doctrine as Result,
	Doctrine\ORM\EntityManager,
	Doctrine\ORM\QueryBuilder;

/**
 * An authentication adapter using Doctrine.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
abstract class Doctrine implements \Zend\Authentication\Adapter
{
	/**
	 * @var \Doctrine\ORM\EntityManager The EntityManager instance
	 */
	private $_entityManager = null;

    /**
     * @var string The name of the entity that holds the authentication information
     */
    private $_entityName = '';

    /**
     * @var string The name of the column that holds the identity
     */
    private $_identityColumn = '';

    /**
     * @var bool Whether or not the username check is case-sensitive
     */
    private $_caseSensitive = false;

    /**
     * @var string The identity value that should be checked
     */
    private $_identity = '';

    /**
     * @var string The credential value that should be checked
     */
    private $_credential = '';

    /**
     * @var array The result of the authentication (with extended info)
     */
    private $_authenticationResult = array();

    /**
     * @var \Litus\Entity\Users\Person The object returned by our DQL query
     */
    private $_personObject = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param string $entityName The name of the class in the model that has the authentication information
     * @param string $identityColumn The name of the column that holds the identity
     * @param bool $caseSensitive Whether or not the username check is case-sensitive
     * @throws \CommonBundle\Component\Authentication\Adapter\Exception\InvalidArgumentException The entity name cannot have a leading backslash
     */
    public function __construct(EntityManager $entityManager, $entityName, $identityColumn, $caseSensitive = false)
    {
    	$this->_entityManager = $entityManager;
    	
    	// A bit of a dirty hack to get Zend's DI to play nice
    	$entityName = str_replace('"', '', $entityName);
    	
        if ('\\' == substr($entityName, 0, 1)) {
            throw new Exception\InvalidArgumentException(
                'The entity name cannot have a leading backslash'
            );
        }
        $this->_entityName = $entityName;
        
        $this->_identityColumn = $identityColumn;
        $this->_caseSensitive = $caseSensitive;
        
        $this->setAuthenticationResult(
        	array(
        		'code' => Result::FAILURE,
        		'identity' => '',
        		'messages' => array(),
        		'personObject' => null
        	)
        );
    }
	
	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	protected function getEntityManager()
	{
		return $this->_entityManager;
	}
	
	/**
	 * @return string
	 */
	protected function getEntityName()
	{
		return $this->_entityName;
	}
	
	/**
	 * @return string
	 */
	protected function getIdentityColumn()
	{
		return $this->_identityColumn;
	}
	
	/**
	 * @return bool
	 */
	protected function getCaseSensitive()
	{
		return $this->_caseSensitive;
	}
	
    /**
     * @param string $identity
     * @return \CommonBundle\Component\Authentication\Adapter\Doctrine
     */
    public function setIdentity($identity)
    {
        $this->_identity = $identity;
        return $this;
    }

	/**
	 * @return string
	 */
	protected function getIdentity()
	{
		return $this->_identity;
	}

    /**
     * @param string $credential
     * @return \CommonBundle\Component\Authentication\Adapter\Doctrine
     */
    public function setCredential($credential)
    {
        $this->_credential = $credential;
        return $this;
    }
    
    /**
     * @return string
     */
    protected function getCredential()
    {
    	return $this->_credential;
    }
    
    /**
     * @param array $authenticationResult
     */
	protected function setAuthenticationResult(array $authenticationResult)
	{
		$this->_authenticationResult = array_merge(
			$this->_authenticationResult,
			$authenticationResult
		);
	}
	
	/**
	 * @return \Litus\Entity\Users\Person
	 */
	protected function getPersonObject()
	{
		return $this->_personObject;
	}
    
    /**
     * Execute the DQL query.
     *
     * @param \Doctrine\ORM\QueryBuilder $query The DQL query that should be executed
     * @return void
     * @throws \CommonBundle\Component\Authentication\Adapter\Exception\QueryFailedException The adapter failed to execute the query
     */
    protected function executeQuery(QueryBuilder $query)
    {
        try {
            $resultSet = $query->getQuery()->getResult();
        } catch (\Exception $e) {
            throw new Exception\QueryFailedException(
                'The adapter failed to execute the query', 0, $e
            );
        }

        $this->validateResultSet($resultSet);
    }

    /**
     * Create the Doctrine query.
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    abstract protected function createQuery();

    /**
     * Validate the query result: check the number of results.
     *
     * @param array $resultSet The result set of the DQL query
     * @return \Litus\Authentication\Result|void
     */
    protected function validateResultSet(array $resultSet)
    {
        if (count($resultSet) < 1) {
            $this->setAuthenticationResult(
            	array(
            		'code' => Result::FAILURE_IDENTITY_NOT_FOUND,
            		'messages' => array(
            			'A record with the supplied identity could not be found'
            		)
            	)
            );
        } elseif (count($resultSet) > 1) {
            $this->setAuthenticationResult(
            	array(
            		'code' => Result::FAILURE_IDENTITY_AMBIGUOUS,
            		'messages' => array(
            			'More than one record matches the supplied identity'
            		)
            	)
            );
        } else {
            $this->_personObject = $resultSet[0];
            $this->validatePersonObject();
        }
    }

    /**
     * Validate the query result: check the credential.
     *
     * @return \Litus\Authentication\Result
     */
    abstract protected function validatePersonObject();

    /**
     * Create the authentication result.
     *
     * @return \Litus\Authentication\Result
     */
    protected function createResult()
    {
        return new Result(
            $this->_authenticationResult['code'],
            $this->_authenticationResult['identity'],
            $this->_authenticationResult['messages'],
            $this->_authenticationResult['personObject']
        );
    }
}
