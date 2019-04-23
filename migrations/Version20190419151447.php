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
 * Version 20190419151447
 */
class Version20190419151447 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on PostgreSQL.'
        );

        $this->addSql('CREATE SEQUENCE api_codes_authorization_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE api_tokens_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE api_codes_authorization (id BIGINT NOT NULL, person BIGINT DEFAULT NULL, key BIGINT DEFAULT NULL, code VARCHAR(255) NOT NULL, expiration_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, exchange_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_fc46eb8034dcd176 ON api_codes_authorization (person)');
        $this->addSql('CREATE INDEX idx_fc46eb808a90aba9 ON api_codes_authorization (key)');
        $this->addSql('CREATE TABLE api_tokens (id BIGINT NOT NULL, person BIGINT DEFAULT NULL, authorization_code BIGINT DEFAULT NULL, code VARCHAR(255) NOT NULL, expiration_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, inheritance_type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_2cad560e34dcd176 ON api_tokens (person)');
        $this->addSql('CREATE INDEX idx_2cad560e2f33e8b8 ON api_tokens (authorization_code)');
        $this->addSql('CREATE TABLE api_tokens_access (id BIGINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE api_tokens_refresh (id BIGINT NOT NULL, key BIGINT DEFAULT NULL, exchange_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_e0f54f0c8a90aba9 ON api_tokens_refresh (key)');
        $this->addSql('ALTER TABLE api_codes_authorization ADD CONSTRAINT fk_fc46eb8034dcd176 FOREIGN KEY (person) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE api_codes_authorization ADD CONSTRAINT fk_fc46eb808a90aba9 FOREIGN KEY (key) REFERENCES api_keys (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE api_tokens ADD CONSTRAINT fk_2cad560e34dcd176 FOREIGN KEY (person) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE api_tokens ADD CONSTRAINT fk_2cad560e2f33e8b8 FOREIGN KEY (authorization_code) REFERENCES api_codes_authorization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE api_tokens_access ADD CONSTRAINT fk_17e117c1bf396750 FOREIGN KEY (id) REFERENCES api_tokens (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE api_tokens_refresh ADD CONSTRAINT fk_e0f54f0c8a90aba9 FOREIGN KEY (key) REFERENCES api_keys (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE api_tokens_refresh ADD CONSTRAINT fk_e0f54f0cbf396750 FOREIGN KEY (id) REFERENCES api_tokens (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
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
