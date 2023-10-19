<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20231019134846
 */
class Version20231019134846 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE shop_reservations_bans DROP CONSTRAINT fk_197f9cc06c18a340');
        $this->addSql('DROP INDEX idx_197f9cc06c18a340');
        $this->addSql('ALTER TABLE shop_reservations_bans ADD person BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE shop_reservations_bans DROP person_username');
        $this->addSql('ALTER TABLE shop_reservations_bans ADD CONSTRAINT FK_197F9CC034DCD176 FOREIGN KEY (person) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_197F9CC034DCD176 ON shop_reservations_bans (person)');
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
