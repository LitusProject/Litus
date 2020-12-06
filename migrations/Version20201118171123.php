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
 * Version 20201118171123
 */
class Version20201118171123 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE logistics_request DROP CONSTRAINT fk_b1e5ed5bf5299398');
        $this->addSql('DROP INDEX idx_b1e5ed5bf5299398');
        $this->addSql('ALTER TABLE logistics_request RENAME COLUMN "order" TO referencedOrder');
        $this->addSql('ALTER TABLE logistics_request ADD CONSTRAINT FK_B1E5ED5B8CF42BE2 FOREIGN KEY (referencedOrder) REFERENCES logistics_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
//        $this->addSql('CREATE INDEX IDX_B1E5ED5B8CF42BE2 ON logistics_request (referencedOrder)');
        $this->addSql('ALTER TABLE logistics_request DROP CONSTRAINT fk_b1e5ed5b8cf42be2');
//        $this->addSql('DROP INDEX idx_b1e5ed5b8cf42be2');
        $this->addSql('ALTER TABLE logistics_request RENAME COLUMN referencedorder TO referenced_order');
        $this->addSql('ALTER TABLE logistics_request ADD CONSTRAINT FK_B1E5ED5BC8143FA8 FOREIGN KEY (referenced_order) REFERENCES logistics_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B1E5ED5BC8143FA8 ON logistics_request (referenced_order)');
        $this->addSql('ALTER TABLE logistics_order ALTER dateupdated TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE logistics_order ALTER dateupdated DROP DEFAULT');
        $this->addSql('ALTER TABLE logistics_request DROP CONSTRAINT fk_b1e5ed5b368e27c3');
        $this->addSql('DROP INDEX idx_b1e5ed5b368e27c3');
        $this->addSql('ALTER TABLE logistics_request RENAME COLUMN edit_job TO edit_order');
        $this->addSql('ALTER TABLE logistics_request ADD CONSTRAINT FK_B1E5ED5BCC9BE6E5 FOREIGN KEY (edit_order) REFERENCES logistics_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B1E5ED5BCC9BE6E5 ON logistics_request (edit_order)');
        $this->addSql('ALTER TABLE logistics_order_order_article_map DROP CONSTRAINT fk_d35d2b3e8d9f6d38');
        $this->addSql('DROP INDEX idx_d35d2b3e8d9f6d38');
        $this->addSql('ALTER TABLE logistics_order_order_article_map RENAME COLUMN order_id TO referenced_order');
        $this->addSql('ALTER TABLE logistics_order_order_article_map ADD CONSTRAINT FK_D35D2B3E870A1E FOREIGN KEY (referenced_order) REFERENCES logistics_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D35D2B3E870A1E ON logistics_order_order_article_map (referenced_order)');

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
