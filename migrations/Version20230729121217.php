<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20230729121217
 */
class Version20230729121217 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE logistics_request ADD canceled BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE logistics_request ALTER handled SET DEFAULT \'false\'');
        $this->addSql('ALTER TABLE logistics_request ALTER removed SET DEFAULT \'false\'');
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
