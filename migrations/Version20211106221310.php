<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20211106221310
 */
class Version20211106221310 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE ticket_transactions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ticket_transactions (id BIGINT NOT NULL, person BIGINT DEFAULT NULL, consumption BIGINT DEFAULT NULL, amount INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2A7EE6E734DCD176 ON ticket_transactions (person)');
        $this->addSql('CREATE INDEX IDX_2A7EE6E72CFF2DF9 ON ticket_transactions (consumption)');
        $this->addSql('ALTER TABLE ticket_transactions ADD CONSTRAINT FK_2A7EE6E734DCD176 FOREIGN KEY (person) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket_transactions ADD CONSTRAINT FK_2A7EE6E72CFF2DF9 FOREIGN KEY (consumption) REFERENCES ticket_consumptions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
