<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20210222112500
 */
class Version20210222112500 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE logistics_article_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE logistics_order_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE logistics_order_order_article_map_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE logistics_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE logistics_article (id BIGINT NOT NULL, location BIGINT DEFAULT NULL, name TEXT NOT NULL, additional_info TEXT NOT NULL, amount_owned INT NOT NULL, amount_available INT NOT NULL, visibility TEXT NOT NULL, status TEXT NOT NULL, spot TEXT NOT NULL, warranty INT NOT NULL, rent INT NOT NULL, category TEXT NOT NULL, date_updated DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_88486CA25E9E89CB ON logistics_article (location)');
        $this->addSql('CREATE TABLE logistics_order (id BIGINT NOT NULL, location BIGINT DEFAULT NULL, creator BIGINT DEFAULT NULL, unit BIGINT DEFAULT NULL, name VARCHAR(100) NOT NULL, description TEXT NOT NULL, email TEXT DEFAULT NULL, contact TEXT DEFAULT NULL, dateUpdated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, approved BOOLEAN DEFAULT NULL, removed BOOLEAN DEFAULT \'false\' NOT NULL, rejected BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E9BFCB065E9E89CB ON logistics_order (location)');
        $this->addSql('CREATE INDEX IDX_E9BFCB06BC06EA63 ON logistics_order (creator)');
        $this->addSql('CREATE INDEX IDX_E9BFCB06DCBB0C53 ON logistics_order (unit)');
        $this->addSql('CREATE TABLE logistics_order_order_article_map (id BIGINT NOT NULL, referenced_order BIGINT DEFAULT NULL, referenced_article BIGINT DEFAULT NULL, amount BIGINT NOT NULL, status TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D35D2B3EC8143FA8 ON logistics_order_order_article_map (referenced_order)');
        $this->addSql('CREATE INDEX IDX_D35D2B3E21CEA6B ON logistics_order_order_article_map (referenced_article)');
        $this->addSql('CREATE TABLE logistics_request (id BIGINT NOT NULL, contact BIGINT DEFAULT NULL, referenced_order BIGINT DEFAULT NULL, edit_order BIGINT DEFAULT NULL, creation_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, handled BOOLEAN NOT NULL, removed BOOLEAN NOT NULL, requestType TEXT NOT NULL, rejectMessage TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B1E5ED5B4C62E638 ON logistics_request (contact)');
        $this->addSql('CREATE INDEX IDX_B1E5ED5BC8143FA8 ON logistics_request (referenced_order)');
        $this->addSql('CREATE INDEX IDX_B1E5ED5BCC9BE6E5 ON logistics_request (edit_order)');
        $this->addSql('ALTER TABLE logistics_article ADD CONSTRAINT FK_88486CA25E9E89CB FOREIGN KEY (location) REFERENCES general_locations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_order ADD CONSTRAINT FK_E9BFCB065E9E89CB FOREIGN KEY (location) REFERENCES general_locations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_order ADD CONSTRAINT FK_E9BFCB06BC06EA63 FOREIGN KEY (creator) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_order ADD CONSTRAINT FK_E9BFCB06DCBB0C53 FOREIGN KEY (unit) REFERENCES general_organizations_units (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_order_order_article_map ADD CONSTRAINT FK_D35D2B3EC8143FA8 FOREIGN KEY (referenced_order) REFERENCES logistics_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_order_order_article_map ADD CONSTRAINT FK_D35D2B3E21CEA6B FOREIGN KEY (referenced_article) REFERENCES logistics_article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_request ADD CONSTRAINT FK_B1E5ED5B4C62E638 FOREIGN KEY (contact) REFERENCES users_people_academic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_request ADD CONSTRAINT FK_B1E5ED5BC8143FA8 FOREIGN KEY (referenced_order) REFERENCES logistics_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_request ADD CONSTRAINT FK_B1E5ED5BCC9BE6E5 FOREIGN KEY (edit_order) REFERENCES logistics_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
