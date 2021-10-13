<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20210418234146
 */
class Version20210418234146 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE logistics_article ADD internal_comment TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE logistics_article ADD photo_path VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE logistics_order ADD needs_ride BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE logistics_request ADD flag TEXT DEFAULT NULL');
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
