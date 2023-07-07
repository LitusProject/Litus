<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20230707075105
 */
class Version20230707075105 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE nodes_categorypages (id BIGINT NOT NULL, category BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E8B86FB864C19C1 ON nodes_categorypages (category)');
        $this->addSql('CREATE TABLE nodes_categorypages_roles_map (category_page BIGINT NOT NULL, role VARCHAR(255) NOT NULL, PRIMARY KEY(category_page, role))');
        $this->addSql('CREATE INDEX IDX_27302179F91CC67 ON nodes_categorypages_roles_map (category_page)');
        $this->addSql('CREATE INDEX IDX_273021757698A6A ON nodes_categorypages_roles_map (role)');
        $this->addSql('ALTER TABLE nodes_categorypages ADD CONSTRAINT FK_E8B86FB864C19C1 FOREIGN KEY (category) REFERENCES nodes_pages_categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE nodes_categorypages ADD CONSTRAINT FK_E8B86FB8BF396750 FOREIGN KEY (id) REFERENCES nodes_nodes (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE nodes_categorypages_roles_map ADD CONSTRAINT FK_27302179F91CC67 FOREIGN KEY (category_page) REFERENCES nodes_categorypages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE nodes_categorypages_roles_map ADD CONSTRAINT FK_273021757698A6A FOREIGN KEY (role) REFERENCES acl_roles (name) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
