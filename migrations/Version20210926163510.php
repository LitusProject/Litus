<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20210926163510
 */
class Version20210926163510 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE tickets_events_categories_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE tickets_events_categories (id BIGINT NOT NULL, event BIGINT DEFAULT NULL, category VARCHAR(255) NOT NULL, bookingOpenDate TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, bookingCloseDate TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, maxNumberTickets INT NOT NULL, maxAmountGuests INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_896D0F0E3BAE0AA7 ON tickets_events_categories (event)');
        $this->addSql('CREATE TABLE tickets_orders (id BIGINT NOT NULL, event BIGINT DEFAULT NULL, booker BIGINT DEFAULT NULL, book_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BE0934263BAE0AA7 ON tickets_orders (event)');
        $this->addSql('CREATE INDEX IDX_BE0934266EDAA308 ON tickets_orders (booker)');
        $this->addSql('ALTER TABLE tickets_events_categories ADD CONSTRAINT FK_896D0F0E3BAE0AA7 FOREIGN KEY (event) REFERENCES ticket_events (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tickets_orders ADD CONSTRAINT FK_BE0934263BAE0AA7 FOREIGN KEY (event) REFERENCES ticket_events (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tickets_orders ADD CONSTRAINT FK_BE0934266EDAA308 FOREIGN KEY (booker) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket_events ADD description TEXT');
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
