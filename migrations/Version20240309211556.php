<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20240309211556
 */
class Version20240309211556 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE order_unit (order_id BIGINT NOT NULL, unit_id BIGINT NOT NULL, PRIMARY KEY(order_id, unit_id))');
        $this->addSql('CREATE INDEX IDX_914A36448D9F6D38 ON order_unit (order_id)');
        $this->addSql('CREATE INDEX IDX_914A3644F8BD700D ON order_unit (unit_id)');
        $this->addSql('ALTER TABLE order_unit ADD CONSTRAINT FK_914A36448D9F6D38 FOREIGN KEY (order_id) REFERENCES logistics_order (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_unit ADD CONSTRAINT FK_914A3644F8BD700D FOREIGN KEY (unit_id) REFERENCES general_organizations_units (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_order DROP CONSTRAINT fk_e9bfcb06dcbb0c53');
        $this->addSql('DROP INDEX idx_e9bfcb06dcbb0c53');
        $this->addSql('ALTER TABLE logistics_order DROP unit');
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
