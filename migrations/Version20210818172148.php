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
 * Version 20210818172148
 */
class Version20210818172148 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE br_match_profile_company_map_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE br_match_profile_student_map_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE br_match_profile_company_map (id BIGINT NOT NULL, company BIGINT DEFAULT NULL, profile BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4C170BA94FBF094F ON br_match_profile_company_map (company)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4C170BA98157AA0F ON br_match_profile_company_map (profile)');
        $this->addSql('CREATE UNIQUE INDEX br_match_profile_company_map_company_profile ON br_match_profile_company_map (company, profile)');
        $this->addSql('CREATE TABLE br_match_profile_student_map (id BIGINT NOT NULL, student BIGINT DEFAULT NULL, profile BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DE998A67B723AF33 ON br_match_profile_student_map (student)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DE998A678157AA0F ON br_match_profile_student_map (profile)');
        $this->addSql('CREATE UNIQUE INDEX br_match_profile_student_map_student_profile ON br_match_profile_student_map (student, profile)');
        $this->addSql('ALTER TABLE br_match_profile_company_map ADD CONSTRAINT FK_4C170BA94FBF094F FOREIGN KEY (company) REFERENCES br_companies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_profile_company_map ADD CONSTRAINT FK_4C170BA98157AA0F FOREIGN KEY (profile) REFERENCES br_match_profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_profile_student_map ADD CONSTRAINT FK_DE998A67B723AF33 FOREIGN KEY (student) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_match_profile_student_map ADD CONSTRAINT FK_DE998A678157AA0F FOREIGN KEY (profile) REFERENCES br_match_profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE br_match_profile_companyprofile');
        $this->addSql('DROP TABLE br_match_profile_studentprofile');
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
