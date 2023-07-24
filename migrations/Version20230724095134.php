<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20230724095134
 */
class Version20230724095134 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE frames_big DROP CONSTRAINT fk_4e995a4bbf396750');
        $this->addSql('ALTER TABLE frames_smalldescription DROP CONSTRAINT fk_24d61019bf396750');
        $this->addSql('ALTER TABLE frames_smallposter DROP CONSTRAINT fk_9b2ea47bf396750');
        $this->addSql('ALTER TABLE frames_big_translations DROP CONSTRAINT fk_32cddc89b5f83ccd');
        $this->addSql('ALTER TABLE frames_smalldescription_translations DROP CONSTRAINT fk_23a78b2b5f83ccd');
        $this->addSql('DROP SEQUENCE frames_big_translations_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE frames_frames_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE frames_smalldescription_translations_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE frames_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE frames_translations_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE frames (id BIGINT NOT NULL, link_to_page BIGINT DEFAULT NULL, link_to_link BIGINT DEFAULT NULL, active BOOLEAN DEFAULT \'true\' NOT NULL, big BOOLEAN DEFAULT \'true\' NOT NULL, has_poster BOOLEAN DEFAULT \'true\' NOT NULL, poster VARCHAR(255) DEFAULT NULL, categoryPage BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FE6E893ADC91ADBE ON frames (categoryPage)');
        $this->addSql('CREATE INDEX IDX_FE6E893A8E82FD1A ON frames (link_to_page)');
        $this->addSql('CREATE INDEX IDX_FE6E893AAC24D2CB ON frames (link_to_link)');
        $this->addSql('CREATE TABLE frames_translations (id BIGINT NOT NULL, frame BIGINT DEFAULT NULL, language BIGINT DEFAULT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_50836742B5F83CCD ON frames_translations (frame)');
        $this->addSql('CREATE INDEX IDX_50836742D4DB71B5 ON frames_translations (language)');
        $this->addSql('ALTER TABLE frames ADD CONSTRAINT FK_FE6E893ADC91ADBE FOREIGN KEY (categoryPage) REFERENCES nodes_categorypages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE frames ADD CONSTRAINT FK_FE6E893A8E82FD1A FOREIGN KEY (link_to_page) REFERENCES nodes_pages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE frames ADD CONSTRAINT FK_FE6E893AAC24D2CB FOREIGN KEY (link_to_link) REFERENCES nodes_pages_links (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE frames_translations ADD CONSTRAINT FK_50836742B5F83CCD FOREIGN KEY (frame) REFERENCES frames (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE frames_translations ADD CONSTRAINT FK_50836742D4DB71B5 FOREIGN KEY (language) REFERENCES general_languages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE frames_frames');
        $this->addSql('DROP TABLE frames_big');
        $this->addSql('DROP TABLE frames_smalldescription');
        $this->addSql('DROP TABLE frames_big_translations');
        $this->addSql('DROP TABLE frames_smalldescription_translations');
        $this->addSql('DROP TABLE frames_smallposter');
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
