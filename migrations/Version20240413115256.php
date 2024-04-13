<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20240413115256
 */
class Version20240413115256 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE inventory_article_unit');
        $this->addSql('ALTER TABLE logistics_cg_article ADD "order" BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE logistics_cg_article ADD CONSTRAINT FK_8D6E2C39F5299398 FOREIGN KEY ("order") REFERENCES logistics_order (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D6E2C39F5299398 ON logistics_cg_article ("order")');
        $this->addSql('ALTER TABLE logistics_inventory_article ADD unit BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE logistics_inventory_article ADD CONSTRAINT FK_82EC8C32DCBB0C53 FOREIGN KEY (unit) REFERENCES general_organizations_units (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_82EC8C32DCBB0C53 ON logistics_inventory_article (unit)');
        $this->addSql('ALTER TABLE logistics_order DROP CONSTRAINT FK_E9BFCB06BC06EA63');
        $this->addSql('ALTER TABLE logistics_order ADD updater BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE logistics_order ADD CONSTRAINT FK_E9BFCB06324F23A6 FOREIGN KEY (updater) REFERENCES users_people_academic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_order ADD CONSTRAINT FK_E9BFCB06BC06EA63 FOREIGN KEY (creator) REFERENCES users_people_academic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E9BFCB06324F23A6 ON logistics_order (updater)');
        $this->addSql('ALTER TABLE logistics_order_order_flesserke_article_map ADD "order" BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE logistics_order_order_flesserke_article_map ADD CONSTRAINT FK_3D7CED63F5299398 FOREIGN KEY ("order") REFERENCES logistics_order (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3D7CED63F5299398 ON logistics_order_order_flesserke_article_map ("order")');
        $this->addSql('ALTER TABLE logistics_order_order_inventory_article_map ADD "order" BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE logistics_order_order_inventory_article_map ADD CONSTRAINT FK_38F9EDD1F5299398 FOREIGN KEY ("order") REFERENCES logistics_order (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_38F9EDD1F5299398 ON logistics_order_order_inventory_article_map ("order")');
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
