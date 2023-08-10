<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20230810131422
 */
class Version20230810131422 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE categorypages_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE frames_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE frames_translations_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE categorypages (id BIGINT NOT NULL, category BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6CAEF2964C19C1 ON categorypages (category)');
        $this->addSql('CREATE TABLE categorypages_roles_map (category_page BIGINT NOT NULL, role VARCHAR(255) NOT NULL, PRIMARY KEY(category_page, role))');
        $this->addSql('CREATE INDEX IDX_93DB90F19F91CC67 ON categorypages_roles_map (category_page)');
        $this->addSql('CREATE INDEX IDX_93DB90F157698A6A ON categorypages_roles_map (role)');
        $this->addSql('CREATE TABLE frames (id BIGINT NOT NULL, link_to_page BIGINT DEFAULT NULL, link_to_link BIGINT DEFAULT NULL, active BOOLEAN DEFAULT \'true\' NOT NULL, big BOOLEAN DEFAULT \'true\' NOT NULL, has_description BOOLEAN DEFAULT \'true\' NOT NULL, has_poster BOOLEAN DEFAULT \'true\' NOT NULL, poster VARCHAR(255) DEFAULT NULL, order_number INT DEFAULT NULL, categoryPage BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FE6E893ADC91ADBE ON frames (categoryPage)');
        $this->addSql('CREATE INDEX IDX_FE6E893A8E82FD1A ON frames (link_to_page)');
        $this->addSql('CREATE INDEX IDX_FE6E893AAC24D2CB ON frames (link_to_link)');
        $this->addSql('CREATE TABLE frames_translations (id BIGINT NOT NULL, frame BIGINT DEFAULT NULL, language BIGINT DEFAULT NULL, description TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_50836742B5F83CCD ON frames_translations (frame)');
        $this->addSql('CREATE INDEX IDX_50836742D4DB71B5 ON frames_translations (language)');
        $this->addSql('ALTER TABLE categorypages ADD CONSTRAINT FK_F6CAEF2964C19C1 FOREIGN KEY (category) REFERENCES nodes_pages_categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE categorypages_roles_map ADD CONSTRAINT FK_93DB90F19F91CC67 FOREIGN KEY (category_page) REFERENCES categorypages (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE categorypages_roles_map ADD CONSTRAINT FK_93DB90F157698A6A FOREIGN KEY (role) REFERENCES acl_roles (name) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE frames ADD CONSTRAINT FK_FE6E893ADC91ADBE FOREIGN KEY (categoryPage) REFERENCES categorypages (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE frames ADD CONSTRAINT FK_FE6E893A8E82FD1A FOREIGN KEY (link_to_page) REFERENCES nodes_pages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE frames ADD CONSTRAINT FK_FE6E893AAC24D2CB FOREIGN KEY (link_to_link) REFERENCES nodes_pages_links (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE frames_translations ADD CONSTRAINT FK_50836742B5F83CCD FOREIGN KEY (frame) REFERENCES frames (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
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
