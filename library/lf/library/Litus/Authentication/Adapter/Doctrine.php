<?php
namespace Litus\Authentication\Adapter;

use \Doctrine\ORM\QueryBuilder;

use \Litus\Authentication\Result;
use \Litus\Authentication\Result\Doctrine as DoctrineResult;

class Doctrine implements \Litus\Authentication\Adapter
{
    /**
     * @var string The name of the class in the model that has the authentication information
     */
    private $_modelName = '';

    /**
     * @var string The name of the column that holds the identity
     */
    private $_identityColumn = '';

    /**
     * @var bool Whether or not the username check is case-sensitive
     */
    private $_strict = false;

    /**
     * @var string The identity that was provided
     */
    private $_identity = '';

    /**
     * @var string The credential that was provided
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
     * @var array Additional conditions for the DQL query
     */
    private $_dqlConditions = array();

    /**
     * Creating our new Doctrine adapter.
     *
     * @param string $modelName The name of the class in the model that has the authentication information
     * @param string $identityColumn The name of the column that holds the identity
     * @param bool $strict Whether or not the username check is case-sensitive
     * @param array $dqlConditions
     */
    public function __construct($modelName, $identityColumn, $strict = false, array $dqlConditions = array())
    {
        $this->_modelName = $modelName;
        $this->_identityColumn = $identityColumn;
        $this->_strict = $strict;
        $this->_dqlConditions = $dqlConditions;
    }

    /**
     * Set the provided identity.
     *
     * @param string $identity The identity that was provided
     * @return \Litus\Authentication\Adapter\DoctrineAdapter
     */
    public function setIdentity($identity)
    {
        $this->_identity = $identity;
        return $this;
    }

    /**
     * Returns true if the identity is set.
     *
     * @return bool
     */
    public function hasIdentity()
    {
        return $this->_identity != '';
    }

    /**
     * Set the provided credential.
     *
     * @param string $credential The credential that was provided
     * @return \Litus\Authentication\Adapter\DoctrineAdapter
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
        $this->_executeQuery($this->_createQuery());

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
            'credential' => '',
            'messages' => array(),
            'personObject' => null
        );
    }

    /**
     * Create the Doctrine query.
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function _createQuery()
    {
        $alias = 'u';

        $query = new \Doctrine\ORM\QueryBuilder(\Zend\Registry::get('EntityManager'));
        $query->from($this->_modelName, $alias);
        $query->select($alias);

        if ($this->_strict) {
            $query->where($alias . '.' . $this->_identityColumn . ' = ?1');
            $query->setParameter(1, $this->_identity);
        } else {
            $query->where('TRIM(LOWER(' . $alias . '.' . $this->_identityColumn . ')) = :identity');
            $query->setParameter('identity', trim(strtolower($this->_identity)));
        }

        foreach ($this->_dqlConditions as $key => $value) {
            $query->andWhere($alias . '.' . $key . ' ' . $value['operator'] . ' :' . $key);
            $query->setParameter($key, $value['value']);
        }

        return $query;
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
     * Validate the query result: check the number of results.
     *
     * @param array $resultSet The result set of the DQL query
     * @return \Litus\Authentication\Result|void
     */
    private function _validateResultSet(array $resultSet)
    {
        if (count($resultSet) < 1) {
            $this->_authenticationResult['code'] = Result::FAILURE_IDENTITY_NOT_FOUND;
            $this->_authenticationResult['messages'][] = 'A record with the supplied identity could not be found.';
        } elseif (count($resultSet) > 1) {
            $this->_authenticationResult['code'] = Result::FAILURE_IDENTITY_AMBIGUOUS;
            $this->_authenticationResult['messages'][] = 'More than one record matches the supplied identity.';
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
            $this->_authenticationResult['messages'][] = 'Supplied credential is invalid.';
        } else {
            $this->_authenticationResult['code'] = Result::SUCCESS;
            $this->_authenticationResult['messages'][] = 'Authentication successful.';

            $this->_authenticationResult['personObject'] = $this->_personObject;
            $this->_authenticationResult['credential'] = $this->_personObject->getCredential();
        }
    }

    /**
     * Create the authentication result.
     *
     * @return \Litus\Authentication\Result
     */
    private function _createResult()
    {
        return new DoctrineResult(
            $this->_authenticationResult['code'],
            $this->_authenticationResult['identity'],
            $this->_authenticationResult['messages'],
            $this->_authenticationResult['personObject']
        );
    }
}