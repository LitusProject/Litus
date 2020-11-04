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
 * Version 20200929144025
 */
class Version20200929144025 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE logistics_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE logistics_order_order_article_map_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE logistics_order_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE logistics_article_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE logistics_request (id BIGINT NOT NULL, contact BIGINT DEFAULT NULL, "order" BIGINT DEFAULT NULL, edit_job BIGINT DEFAULT NULL, creation_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, handled BOOLEAN NOT NULL, requestType TEXT NOT NULL, rejectMessage TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B1E5ED5B4C62E638 ON logistics_request (contact)');
        $this->addSql('CREATE INDEX IDX_B1E5ED5BF5299398 ON logistics_request ("order")');
        $this->addSql('CREATE INDEX IDX_B1E5ED5B368E27C3 ON logistics_request (edit_job)');
        $this->addSql('CREATE TABLE logistics_order_order_article_map (id BIGINT NOT NULL, order_id BIGINT DEFAULT NULL, article_id BIGINT DEFAULT NULL, amount BIGINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D35D2B3E8D9F6D38 ON logistics_order_order_article_map (order_id)');
        $this->addSql('CREATE INDEX IDX_D35D2B3E7294869C ON logistics_order_order_article_map (article_id)');
        $this->addSql('CREATE TABLE logistics_order (id BIGINT NOT NULL, location BIGINT DEFAULT NULL, contact BIGINT DEFAULT NULL, unit BIGINT DEFAULT NULL, name VARCHAR(100) NOT NULL, description TEXT NOT NULL, email TEXT DEFAULT NULL, dateUpdated DATE NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, approved BOOLEAN DEFAULT NULL, removed BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E9BFCB065E9E89CB ON logistics_order (location)');
        $this->addSql('CREATE INDEX IDX_E9BFCB064C62E638 ON logistics_order (contact)');
        $this->addSql('CREATE INDEX IDX_E9BFCB06DCBB0C53 ON logistics_order (unit)');
        $this->addSql('CREATE TABLE logistics_article (id BIGINT NOT NULL, location BIGINT DEFAULT NULL, name TEXT NOT NULL, additional_info TEXT NOT NULL, amount_owned INT NOT NULL, amount_available INT NOT NULL, visibility TEXT NOT NULL, status TEXT NOT NULL, spot TEXT NOT NULL, warranty INT NOT NULL, rent INT NOT NULL, category TEXT NOT NULL, date_updated DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_88486CA25E9E89CB ON logistics_article (location)');
        $this->addSql('ALTER TABLE logistics_request ADD CONSTRAINT FK_B1E5ED5B4C62E638 FOREIGN KEY (contact) REFERENCES users_people_academic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_request ADD CONSTRAINT FK_B1E5ED5BF5299398 FOREIGN KEY ("order") REFERENCES logistics_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_request ADD CONSTRAINT FK_B1E5ED5B368E27C3 FOREIGN KEY (edit_job) REFERENCES logistics_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_order_order_article_map ADD CONSTRAINT FK_D35D2B3E8D9F6D38 FOREIGN KEY (order_id) REFERENCES logistics_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_order_order_article_map ADD CONSTRAINT FK_D35D2B3E7294869C FOREIGN KEY (article_id) REFERENCES logistics_article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_order ADD CONSTRAINT FK_E9BFCB065E9E89CB FOREIGN KEY (location) REFERENCES general_locations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_order ADD CONSTRAINT FK_E9BFCB064C62E638 FOREGN KEY (contact) REFERENCES users_people_academic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_order ADD CONSTRAINT FK_E9BFCB06DCBB0C53 FOREIGN KEY (unit) REFERENCES general_organizations_units (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_article ADD CONSTRAINT FK_88486CA25E9E89CB FOREIGN KEY (location) REFERENCES general_locations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_order_order_article_map ADD status TEXT NOT NULL');
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
