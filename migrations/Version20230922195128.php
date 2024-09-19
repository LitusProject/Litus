<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20230922195128
 */
class Version20230922195128 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE users_preferences DROP CONSTRAINT fk_1e849a072d737aef');
        $this->addSql('ALTER TABLE mail_sections DROP CONSTRAINT fk_f64182d0fa9723a1');
        $this->addSql('DROP SEQUENCE mail_sections_groups_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mail_sections_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_preferences_id_seq CASCADE');
        $this->addSql('CREATE TABLE mail_preferences (id BIGINT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, attribute VARCHAR(255) NOT NULL, default_value BOOLEAN NOT NULL, is_newsletter BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE users_preference_map (id BIGINT NOT NULL, person BIGINT DEFAULT NULL, preference BIGINT DEFAULT NULL, value BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1D7D890F34DCD176 ON users_preference_map (person)');
        $this->addSql('CREATE INDEX IDX_1D7D890F5D69B053 ON users_preference_map (preference)');
        $this->addSql('ALTER TABLE users_preference_map ADD CONSTRAINT FK_1D7D890F34DCD176 FOREIGN KEY (person) REFERENCES users_people_academic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users_preference_map ADD CONSTRAINT FK_1D7D890F5D69B053 FOREIGN KEY (preference) REFERENCES mail_preferences (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE users_preferences');
        $this->addSql('DROP TABLE mail_sections');
        $this->addSql('DROP TABLE mail_sections_groups');
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
