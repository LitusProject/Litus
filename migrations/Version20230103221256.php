<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20230103221256
 */
class Version20230103221256 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE quiz_tiebreaker_answers_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE quiz_tiebreakers_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE quiz_tiebreaker_answers (id BIGINT NOT NULL, tiebreaker BIGINT DEFAULT NULL, team BIGINT DEFAULT NULL, answer SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1AEFCE6F1E6BE6DA ON quiz_tiebreaker_answers (tiebreaker)');
        $this->addSql('CREATE INDEX IDX_1AEFCE6FC4E0A61F ON quiz_tiebreaker_answers (team)');
        $this->addSql('CREATE UNIQUE INDEX tiebreaker_answer_unique ON quiz_tiebreaker_answers (tiebreaker, team)');
        $this->addSql('CREATE TABLE quiz_tiebreakers (id BIGINT NOT NULL, quiz BIGINT DEFAULT NULL, name VARCHAR(255) NOT NULL, correct_answer SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_52308C87A412FA92 ON quiz_tiebreakers (quiz)');
        $this->addSql('ALTER TABLE quiz_tiebreaker_answers ADD CONSTRAINT FK_1AEFCE6F1E6BE6DA FOREIGN KEY (tiebreaker) REFERENCES quiz_tiebreakers (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_tiebreaker_answers ADD CONSTRAINT FK_1AEFCE6FC4E0A61F FOREIGN KEY (team) REFERENCES quiz_teams (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_tiebreakers ADD CONSTRAINT FK_52308C87A412FA92 FOREIGN KEY (quiz) REFERENCES quiz_quizes (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_quizes ADD tiebreaker BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz_quizes ADD CONSTRAINT FK_63040A4A1E6BE6DA FOREIGN KEY (tiebreaker) REFERENCES quiz_tiebreakers (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_63040A4A1E6BE6DA ON quiz_quizes (tiebreaker)');
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
