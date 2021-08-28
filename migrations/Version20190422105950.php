<?php
declare(strict_types=1);

namespace Migrations;

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use Doctrine\DBAL\Schema\Schema;
use ShiftBundle\Entity\Token;

/**
 * Version 20190422105950
 */
class Version20190422105950 extends \Doctrine\Migrations\AbstractMigration implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    private $documents;

    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function preUp(Schema $schema) : void
    {
        $this->abortIf(
            !$this->getServiceLocator()->has('doctrine.configuration.odm_default'),
            'Migration can only be executed when Doctrine supports document mapping.'
        );

        $database = $this->getServiceLocator()
            ->get('doctrine.configuration.odm_default')
            ->getDefaultDB();

        $this->documents = $this->getServiceLocator()
            ->get('doctrine.connection.odm_default')
            ->getMongoClient()
            ->selectCollection($database, 'shiftbundle_tokens')
            ->find();
    }

    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on PostgreSQL.'
        );

        $this->addSql('CREATE SEQUENCE shift_tokens_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE shift_tokens (id BIGINT NOT NULL, person BIGINT DEFAULT NULL, hash VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_2682839b34dcd176 ON shift_tokens (person)');
        $this->addSql('CREATE UNIQUE INDEX shift_tokens_hash ON shift_tokens (hash)');
        $this->addSql('ALTER TABLE shift_tokens ADD CONSTRAINT fk_2682839b34dcd176 FOREIGN KEY (person) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function down(Schema $schema) : void
    {
        $this->throwIrreversibleMigrationException();
    }

    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function postUp(Schema $schema) : void
    {
        $this->abortIf(
            !$this->getServiceLocator()->has('doctrine.configuration.odm_default'),
            'Migration can only be executed when Doctrine supports document mapping.'
        );

        foreach ($this->documents as $document) {
            $person = $this->getServiceLocator()
                ->get('doctrine.entitymanager.orm_default')
                ->getRepository('CommonBundle\Entity\User\Person')
                ->findOneById($document['person']);

            if ($person === null) {
                continue;
            }

            $token = new Token($person);
            $token->setHash($document['hash']);

            $this->getServiceLocator()
                ->get('doctrine.entitymanager.orm_default')
                ->persist($token);
        }

        $this->getServiceLocator()
            ->get('doctrine.entitymanager.orm_default')
            ->flush();
    }
}
