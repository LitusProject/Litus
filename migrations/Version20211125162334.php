<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20211125162334
 */
class Version20211125162334 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE faq_page_map_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE nodes_faq_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE br_match_profile_companyprofile (id BIGINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE br_match_profile_studentprofile (id BIGINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE br_match_profile_companyprofile ADD CONSTRAINT FK_FB6EED77BF396750 FOREIGN KEY (id) REFERENCES br_match_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_profile_studentprofile ADD CONSTRAINT FK_48C75182BF396750 FOREIGN KEY (id) REFERENCES br_match_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_events ADD description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE faq_page_map ADD CONSTRAINT FK_A3B05B3D4A61D01 FOREIGN KEY (referenced_faq) REFERENCES nodes_faq (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE faq_roles_map ADD CONSTRAINT FK_E76724EEE8FF75CC FOREIGN KEY (faq) REFERENCES nodes_faq (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE nodes_faq_translation ADD CONSTRAINT FK_D5444E5FE8FF75CC FOREIGN KEY (faq) REFERENCES nodes_faq (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
