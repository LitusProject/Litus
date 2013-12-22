<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Authentication\Adapter\Doctrine;

use CommonBundle\Component\Authentication\Result\Doctrine as Result,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\QueryBuilder;

/**
 * An authentication adapter using Doctrine.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Credential extends \CommonBundle\Component\Authentication\Adapter\Doctrine
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param string $entityName The name of the class in the model that has the authentication information
     * @param string $identityColumn The name of the column that holds the identity
     * @param bool $caseSensitive Whether or not the username check is case-sensitive
     * @throws \CommonBundle\Component\Authentication\Adapter\Exception\InvalidArgumentException The entity name cannot have a leading backslash
     */
    public function __construct(EntityManager $entityManager, $entityName, $identityColumn, $caseSensitive = false)
    {
        parent::__construct($entityManager, $entityName, $identityColumn, $caseSensitive);
    }

    /**
     * Create the Doctrine query.
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createQuery()
    {
        $query = new QueryBuilder($this->getEntityManager());
        $query->from($this->getEntityName(), 'u')
            ->select('u');

        if ($this->getCaseSensitive()) {
            $query->where('u.' . $this->getIdentityColumn() . ' = :identity')
                ->setParameter('identity', $this->getIdentity());
        } else {
            $query->where('TRIM(LOWER(u.' . $this->getIdentityColumn() . ')) = :identity')
                ->setParameter('identity', trim(strtolower($this->getIdentity())));
        }

        return $query;
    }

    /**
     * Validate the query result: check the credential.
     */
    protected function validatePersonObject()
    {
        if (!$this->getPersonObject()->validateCredential($this->getCredential())) {
            $this->setAuthenticationResult(
                array(
                    'code' => Result::FAILURE_CREDENTIAL_INVALID,
                    'messages' => array(
                        'The supplied credential is invalid'
                    ),
                    'personObject' => $this->getPersonObject()
                )
            );
        }
        else if (!$this->getPersonObject()->canLogin() || $this->getPersonObject()->getCode() !== null) {
            $this->setAuthenticationResult(
                array(
                    'code' => Result::FAILURE,
                    'messages' => array(
                        'The given identity cannot login'
                    ),
                    'personObject' => $this->getPersonObject()
                )
            );
        } else {
            $credential = $this->getPersonObject()->getCredential();
            if ($credential->shouldUpdate()) {
                $credential->update($this->getCredential());
                $this->getEntityManager()->flush();
            }

            $this->setAuthenticationResult(
                array(
                    'code' => Result::SUCCESS,
                    'identity' => $this->getIdentity(),
                    'message' => array(
                        'Authentication successful'
                    ),
                    'personObject' => $this->getPersonObject()
                )
            );
        }
    }
}
