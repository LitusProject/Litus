<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20221230180441
 */
class Version20221230180441 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE quiz_quizes ADD tiebreaker BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz_quizes ADD CONSTRAINT FK_63040A4A1E6BE6DA FOREIGN KEY (tiebreaker) REFERENCES quiz_tiebreakers (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_63040A4A1E6BE6DA ON quiz_quizes (tiebreaker)');
        $this->addSql('DROP INDEX idx_52308c87a412fa92');
        $this->addSql('ALTER TABLE quiz_tiebreakers DROP tiebreaker_order');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_52308C87A412FA92 ON quiz_tiebreakers (quiz)');
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
