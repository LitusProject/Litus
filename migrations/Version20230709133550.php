<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20230709133550
 */
class Version20230709133550 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE frame_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE frames_translations_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE frame (id BIGINT NOT NULL, active BOOLEAN DEFAULT \'true\' NOT NULL, categoryPage BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B5F83CCDDC91ADBE ON frame (categoryPage)');
        $this->addSql('CREATE TABLE frames_translations (id BIGINT NOT NULL, frame BIGINT DEFAULT NULL, language BIGINT DEFAULT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_50836742B5F83CCD ON frames_translations (frame)');
        $this->addSql('CREATE INDEX IDX_50836742D4DB71B5 ON frames_translations (language)');
        $this->addSql('ALTER TABLE frame ADD CONSTRAINT FK_B5F83CCDDC91ADBE FOREIGN KEY (categoryPage) REFERENCES nodes_categorypages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE frames_translations ADD CONSTRAINT FK_50836742B5F83CCD FOREIGN KEY (frame) REFERENCES frame (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE frames_translations ADD CONSTRAINT FK_50836742D4DB71B5 FOREIGN KEY (language) REFERENCES general_languages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
