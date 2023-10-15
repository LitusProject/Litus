<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20231015200531
 */
class Version20231015200531 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE shop_sessions_opening_hours_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE shop_sessions_opening_hours_translations_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE shop_sessions_opening_hours (id BIGINT NOT NULL, person BIGINT DEFAULT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, creation_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B036599134DCD176 ON shop_sessions_opening_hours (person)');
        $this->addSql('CREATE TABLE shop_sessions_opening_hours_translations (id BIGINT NOT NULL, opening_hour BIGINT DEFAULT NULL, language BIGINT DEFAULT NULL, comment VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BAEC889D969BD765 ON shop_sessions_opening_hours_translations (opening_hour)');
        $this->addSql('CREATE INDEX IDX_BAEC889DD4DB71B5 ON shop_sessions_opening_hours_translations (language)');
        $this->addSql('ALTER TABLE shop_sessions_opening_hours ADD CONSTRAINT FK_B036599134DCD176 FOREIGN KEY (person) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shop_sessions_opening_hours_translations ADD CONSTRAINT FK_BAEC889D969BD765 FOREIGN KEY (opening_hour) REFERENCES shop_sessions_opening_hours (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shop_sessions_opening_hours_translations ADD CONSTRAINT FK_BAEC889DD4DB71B5 FOREIGN KEY (language) REFERENCES general_languages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
