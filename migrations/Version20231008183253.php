<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20231008183253
 */
class Version20231008183253 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE logistics_order_order_article_map DROP CONSTRAINT FK_D35D2B3E21CEA6B');
        $this->addSql('ALTER TABLE logistics_order_order_article_map DROP CONSTRAINT FK_D35D2B3EC8143FA8');
        $this->addSql('ALTER TABLE logistics_order_order_article_map ADD comment TEXT DEFAULT \'\'');
        $this->addSql('ALTER TABLE logistics_order_order_article_map ADD CONSTRAINT FK_D35D2B3E21CEA6B FOREIGN KEY (referenced_article) REFERENCES logistics_article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_order_order_article_map ADD CONSTRAINT FK_D35D2B3EC8143FA8 FOREIGN KEY (referenced_order) REFERENCES logistics_order (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
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
