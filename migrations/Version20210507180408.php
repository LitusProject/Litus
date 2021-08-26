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
 * Version 20210507180408
 */
class Version20210507180408 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE nodes_faq_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE nodes_faq_translation (id BIGINT NOT NULL, faq BIGINT DEFAULT NULL, language BIGINT DEFAULT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D5444E5FE8FF75CC ON nodes_faq_translation (faq)');
        $this->addSql('CREATE INDEX IDX_D5444E5FD4DB71B5 ON nodes_faq_translation (language)');
        $this->addSql('CREATE TABLE nodes_faq (id BIGINT NOT NULL, forced_language BIGINT DEFAULT NULL, name VARCHAR(255) NOT NULL, order_number INT DEFAULT NULL, active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_660123A5DA76015 ON nodes_faq (forced_language)');
        $this->addSql('CREATE TABLE faq_roles_map (faq BIGINT NOT NULL, role VARCHAR(255) NOT NULL, PRIMARY KEY(faq, role))');
        $this->addSql('CREATE INDEX IDX_E76724EEE8FF75CC ON faq_roles_map (faq)');
        $this->addSql('CREATE INDEX IDX_E76724EE57698A6A ON faq_roles_map (role)');
        $this->addSql('ALTER TABLE nodes_faq_translation ADD CONSTRAINT FK_D5444E5FE8FF75CC FOREIGN KEY (faq) REFERENCES nodes_faq (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE nodes_faq_translation ADD CONSTRAINT FK_D5444E5FD4DB71B5 FOREIGN KEY (language) REFERENCES general_languages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE nodes_faq ADD CONSTRAINT FK_660123A5DA76015 FOREIGN KEY (forced_language) REFERENCES general_languages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE nodes_faq ADD CONSTRAINT FK_660123A5BF396750 FOREIGN KEY (id) REFERENCES nodes_nodes (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE faq_roles_map ADD CONSTRAINT FK_E76724EEE8FF75CC FOREIGN KEY (faq) REFERENCES nodes_faq (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE faq_roles_map ADD CONSTRAINT FK_E76724EE57698A6A FOREIGN KEY (role) REFERENCES acl_roles (name) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE SEQUENCE faq_page_map_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE faq_page_map (id BIGINT NOT NULL, referenced_faq BIGINT DEFAULT NULL, referenced_page BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A3B05B3D4A61D01 ON faq_page_map (referenced_faq)');
        $this->addSql('CREATE INDEX IDX_A3B05B3DF1335145 ON faq_page_map (referenced_page)');
        $this->addSql('ALTER TABLE faq_page_map ADD CONSTRAINT FK_A3B05B3D4A61D01 FOREIGN KEY (referenced_faq) REFERENCES nodes_faq (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE faq_page_map ADD CONSTRAINT FK_A3B05B3DF1335145 FOREIGN KEY (referenced_page) REFERENCES nodes_pages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
