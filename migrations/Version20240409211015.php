<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20240409211015
 */
class Version20240409211015 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE logistics_order_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE logistics_order (id BIGINT NOT NULL, history BIGINT DEFAULT NULL, creator BIGINT DEFAULT NULL, active BOOLEAN NOT NULL, update_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name VARCHAR(100) NOT NULL, description TEXT NOT NULL, location VARCHAR(255) NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, transport VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E9BFCB0627BA704B ON logistics_order (history)');
        $this->addSql('CREATE INDEX IDX_E9BFCB06BC06EA63 ON logistics_order (creator)');
        $this->addSql('CREATE TABLE order_unit (order_id BIGINT NOT NULL, unit_id BIGINT NOT NULL, PRIMARY KEY(order_id, unit_id))');
        $this->addSql('CREATE INDEX IDX_914A36448D9F6D38 ON order_unit (order_id)');
        $this->addSql('CREATE INDEX IDX_914A3644F8BD700D ON order_unit (unit_id)');
        $this->addSql('ALTER TABLE logistics_order ADD CONSTRAINT FK_E9BFCB0627BA704B FOREIGN KEY (history) REFERENCES logistics_order_history (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_order ADD CONSTRAINT FK_E9BFCB06BC06EA63 FOREIGN KEY (creator) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_unit ADD CONSTRAINT FK_914A36448D9F6D38 FOREIGN KEY (order_id) REFERENCES logistics_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_unit ADD CONSTRAINT FK_914A3644F8BD700D FOREIGN KEY (unit_id) REFERENCES general_organizations_units (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
