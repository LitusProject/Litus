<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20230727220845
 */
class Version20230727220845 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE logistics_order ADD referenced_Request BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE logistics_order ADD CONSTRAINT FK_E9BFCB06F40C520E FOREIGN KEY (referenced_Request) REFERENCES logistics_request (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E9BFCB06F40C520E ON logistics_order (referenced_Request)');
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
