<?php
declare(strict_types=1);

namespace Migrations;

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use Doctrine\DBAL\Schema\Schema;
use DoorBundle\Entity\Rule;

/**
 * Version 20190419195621
 */
class Version20190419195621 extends \Doctrine\Migrations\AbstractMigration implements ServiceLocatorAwareInterface
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
            ->selectCollection($database, 'doorbundle_rules')
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

        $this->addSql('CREATE SEQUENCE door_rules_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE door_rules (id BIGINT NOT NULL, academic BIGINT DEFAULT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, start_time INT NOT NULL, end_time INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_7800251f40549b08 ON door_rules (academic)');
        $this->addSql('ALTER TABLE door_rules ADD CONSTRAINT fk_7800251f40549b08 FOREIGN KEY (academic) REFERENCES users_people_academic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
            $academic = $this->getServiceLocator()
                ->get('doctrine.entitymanager.orm_default')
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneById($document['academic']);

            $rule = new Rule($academic);
            $rule->setStartDate($document['startDate']->toDateTime());
            $rule->setEndDate($document['endDate']->toDateTime());
            $rule->setStartTime($document['startTime']);
            $rule->setEndTime($document['endTime']);

            $this->getServiceLocator()
                ->get('doctrine.entitymanager.orm_default')
                ->persist($rule);
        }

        $this->getServiceLocator()
            ->get('doctrine.entitymanager.orm_default')
            ->flush();
    }
}
