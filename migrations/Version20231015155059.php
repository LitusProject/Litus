<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20231015155059
 */
class Version20231015155059 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE br_companies_requests_vacancy');
        $this->addSql('DROP TABLE br_companies_requests_student_job');
        $this->addSql('DROP TABLE br_companies_requests_internship');
        $this->addSql('ALTER TABLE br_companies_requests ADD job BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE br_companies_requests ADD edit_job BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE br_companies_requests ADD requestType TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE br_companies_requests ADD rejectMessage TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE br_companies_requests DROP inheritance_type');
        $this->addSql('ALTER TABLE br_companies_requests ADD CONSTRAINT FK_8FC0C94AFBD8E0F8 FOREIGN KEY (job) REFERENCES br_companies_jobs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE br_companies_requests ADD CONSTRAINT FK_8FC0C94A368E27C3 FOREIGN KEY (edit_job) REFERENCES br_companies_jobs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8FC0C94AFBD8E0F8 ON br_companies_requests (job)');
        $this->addSql('CREATE INDEX IDX_8FC0C94A368E27C3 ON br_companies_requests (edit_job)');
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
