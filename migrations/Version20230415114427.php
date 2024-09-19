<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20230415114427
 */
class Version20230415114427 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE mail_sections DROP CONSTRAINT fk_f64182d06dc044c5');
        $this->addSql('DROP INDEX idx_f64182d06dc044c5');
        $this->addSql('ALTER TABLE mail_sections RENAME COLUMN "group" TO section_group');
        $this->addSql('ALTER TABLE mail_sections ADD CONSTRAINT FK_F64182D0FA9723A1 FOREIGN KEY (section_group) REFERENCES mail_sections_groups (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F64182D0FA9723A1 ON mail_sections (section_group)');
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
