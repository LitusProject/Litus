<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20210919182550
 */
class Version20210919182550 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE logistics_consumptions (id BIGINT NOT NULL, person BIGINT DEFAULT NULL, number_of_consumptions INT DEFAULT NULL, username VARCHAR(50), name VARCHAR(50), PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C4E0C7F234DCD176 ON logistics_consumptions (person)');
        $this->addSql('ALTER TABLE logistics_consumptions ADD CONSTRAINT FK_C4E0C7F234DCD176 FOREIGN KEY (person) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
