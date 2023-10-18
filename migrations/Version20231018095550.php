<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20231018095550
 */
class Version20231018095550 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');
        
        $this->addSql('ALTER TABLE quiz_tiebreaker_answers ALTER answer TYPE INT');
        $this->addSql('ALTER TABLE quiz_tiebreaker_answers ALTER answer DROP DEFAULT');
        $this->addSql('ALTER TABLE quiz_tiebreakers ALTER correct_answer TYPE INT');
        $this->addSql('ALTER TABLE quiz_tiebreakers ALTER correct_answer DROP DEFAULT');
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
