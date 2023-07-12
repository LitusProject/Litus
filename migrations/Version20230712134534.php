<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20230712134534
 */
class Version20230712134534 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE frames_translations DROP CONSTRAINT fk_50836742b5f83ccd');
        $this->addSql('DROP SEQUENCE frame_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE frames_translations_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE frames_big_translations_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE frames_frames_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE frames_smalldescription_translations_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE frames_big (id BIGINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE frames_big_translations (id BIGINT NOT NULL, frame BIGINT DEFAULT NULL, language BIGINT DEFAULT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_32CDDC89B5F83CCD ON frames_big_translations (frame)');
        $this->addSql('CREATE INDEX IDX_32CDDC89D4DB71B5 ON frames_big_translations (language)');
        $this->addSql('CREATE TABLE frames_frames (id BIGINT NOT NULL, active BOOLEAN DEFAULT \'true\' NOT NULL, categoryPage BIGINT DEFAULT NULL, inheritance_type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_724A790DDC91ADBE ON frames_frames (categoryPage)');
        $this->addSql('CREATE TABLE frames_smalldescription (id BIGINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE frames_smalldescription_translations (id BIGINT NOT NULL, frame BIGINT DEFAULT NULL, language BIGINT DEFAULT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_23A78B2B5F83CCD ON frames_smalldescription_translations (frame)');
        $this->addSql('CREATE INDEX IDX_23A78B2D4DB71B5 ON frames_smalldescription_translations (language)');
        $this->addSql('CREATE TABLE frames_smallposter (id BIGINT NOT NULL, poster VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE frames_big ADD CONSTRAINT FK_4E995A4BBF396750 FOREIGN KEY (id) REFERENCES frames_frames (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE frames_big_translations ADD CONSTRAINT FK_32CDDC89B5F83CCD FOREIGN KEY (frame) REFERENCES frames_smalldescription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE frames_big_translations ADD CONSTRAINT FK_32CDDC89D4DB71B5 FOREIGN KEY (language) REFERENCES general_languages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE frames_frames ADD CONSTRAINT FK_724A790DDC91ADBE FOREIGN KEY (categoryPage) REFERENCES nodes_categorypages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE frames_smalldescription ADD CONSTRAINT FK_24D61019BF396750 FOREIGN KEY (id) REFERENCES frames_frames (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE frames_smalldescription_translations ADD CONSTRAINT FK_23A78B2B5F83CCD FOREIGN KEY (frame) REFERENCES frames_smalldescription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE frames_smalldescription_translations ADD CONSTRAINT FK_23A78B2D4DB71B5 FOREIGN KEY (language) REFERENCES general_languages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE frames_smallposter ADD CONSTRAINT FK_9B2EA47BF396750 FOREIGN KEY (id) REFERENCES frames_frames (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE frame');
        $this->addSql('DROP TABLE frames_translations');
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
