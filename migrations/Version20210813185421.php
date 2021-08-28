<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20210813185421
 */
class Version20210813185421 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE br_events ADD description_for_companies TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE br_events ADD nb_companies INT');
        $this->addSql('ALTER TABLE br_events ADD nb_students INT');
        $this->addSql('ALTER TABLE br_events ADD visible_for_companies BOOLEAN');
        $this->addSql('ALTER TABLE br_events ADD visible_for_students BOOLEAN');
        $this->addSql('ALTER TABLE br_events ADD location TEXT');
        $this->addSql('ALTER TABLE br_events ADD audience TEXT');
        $this->addSql('ALTER TABLE br_events RENAME COLUMN description TO description_for_students');
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
