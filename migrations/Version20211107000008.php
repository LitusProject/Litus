<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20211107000008
 */
class Version20211107000008 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE ticket_transactions DROP CONSTRAINT fk_2a7ee6e72cff2df9');
        $this->addSql('DROP INDEX idx_2a7ee6e72cff2df9');
        $this->addSql('ALTER TABLE ticket_transactions RENAME COLUMN consumption TO owner');
        $this->addSql('ALTER TABLE ticket_transactions ADD CONSTRAINT FK_2A7EE6E7CF60E67C FOREIGN KEY (owner) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2A7EE6E7CF60E67C ON ticket_transactions (owner)');
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
