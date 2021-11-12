<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20210130105705
 */
class Version20210130105705 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE cudi_deal_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE cudi_retail_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE cudi_deal (id BIGINT NOT NULL, retail BIGINT DEFAULT NULL, buyer BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4FAC464FFB899E15 ON cudi_deal (retail)');
        $this->addSql('CREATE INDEX IDX_4FAC464F84905FB3 ON cudi_deal (buyer)');
        $this->addSql('CREATE TABLE cudi_retail (id BIGINT NOT NULL, article BIGINT DEFAULT NULL, person BIGINT DEFAULT NULL, price INT NOT NULL, is_anonymous BOOLEAN NOT NULL, comment VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E32CC2D923A0E66 ON cudi_retail (article)');
        $this->addSql('CREATE INDEX IDX_E32CC2D934DCD176 ON cudi_retail (person)');
        $this->addSql('ALTER TABLE cudi_deal ADD CONSTRAINT FK_4FAC464FFB899E15 FOREIGN KEY (retail) REFERENCES cudi_retail (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cudi_deal ADD CONSTRAINT FK_4FAC464F84905FB3 FOREIGN KEY (buyer) REFERENCES users_people_academic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cudi_retail ADD CONSTRAINT FK_E32CC2D923A0E66 FOREIGN KEY (article) REFERENCES cudi_articles (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cudi_retail ADD CONSTRAINT FK_E32CC2D934DCD176 FOREIGN KEY (person) REFERENCES users_people_academic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
