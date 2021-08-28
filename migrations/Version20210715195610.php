<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20210715195610
 */
class Version20210715195610 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');


        $this->addSql('ALTER TABLE br_products DROP CONSTRAINT fk_d2817b23bae0aa7');
        $this->addSql('DROP INDEX idx_eaf0c0243bae0aa7');
        $this->addSql('ALTER TABLE br_products DROP event');
        $this->addSql('ALTER TABLE br_events_companies_map ADD done BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE br_products ADD br_event BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE br_products ADD CONSTRAINT FK_EAF0C0248C4FFE35 FOREIGN KEY (br_event) REFERENCES br_events (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_EAF0C0248C4FFE35 ON br_products (br_event)');
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
