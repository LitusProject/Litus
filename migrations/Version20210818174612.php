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
 * Version 20210818174612
 */
class Version20210818174612 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX uniq_4c170ba94fbf094f');
        $this->addSql('DROP INDEX uniq_4c170ba98157aa0f');
        $this->addSql('CREATE INDEX IDX_4C170BA94FBF094F ON br_match_profile_company_map (company)');
        $this->addSql('CREATE INDEX IDX_4C170BA98157AA0F ON br_match_profile_company_map (profile)');
        $this->addSql('DROP INDEX uniq_a89ca721fd77566');
        $this->addSql('DROP INDEX uniq_a89ca728157aa0f');
        $this->addSql('CREATE INDEX IDX_A89CA721FD77566 ON br_match_profile_feature_map (feature)');
        $this->addSql('CREATE INDEX IDX_A89CA728157AA0F ON br_match_profile_feature_map (profile)');
        $this->addSql('DROP INDEX uniq_de998a67b723af33');
        $this->addSql('DROP INDEX uniq_de998a678157aa0f');
        $this->addSql('CREATE INDEX IDX_DE998A67B723AF33 ON br_match_profile_student_map (student)');
        $this->addSql('CREATE INDEX IDX_DE998A678157AA0F ON br_match_profile_student_map (profile)');
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
