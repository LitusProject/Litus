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
 * Version 20200920152009
 */
class Version20200920152009 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE br_companies_requests_student_job (id BIGINT NOT NULL, job BIGINT DEFAULT NULL, edit_job BIGINT DEFAULT NULL, requestType TEXT NOT NULL, rejectMessage TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F2FBDBFBFBD8E0F8 ON br_companies_requests_student_job (job)');
        $this->addSql('CREATE INDEX IDX_F2FBDBFB368E27C3 ON br_companies_requests_student_job (edit_job)');
        $this->addSql('ALTER TABLE br_companies_requests_student_job ADD CONSTRAINT FK_F2FBDBFBFBD8E0F8 FOREIGN KEY (job) REFERENCES br_companies_jobs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_companies_requests_student_job ADD CONSTRAINT FK_F2FBDBFB368E27C3 FOREIGN KEY (edit_job) REFERENCES br_companies_jobs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_companies_requests_student_job ADD CONSTRAINT FK_F2FBDBFBBF396750 FOREIGN KEY (id) REFERENCES br_companies_requests (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_companies_jobs ALTER sector TYPE TEXT');
        $this->addSql('ALTER TABLE br_companies_jobs ALTER sector DROP DEFAULT');
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
