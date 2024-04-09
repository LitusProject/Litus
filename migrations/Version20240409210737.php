<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20240409210737
 */
class Version20240409210737 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE logistics_order_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE logistics_order_order_article_map_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE logistics_request_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE logistics_order_history_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE logistics_order_order_flesserke_article_map_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE logistics_order_order_inventory_article_map_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE logistics_order_history (id BIGINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE logistics_order_order_flesserke_article_map (id BIGINT NOT NULL, article BIGINT DEFAULT NULL, amount BIGINT NOT NULL, oldAmount BIGINT DEFAULT 0 NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3D7CED6323A0E66 ON logistics_order_order_flesserke_article_map (article)');
        $this->addSql('CREATE TABLE logistics_order_order_inventory_article_map (id BIGINT NOT NULL, article BIGINT DEFAULT NULL, amount BIGINT NOT NULL, oldAmount BIGINT DEFAULT 0 NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_38F9EDD123A0E66 ON logistics_order_order_inventory_article_map (article)');
        $this->addSql('ALTER TABLE logistics_order_order_flesserke_article_map ADD CONSTRAINT FK_3D7CED6323A0E66 FOREIGN KEY (article) REFERENCES logistics_flesserke_article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_order_order_inventory_article_map ADD CONSTRAINT FK_38F9EDD123A0E66 FOREIGN KEY (article) REFERENCES logistics_inventory_article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE logistics_request');
        $this->addSql('DROP TABLE logistics_order_order_article_map');
        $this->addSql('DROP TABLE order_unit');
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
