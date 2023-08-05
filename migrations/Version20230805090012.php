<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20230805090012
 */
class Version20230805090012 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE mail_sections_groups_id_seq CASCADE');
        $this->addSql('CREATE TABLE mail_sections (id BIGINT NOT NULL, name VARCHAR(255) NOT NULL, attribute VARCHAR(255) NOT NULL, default_value BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE users_preferences (id BIGINT NOT NULL, person BIGINT DEFAULT NULL, section BIGINT DEFAULT NULL, value BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1E849A0734DCD176 ON users_preferences (person)');
        $this->addSql('CREATE INDEX IDX_1E849A072D737AEF ON users_preferences (section)');
        $this->addSql('ALTER TABLE users_preferences ADD CONSTRAINT FK_1E849A0734DCD176 FOREIGN KEY (person) REFERENCES users_people_academic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users_preferences ADD CONSTRAINT FK_1E849A072D737AEF FOREIGN KEY (section) REFERENCES mail_sections (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
