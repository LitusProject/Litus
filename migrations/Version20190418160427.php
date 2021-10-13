<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20190418160427
 */
class Version20190418160427 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on PostgreSQL.'
        );

        $this->addSql('ALTER TABLE form_fields_translations_options DROP CONSTRAINT IF EXISTS fk_862f14455bf54558');
        $this->addSql('ALTER TABLE form_fields_translations_options DROP CONSTRAINT IF EXISTS fk_938323c65bf54558');
        $this->addSql('ALTER TABLE form_fields_translations_options ADD CONSTRAINT fk_2b86707c5bf54558 FOREIGN KEY (field) REFERENCES form_fields_dropdowns (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE form_fields_options');
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
