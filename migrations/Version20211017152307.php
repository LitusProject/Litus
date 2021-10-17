<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20211017152307
 */
class Version20211017152307 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE shift_shifts_volunteers ADD payed_year BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE shift_shifts_volunteers ADD CONSTRAINT FK_6E7F237387AA288D FOREIGN KEY (payed_year) REFERENCES general_academic_years (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6E7F237387AA288D ON shift_shifts_volunteers (payed_year)');
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
