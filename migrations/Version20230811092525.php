<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20230811092525
 */
class Version20230811092525 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE categorypages_roles_map DROP CONSTRAINT fk_93db90f19f91cc67');
        $this->addSql('ALTER TABLE frames DROP CONSTRAINT fk_fe6e893adc91adbe');
        $this->addSql('ALTER TABLE frames_translations DROP CONSTRAINT fk_50836742b5f83ccd');
        $this->addSql('DROP SEQUENCE categorypages_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE frames_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE frames_translations_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE nodes_pages_categorypages_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE nodes_pages_frames_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE nodes_pages_frames_translations_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE nodes_pages_categorypages (id BIGINT NOT NULL, category BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9562983064C19C1 ON nodes_pages_categorypages (category)');
        $this->addSql('CREATE TABLE nodes_pages_categorypages_roles_map (category_page BIGINT NOT NULL, role VARCHAR(255) NOT NULL, PRIMARY KEY(category_page, role))');
        $this->addSql('CREATE INDEX IDX_1F543E8E9F91CC67 ON nodes_pages_categorypages_roles_map (category_page)');
        $this->addSql('CREATE INDEX IDX_1F543E8E57698A6A ON nodes_pages_categorypages_roles_map (role)');
        $this->addSql('CREATE TABLE nodes_pages_frames (id BIGINT NOT NULL, link_to_page BIGINT DEFAULT NULL, link_to_link BIGINT DEFAULT NULL, active BOOLEAN DEFAULT \'true\' NOT NULL, big BOOLEAN DEFAULT \'true\' NOT NULL, has_description BOOLEAN DEFAULT \'true\' NOT NULL, has_poster BOOLEAN DEFAULT \'true\' NOT NULL, poster VARCHAR(255) DEFAULT NULL, order_number INT DEFAULT NULL, categoryPage BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_20960BA7DC91ADBE ON nodes_pages_frames (categoryPage)');
        $this->addSql('CREATE INDEX IDX_20960BA78E82FD1A ON nodes_pages_frames (link_to_page)');
        $this->addSql('CREATE INDEX IDX_20960BA7AC24D2CB ON nodes_pages_frames (link_to_link)');
        $this->addSql('CREATE TABLE nodes_pages_frames_translations (id BIGINT NOT NULL, frame BIGINT DEFAULT NULL, language BIGINT DEFAULT NULL, description TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FDE64C5DB5F83CCD ON nodes_pages_frames_translations (frame)');
        $this->addSql('CREATE INDEX IDX_FDE64C5DD4DB71B5 ON nodes_pages_frames_translations (language)');
        $this->addSql('ALTER TABLE nodes_pages_categorypages ADD CONSTRAINT FK_9562983064C19C1 FOREIGN KEY (category) REFERENCES nodes_pages_categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE nodes_pages_categorypages_roles_map ADD CONSTRAINT FK_1F543E8E9F91CC67 FOREIGN KEY (category_page) REFERENCES nodes_pages_categorypages (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE nodes_pages_categorypages_roles_map ADD CONSTRAINT FK_1F543E8E57698A6A FOREIGN KEY (role) REFERENCES acl_roles (name) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE nodes_pages_frames ADD CONSTRAINT FK_20960BA7DC91ADBE FOREIGN KEY (categoryPage) REFERENCES nodes_pages_categorypages (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE nodes_pages_frames ADD CONSTRAINT FK_20960BA78E82FD1A FOREIGN KEY (link_to_page) REFERENCES nodes_pages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE nodes_pages_frames ADD CONSTRAINT FK_20960BA7AC24D2CB FOREIGN KEY (link_to_link) REFERENCES nodes_pages_links (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE nodes_pages_frames_translations ADD CONSTRAINT FK_FDE64C5DB5F83CCD FOREIGN KEY (frame) REFERENCES nodes_pages_frames (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE nodes_pages_frames_translations ADD CONSTRAINT FK_FDE64C5DD4DB71B5 FOREIGN KEY (language) REFERENCES general_languages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE categorypages');
        $this->addSql('DROP TABLE categorypages_roles_map');
        $this->addSql('DROP TABLE frames');
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
