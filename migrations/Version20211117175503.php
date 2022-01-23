<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20211117175503
 */
class Version20211117175503 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE br_events_companies_attendee_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE br_events_subscriptions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE br_events_companies_metadata_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE br_events_companies_attendee (id BIGINT NOT NULL, first_name TEXT NOT NULL, last_name TEXT NOT NULL, email TEXT NOT NULL, phone_number TEXT DEFAULT NULL, lunch BOOLEAN NOT NULL, veggie BOOLEAN NOT NULL, companyMap_id BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8D3AF51899A014AB ON br_events_companies_attendee (companyMap_id)');
        $this->addSql('CREATE TABLE br_events_subscriptions (id BIGINT NOT NULL, event_id BIGINT DEFAULT NULL, first_name TEXT NOT NULL, last_name TEXT NOT NULL, email TEXT NOT NULL, phone_number TEXT DEFAULT NULL, university TEXT NOT NULL, study TEXT NOT NULL, specialization TEXT DEFAULT NULL, study_year TEXT NOT NULL, food TEXT NOT NULL, qr_code TEXT NOT NULL, network_reception BOOLEAN NOT NULL, consent BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_18F970587D8B1FB5 ON br_events_subscriptions (qr_code)');
        $this->addSql('CREATE INDEX IDX_18F9705871F7E88B ON br_events_subscriptions (event_id)');
        $this->addSql('CREATE TABLE br_events_companies_metadata (id BIGINT NOT NULL, master_interests TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE br_events_companies_attendee ADD CONSTRAINT FK_8D3AF51899A014AB FOREIGN KEY (companyMap_id) REFERENCES br_events_companies_map (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_events_subscriptions ADD CONSTRAINT FK_18F9705871F7E88B FOREIGN KEY (event_id) REFERENCES br_events (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
