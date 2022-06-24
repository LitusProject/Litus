<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20220624160640
 */
class Version20220624160640 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE ticket_guests_info ADD phone_number VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket_guests_info ADD address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket_guests_info ADD studies VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket_guests_info ADD foodOptions VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket_guests_info ADD allergies VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket_guests_info ADD transportation VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket_guests_info ADD comments VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket_guests_info ADD picture VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9FAC156A16DB4F89 ON ticket_guests_info (picture)');
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
