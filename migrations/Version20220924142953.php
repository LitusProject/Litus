<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20220924142953
 */
class Version20220924142953 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE users_preferences_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE users_preferences ADD person BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE users_preferences ALTER id TYPE BIGINT USING id::bigint');
        $this->addSql('ALTER TABLE users_preferences ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE users_preferences ALTER id TYPE BIGINT');
        $this->addSql('ALTER TABLE users_preferences ADD CONSTRAINT FK_1E849A0734DCD176 FOREIGN KEY (person) REFERENCES users_people_academic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_1E849A0734DCD176 ON users_preferences (person)');
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
