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
 * Version 20201122223357
 */
class Version20201122223357 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE logistics_order DROP CONSTRAINT FK_E9BFCB06BC06EA63');
        $this->addSql('ALTER TABLE logistics_order ADD CONSTRAINT FK_E9BFCB06BC06EA63 FOREIGN KEY (creator) REFERENCES users_people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_order_order_article_map DROP CONSTRAINT fk_d35d2b3e7294869c');
        $this->addSql('ALTER TABLE logistics_order_order_article_map DROP CONSTRAINT fk_d35d2b3e870a1e');
        $this->addSql('DROP INDEX idx_d35d2b3e7294869c');
        $this->addSql('DROP INDEX idx_d35d2b3e870a1e');
        $this->addSql('ALTER TABLE logistics_order_order_article_map ADD referenced_order BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE logistics_order_order_article_map ADD referenced_article BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE logistics_order_order_article_map DROP referencedorder_id');
        $this->addSql('ALTER TABLE logistics_order_order_article_map DROP article_id');
        $this->addSql('ALTER TABLE logistics_order_order_article_map ADD CONSTRAINT FK_D35D2B3EC8143FA8 FOREIGN KEY (referenced_order) REFERENCES logistics_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE logistics_order_order_article_map ADD CONSTRAINT FK_D35D2B3E21CEA6B FOREIGN KEY (referenced_article) REFERENCES logistics_article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D35D2B3EC8143FA8 ON logistics_order_order_article_map (referenced_order)');
        $this->addSql('CREATE INDEX IDX_D35D2B3E21CEA6B ON logistics_order_order_article_map (referenced_article)');

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
