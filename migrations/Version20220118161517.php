<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20220118161517
 */
class Version20220118161517 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE br_events_companies_location_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE br_events_companies_location (id BIGINT NOT NULL, company_id BIGINT DEFAULT NULL, event_id BIGINT DEFAULT NULL, number BIGINT NOT NULL, x BIGINT NOT NULL, y BIGINT NOT NULL, orientation TEXT NOT NULL, type TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C2F4A9B4979B1AD6 ON br_events_companies_location (company_id)');
        $this->addSql('CREATE INDEX IDX_C2F4A9B471F7E88B ON br_events_companies_location (event_id)');
        $this->addSql('ALTER TABLE br_events_companies_location ADD CONSTRAINT FK_C2F4A9B4979B1AD6 FOREIGN KEY (company_id) REFERENCES br_companies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_events_companies_location ADD CONSTRAINT FK_C2F4A9B471F7E88B FOREIGN KEY (event_id) REFERENCES br_events (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function down(Schema $schema) : void
    {
        $this->throwIrreversibleMigrationException();
    }
}
