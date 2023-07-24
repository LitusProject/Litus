<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20230724181631
 */
class Version20230724181631 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE nodes_categorypages_roles_map DROP CONSTRAINT FK_27302179F91CC67');
        $this->addSql('ALTER TABLE nodes_categorypages_roles_map DROP CONSTRAINT FK_273021757698A6A');
        $this->addSql('ALTER TABLE nodes_categorypages_roles_map ADD CONSTRAINT FK_27302179F91CC67 FOREIGN KEY (category_page) REFERENCES nodes_categorypages (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE nodes_categorypages_roles_map ADD CONSTRAINT FK_273021757698A6A FOREIGN KEY (role) REFERENCES acl_roles (name) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
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
