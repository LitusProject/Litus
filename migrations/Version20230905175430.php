<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20230905175430
 */
class Version20230905175430 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE tickets.orders_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE br_events_companies_attendee_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE br_events_companies_metadata_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE faq_page_map_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE nodes_faq_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE br_events_companies_attendee (id BIGINT NOT NULL, first_name TEXT NOT NULL, last_name TEXT NOT NULL, email TEXT NOT NULL, phone_number TEXT DEFAULT NULL, lunch BOOLEAN NOT NULL, veggie BOOLEAN NOT NULL, companyMap_id BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8D3AF51899A014AB ON br_events_companies_attendee (companyMap_id)');
        $this->addSql('CREATE TABLE br_events_companies_metadata (id BIGINT NOT NULL, master_interests TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE br_events_companies_attendee ADD CONSTRAINT FK_8D3AF51899A014AB FOREIGN KEY (companyMap_id) REFERENCES br_events_companies_map (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_companies_pages DROP shortdescription');
        $this->addSql('ALTER TABLE br_companies_pages DROP youtubeurl');
        $this->addSql('ALTER TABLE br_contracts_entries DROP contract_text_en');
        $this->addSql('ALTER TABLE br_events ALTER end_date_visible SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4D3144D6D7DF1668 ON br_events (file_name)');
        $this->addSql('ALTER TABLE br_products DROP contract_text_en');
        $this->addSql('ALTER TABLE faq_page_map ADD CONSTRAINT FK_A3B05B3D4A61D01 FOREIGN KEY (referenced_faq) REFERENCES nodes_faq (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_order_order_article_map DROP CONSTRAINT FK_D35D2B3E21CEA6B');
        $this->addSql('ALTER TABLE logistics_order_order_article_map DROP CONSTRAINT FK_D35D2B3EC8143FA8');
        $this->addSql('ALTER TABLE logistics_order_order_article_map ADD CONSTRAINT FK_D35D2B3E21CEA6B FOREIGN KEY (referenced_article) REFERENCES logistics_article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_order_order_article_map ADD CONSTRAINT FK_D35D2B3EC8143FA8 FOREIGN KEY (referenced_order) REFERENCES logistics_order (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE faq_roles_map ADD CONSTRAINT FK_E76724EEE8FF75CC FOREIGN KEY (faq) REFERENCES nodes_faq (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE nodes_faq_translation ADD CONSTRAINT FK_D5444E5FE8FF75CC FOREIGN KEY (faq) REFERENCES nodes_faq (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE on_slugs ALTER active DROP DEFAULT');
        $this->addSql('ALTER INDEX registration_shift_users_academic_years_map_person_academic_yea RENAME TO registration_shift_users_academic_years_map_person_academic_year');
        $this->addSql('ALTER TABLE shop_history ALTER reservation TYPE TEXT');
        $this->addSql('ALTER TABLE shop_history ALTER reservation DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN shop_history.reservation IS \'(DC2Type:array)\'');
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
