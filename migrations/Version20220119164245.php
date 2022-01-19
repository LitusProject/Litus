<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20220119164245
 */
class Version20220119164245 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE br_events_subscriptions ADD other_university TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE br_events_subscriptions ADD other_study TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE br_events_subscriptions ALTER food DROP NOT NULL');
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
