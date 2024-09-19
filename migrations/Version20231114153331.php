<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20231114153331
 */
class Version20231114153331 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');
        
        $this->addSql('ALTER TABLE ticket_events ALTER price_members TYPE INT');
        $this->addSql('ALTER TABLE ticket_events ALTER price_members DROP DEFAULT');
        $this->addSql('ALTER TABLE ticket_events ALTER price_non_members TYPE INT');
        $this->addSql('ALTER TABLE ticket_events ALTER price_non_members DROP DEFAULT');
        $this->addSql('ALTER TABLE ticket_events_options ALTER price_members TYPE INT');
        $this->addSql('ALTER TABLE ticket_events_options ALTER price_members DROP DEFAULT');
        $this->addSql('ALTER TABLE ticket_events_options ALTER price_non_members TYPE INT');
        $this->addSql('ALTER TABLE ticket_events_options ALTER price_non_members DROP DEFAULT');
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
