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
        $this->addSql('CREATE TABLE faq_pages_map (faq BIGINT NOT NULL, page BIGINT NOT NULL, PRIMARY KEY(faq, page))');
        $this->addSql('CREATE INDEX IDX_F801013BE8FF75CC ON faq_pages_map (faq)');
        $this->addSql('CREATE INDEX IDX_F801013B140AB620 ON faq_pages_map (page)');
        $this->addSql('ALTER TABLE nodes_faq_translation ADD CONSTRAINT FK_D5444E5FE8FF75CC FOREIGN KEY (faq) REFERENCES nodes_faq (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE nodes_faq_translation ADD CONSTRAINT FK_D5444E5FD4DB71B5 FOREIGN KEY (language) REFERENCES general_languages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE nodes_faq ADD CONSTRAINT FK_660123A5DA76015 FOREIGN KEY (forced_language) REFERENCES general_languages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE nodes_faq ADD CONSTRAINT FK_660123A5BF396750 FOREIGN KEY (id) REFERENCES nodes_nodes (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE faq_roles_map ADD CONSTRAINT FK_E76724EEE8FF75CC FOREIGN KEY (faq) REFERENCES nodes_faq (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE faq_roles_map ADD CONSTRAINT FK_E76724EE57698A6A FOREIGN KEY (role) REFERENCES acl_roles (name) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE faq_pages_map ADD CONSTRAINT FK_F801013BE8FF75CC FOREIGN KEY (faq) REFERENCES nodes_faq (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE faq_pages_map ADD CONSTRAINT FK_F801013B140AB620 FOREIGN KEY (page) REFERENCES nodes_pages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE TABLE page_faq (page_id BIGINT NOT NULL, faq_id BIGINT NOT NULL, PRIMARY KEY(page_id, faq_id))');
        $this->addSql('CREATE INDEX IDX_24407D5DC4663E4 ON page_faq (page_id)');
        $this->addSql('CREATE INDEX IDX_24407D5D81BEC8C2 ON page_faq (faq_id)');
        $this->addSql('ALTER TABLE page_faq ADD CONSTRAINT FK_24407D5DC4663E4 FOREIGN KEY (page_id) REFERENCES nodes_pages (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE page_faq ADD CONSTRAINT FK_24407D5D81BEC8C2 FOREIGN KEY (faq_id) REFERENCES nodes_faq (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
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
