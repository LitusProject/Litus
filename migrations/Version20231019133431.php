<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20231019133431
 */
class Version20231019133431 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE shop_reservations_bans_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE shop_reservations_bans (id BIGINT NOT NULL, person_username VARCHAR(50) DEFAULT NULL, startTimestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, endTimestamp TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_197F9CC06C18A340 ON shop_reservations_bans (person_username)');
        $this->addSql('ALTER TABLE shop_reservations_bans ADD CONSTRAINT FK_197F9CC06C18A340 FOREIGN KEY (person_username) REFERENCES users_people (username) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
