<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20220130175406
 */
class Version20220130175406 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE br_events_companies_attendee_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE br_events_companies_metadata_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ticket_transactions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE br_events_companies_attendee (id BIGINT NOT NULL, first_name TEXT NOT NULL, last_name TEXT NOT NULL, email TEXT NOT NULL, phone_number TEXT DEFAULT NULL, lunch BOOLEAN NOT NULL, veggie BOOLEAN NOT NULL, companyMap_id BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8D3AF51899A014AB ON br_events_companies_attendee (companyMap_id)');
        $this->addSql('CREATE TABLE br_events_companies_metadata (id BIGINT NOT NULL, master_interests TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE br_events_companies_attendee ADD CONSTRAINT FK_8D3AF51899A014AB FOREIGN KEY (companyMap_id) REFERENCES br_events_companies_map (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_feature ADD sector BOOLEAN DEFAULT \'false\' NOT NULL');
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
