<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20220120153742
 */
class Version20220120153742 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE br_events_companies_map DROP CONSTRAINT fk_1e92e166dc9ee959');
        $this->addSql('DROP SEQUENCE br_events_companies_metadata_id_seq CASCADE');
        $this->addSql('DROP TABLE br_events_companies_metadata');
        $this->addSql('DROP INDEX idx_1e92e166dc9ee959');
        $this->addSql('ALTER TABLE br_events_companies_map ADD masterInterests TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE br_events_companies_map ADD notes TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE br_events_companies_map DROP metadata_id');
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
