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
 * An authentication adapter using Doctrine
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Doctrine implements \Zend\Authentication\Adapter
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
     */
    public function __construct(EntityManager $entityManager, $entityName, $identityColumn, $caseSensitive = false)
    {
    	$this->_entityManager = $entityManager;
    
        if ('\\' == substr($entityName, 0, 1)) {
            throw new \Litus\Authentication\Adapter\Exception\InvalidArgumentException(
                'The entity name cannot have a leading backslash'
            );
        }
        $this->_entityName = $entityName;
        
        $this->_identityColumn = $identityColumn;
        $this->_caseSensitive = $caseSensitive;
    }

    /**
     * @param string $identity
     * @return \Litus\Authentication\Adapter\Doctrine
     */
    public function setIdentity($identity)
    {
        $this->_identity = $identity;
        return $this;
    }

    /**
     * @param string $credential
     * @return \Litus\Authentication\Adapter\Doctrine
     */
    public function setCredential($credential)
    {
        $this->_credential = $credential;
        return $this;
    }

    /**
     * Authenticate the user.
     *
     * @return \Litus\Authentication\Result
     */
    public function authenticate()
    {
        $this->_setupResult();
        $this->_executeQuery(
            $this->_createQuery()
        );

        return $this->_createResult();
    }

    /**
     * Set the default authentication result.
     */
    private function _setupResult()
    {
        $this->_authenticationResult = array(
            'code' => Result::FAILURE,
            'identity' => '',
            'messages' => array(),
            'personObject' => null
        );
    }

    /**
     * Execute the DQL query.
     *
     * @throws \Zend\Authentication\Adapter\Exception If the adapter cannot execute the query
     * @param \Doctrine\ORM\QueryBuilder $query The DQL query that should be executed
     * @return void
     */
    private function _executeQuery(QueryBuilder $query)
    {
        try {
            $resultSet = $query->getQuery()->getResult();
        } catch (\Exception $e) {
            throw new \Litus\Authentication\Adapter\Exception\QueryFailedException(
                'The adapter failed to execute the query.', 0, $e
            );
        }

        $this->_validateResultSet($resultSet);
    }

    /**
     * Create the Doctrine query.
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function _createQuery()
    {
        $query = new QueryBuilder($this->_entityManager);
        $query->from($this->_entityName, 'u');
        $query->select('u');

        if ($this->_caseSensitive) {
            $query->where('u.' . $this->_identityColumn . ' = :identity');
            $query->setParameter('identity', $this->_identity);
        } else {
            $query->where('TRIM(LOWER(u.' . $this->_identityColumn . ')) = :identity');
            $query->setParameter('identity', trim(strtolower($this->_identity)));
        }

        return $query;
    }

    /**
     * Validate the query result: check the number of results.
     *
     * @param array $resultSet The result set of the DQL query
     * @return \Litus\Authentication\Result|void
     */
    private function _validateResultSet(array $resultSet)
    {
        if (count($resultSet) < 1) {
            $this->_authenticationResult['code'] = Result::FAILURE_IDENTITY_NOT_FOUND;
            $this->_authenticationResult['messages'][] = 'A record with the supplied identity could not be found';
        } elseif (count($resultSet) > 1) {
            $this->_authenticationResult['code'] = Result::FAILURE_IDENTITY_AMBIGUOUS;
            $this->_authenticationResult['messages'][] = 'More than one record matches the supplied identity';
        } else {
            $this->_personObject = $resultSet[0];
            $this->_authenticate();
        }
    }

    /**
     * Validate the query result: check the credential.
     *
     * @return \Litus\Authentication\Result
     */
    private function _authenticate()
    {
        if (!$this->_personObject->validateCredential($this->_credential)) {
            $this->_authenticationResult['code'] = Result::FAILURE_CREDENTIAL_INVALID;
            $this->_authenticationResult['messages'][] = 'Supplied credential is invalid';
        }
        else if (!$this->_personObject->canLogin()) {
            $this->_authenticationResult['code'] = Result::FAILURE;
            $this->_authenticationResult['messages'][] = 'The given identity cannot login';
        } else {
            $this->_authenticationResult['code'] = Result::SUCCESS;
            $this->_authenticationResult['messages'][] = 'Authentication successful';

            $this->_authenticationResult['identity'] = $this->_identity;
            $this->_authenticationResult['personObject'] = $this->_personObject;
        }
    }

    /**
     * Create the authentication result.
     *
     * @return \Litus\Authentication\Result
     */
    private function _createResult()
    {
        return new Result(
            $this->_authenticationResult['code'],
            $this->_authenticationResult['identity'],
            $this->_authenticationResult['messages'],
            $this->_authenticationResult['personObject']
        );
    }
}
