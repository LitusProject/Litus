<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20220118170252
 */
class Version20220118170252 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE br_events_companies_location DROP CONSTRAINT fk_c2f4a9b471f7e88b');
        $this->addSql('DROP INDEX idx_c2f4a9b471f7e88b');
        $this->addSql('ALTER TABLE br_events_companies_location RENAME COLUMN event_id TO event_entity');
        $this->addSql('ALTER TABLE br_events_companies_location ADD CONSTRAINT FK_C2F4A9B43CB1333A FOREIGN KEY (event_entity) REFERENCES br_events (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_C2F4A9B43CB1333A ON br_events_companies_location (event_entity)');
        $this->addSql('CREATE UNIQUE INDEX number_event_unique ON br_events_companies_location (event_entity, number)');
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
