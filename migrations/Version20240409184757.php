<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20240409184757
 */
class Version20240409184757 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE logistics_cg_article_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE logistics_cg_article (id BIGINT NOT NULL, name VARCHAR(255) NOT NULL, amount INT NOT NULL, update_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, external_comment TEXT DEFAULT NULL, internal_comment TEXT DEFAULT NULL, unit VARCHAR(255) NOT NULL, per_unit INT NOT NULL, brand VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
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
