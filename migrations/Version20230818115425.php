<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20230818115425
 */
class Version20230818115425 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE cudi_sale_articles_restrictions_studies_map DROP CONSTRAINT fk_921f6a4a7a999bce');
        $this->addSql('ALTER TABLE cudi_sale_articles_restrictions_studies_map ADD CONSTRAINT FK_BA5D823A7A999BCE FOREIGN KEY (restriction) REFERENCES cudi_sale_articles_restrictions_study (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
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
