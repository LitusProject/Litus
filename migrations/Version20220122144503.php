<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20220122144503
 */
class Version20220122144503 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE br_events_companies_attendee_id_seq CASCADE');
        $this->addSql('DROP TABLE br_events_companies_attendee');
        $this->addSql('ALTER TABLE br_events_companies_map ADD attendees BIGINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE br_events_visitors ADD event_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE br_events_visitors ADD CONSTRAINT FK_22ACA2AF71F7E88B FOREIGN KEY (event_id) REFERENCES br_events (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_22ACA2AF71F7E88B ON br_events_visitors (event_id)');
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
