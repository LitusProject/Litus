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
 * Version 20210815213638
 */
class Version20210815213638 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE br_communication_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE br_communication (id BIGINT NOT NULL, company BIGINT DEFAULT NULL, creator BIGINT DEFAULT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, audience VARCHAR(255) NOT NULL, option VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7BC4048E4FBF094F ON br_communication (company)');
        $this->addSql('CREATE INDEX IDX_7BC4048EBC06EA63 ON br_communication (creator)');
        $this->addSql('ALTER TABLE br_communication ADD CONSTRAINT FK_7BC4048E4FBF094F FOREIGN KEY (company) REFERENCES br_companies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_communication ADD CONSTRAINT FK_7BC4048EBC06EA63 FOREIGN KEY (creator) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
