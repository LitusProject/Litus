<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20240408200416
 */
class Version20240408200416 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE logistics_flesserke_article_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE logistics_flesserke_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE logistics_inventory_article_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE logistics_inventory_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE logistics_flesserke_article (id BIGINT NOT NULL, category BIGINT DEFAULT NULL, name VARCHAR(255) NOT NULL, amount INT NOT NULL, update_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, external_comment TEXT DEFAULT NULL, internal_comment TEXT DEFAULT NULL, barcode VARCHAR(255) NOT NULL, unit VARCHAR(255) NOT NULL, per_unit INT NOT NULL, brand VARCHAR(255) NOT NULL, expiration_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_981AEDD464C19C1 ON logistics_flesserke_article (category)');
        $this->addSql('CREATE TABLE logistics_flesserke_category (id BIGINT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE logistics_inventory_article (id BIGINT NOT NULL, category BIGINT DEFAULT NULL, name VARCHAR(255) NOT NULL, amount INT NOT NULL, update_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, external_comment TEXT DEFAULT NULL, internal_comment TEXT DEFAULT NULL, location VARCHAR(255) NOT NULL, spot VARCHAR(255) NOT NULL, visibility VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, warranty_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deposit INT DEFAULT 0 NOT NULL, rent INT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_82EC8C3264C19C1 ON logistics_inventory_article (category)');
        $this->addSql('CREATE TABLE inventory_article_unit (inventory_article_id BIGINT NOT NULL, unit_id BIGINT NOT NULL, PRIMARY KEY(inventory_article_id, unit_id))');
        $this->addSql('CREATE INDEX IDX_6756A64F317AD31F ON inventory_article_unit (inventory_article_id)');
        $this->addSql('CREATE INDEX IDX_6756A64FF8BD700D ON inventory_article_unit (unit_id)');
        $this->addSql('CREATE TABLE logistics_inventory_category (id BIGINT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE logistics_flesserke_article ADD CONSTRAINT FK_981AEDD464C19C1 FOREIGN KEY (category) REFERENCES logistics_flesserke_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_inventory_article ADD CONSTRAINT FK_82EC8C3264C19C1 FOREIGN KEY (category) REFERENCES logistics_inventory_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE inventory_article_unit ADD CONSTRAINT FK_6756A64F317AD31F FOREIGN KEY (inventory_article_id) REFERENCES logistics_inventory_article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE inventory_article_unit ADD CONSTRAINT FK_6756A64FF8BD700D FOREIGN KEY (unit_id) REFERENCES general_organizations_units (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_unit DROP CONSTRAINT FK_914A36448D9F6D38');
        $this->addSql('ALTER TABLE order_unit DROP CONSTRAINT FK_914A3644F8BD700D');
        $this->addSql('ALTER TABLE order_unit ADD CONSTRAINT FK_914A36448D9F6D38 FOREIGN KEY (order_id) REFERENCES logistics_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_unit ADD CONSTRAINT FK_914A3644F8BD700D FOREIGN KEY (unit_id) REFERENCES general_organizations_units (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
