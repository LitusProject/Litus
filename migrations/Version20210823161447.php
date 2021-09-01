<?php
declare(strict_types=1);

/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20210823161447
 */
class Version20210823161447 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA tickets');
        $this->addSql('ALTER TABLE nodes_faq_translation DROP CONSTRAINT fk_d5444e5fe8ff75cc');
        $this->addSql('ALTER TABLE faq_page_map DROP CONSTRAINT fk_a3b05b3d4a61d01');
        $this->addSql('ALTER TABLE faq_roles_map DROP CONSTRAINT fk_e76724eee8ff75cc');
        // $this->addSql('ALTER TABLE ticket_tickets DROP CONSTRAINT fk_588ac4a622d93f');
        $this->addSql('DROP SEQUENCE nodes_faq_translation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE faq_page_map_id_seq CASCADE');
        // $this->addSql('DROP SEQUENCE tickets_orders_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE tickets.orders_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ticket_guests_info_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE tickets.orders (id BIGINT NOT NULL, event BIGINT DEFAULT NULL, booker BIGINT DEFAULT NULL, book_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F416EFB93BAE0AA7 ON tickets.orders (event)');
        $this->addSql('CREATE INDEX IDX_F416EFB96EDAA308 ON tickets.orders (booker)');
        $this->addSql('CREATE TABLE ticket_guests_info (id BIGINT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE tickets.orders ADD CONSTRAINT FK_F416EFB93BAE0AA7 FOREIGN KEY (event) REFERENCES ticket_events (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tickets.orders ADD CONSTRAINT FK_F416EFB96EDAA308 FOREIGN KEY (booker) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket_events ADD bookable_praesidium BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE ticket_events ADD bookable BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE ticket_events ADD bookings_close_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket_events ADD tickets_generated BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE ticket_events ADD number_of_tickets INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket_events ADD limit_per_person INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket_events ADD only_members BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE ticket_events ADD price_members SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE ticket_events ADD price_non_members SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE ticket_events DROP max_number_tickets');
        $this->addSql('ALTER TABLE ticket_events_options DROP CONSTRAINT fk_7fac985e64c19c1');
        $this->addSql('DROP INDEX idx_7fac985e64c19c1');
        $this->addSql('ALTER TABLE ticket_events_options ADD price_non_members SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE ticket_events_options RENAME COLUMN category TO event');
        $this->addSql('ALTER TABLE ticket_events_options RENAME COLUMN price TO price_members');
        $this->addSql('ALTER TABLE ticket_events_options ADD CONSTRAINT FK_7FAC985E3BAE0AA7 FOREIGN KEY (event) REFERENCES ticket_events (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7FAC985E3BAE0AA7 ON ticket_events_options (event)');
        $this->addSql('DROP INDEX idx_588ac4a622d93f');
        $this->addSql('ALTER TABLE ticket_tickets ADD guest_info BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket_tickets ADD book_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket_tickets ADD sold_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket_tickets ADD number BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket_tickets ADD member BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket_tickets DROP bookdate');
        $this->addSql('ALTER TABLE ticket_tickets DROP solddate');
        $this->addSql('ALTER TABLE ticket_tickets RENAME COLUMN orderentity TO event');
        $this->addSql('ALTER TABLE ticket_tickets ADD CONSTRAINT FK_588AC4A63BAE0AA7 FOREIGN KEY (event) REFERENCES ticket_events (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket_tickets ADD CONSTRAINT FK_588AC4A6EF336C0A FOREIGN KEY (guest_info) REFERENCES ticket_guests_info (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_588AC4A63BAE0AA7 ON ticket_tickets (event)');
        $this->addSql('CREATE INDEX IDX_588AC4A6EF336C0A ON ticket_tickets (guest_info)');
        $this->addSql('CREATE UNIQUE INDEX ticket_tickets_event_number ON ticket_tickets (event, number)');
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
