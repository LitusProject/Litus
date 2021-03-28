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
 * Version 20210328225705
 */
class Version20210328225705 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE nodes_pages ADD forced_language BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE nodes_pages ADD order_number INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nodes_pages ADD CONSTRAINT FK_16434FA4DA76015 FOREIGN KEY (forced_language) REFERENCES general_languages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE nodes_pages ADD active BOOLEAN DEFAULT \'true\' NOT NULL');
        $this->addSql('ALTER TABLE nodes_pages_links ADD forced_language BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE nodes_pages_links ADD order_number INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nodes_pages_links ADD active BOOLEAN DEFAULT \'true\' NOT NULL');
        $this->addSql('ALTER TABLE nodes_pages_links ADD CONSTRAINT FK_929EF9F5DA76015 FOREIGN KEY (forced_language) REFERENCES general_languages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_929EF9F5DA76015 ON nodes_pages_links (forced_language)');
        $this->addSql('CREATE INDEX IDX_16434FA4DA76015 ON nodes_pages (forced_language)');
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
