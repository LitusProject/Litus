<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20230724114859
 */
class Version20230724114859 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE frames DROP CONSTRAINT FK_FE6E893ADC91ADBE');
        $this->addSql('ALTER TABLE frames ADD CONSTRAINT FK_FE6E893ADC91ADBE FOREIGN KEY (categoryPage) REFERENCES nodes_categorypages (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
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
