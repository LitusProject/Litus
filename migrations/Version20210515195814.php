<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20210515195814
 */
class Version20210515195814 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

         $this->addSql('ALTER TABLE logistics_article ADD alertMail TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE logistics_order ADD internalComment TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE logistics_order ADD externalComment TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE logistics_article DROP CONSTRAINT fk_88486ca25e9e89cb');
        $this->addSql('DROP INDEX idx_88486ca25e9e89cb');
        $this->addSql('ALTER TABLE logistics_article ALTER location TYPE TEXT');
        $this->addSql('ALTER TABLE logistics_article ALTER location DROP DEFAULT');
        $this->addSql('ALTER TABLE logistics_article ALTER location SET NOT NULL');
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
