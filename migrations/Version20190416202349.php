<?php
declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20190416202349
 */
class Version20190416202349 extends \Doctrine\Migrations\AbstractMigration
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

        $this->addSql('ALTER INDEX idx_10cf6d26c1ee637c RENAME TO idx_e05e63dc1ee637c');
        $this->addSql('ALTER INDEX idx_14cf436275ae721 RENAME TO idx_d4b51666275ae721');
        $this->addSql('ALTER INDEX idx_1751b863d8e604f RENAME TO idx_66b973a53d8e604f');
        $this->addSql('ALTER INDEX idx_1863132913b7300a RENAME TO idx_7c0b372613b7300a');
        $this->addSql('ALTER INDEX idx_186313295ff69b7d RENAME TO idx_7c0b37265ff69b7d');
        $this->addSql('ALTER INDEX idx_206d0775217bbb47 RENAME TO idx_6c55c048217bbb47');
        $this->addSql('ALTER INDEX idx_206d077540c1fea7 RENAME TO idx_6c55c04840c1fea7');
        $this->addSql('ALTER INDEX idx_225c24e923a0e66 RENAME TO idx_fc328f2723a0e66');
        $this->addSql('ALTER INDEX idx_225c24e99b2a6c7e RENAME TO idx_fc328f279b2a6c7e');
        $this->addSql('ALTER INDEX idx_27a6ddcc275ae721 RENAME TO idx_91c5e33a275ae721');
        $this->addSql('ALTER INDEX idx_27a6ddcc3bae0aa7 RENAME TO idx_91c5e33a3bae0aa7');
        $this->addSql('ALTER INDEX idx_27a6ddcc5a8b0903 RENAME TO idx_91c5e33a5a8b0903');
        $this->addSql('ALTER INDEX idx_27a6ddcc5e9e89cb RENAME TO idx_91c5e33a5e9e89cb');
        $this->addSql('ALTER INDEX idx_27a6ddccdcbb0c53 RENAME TO idx_91c5e33adcbb0c53');
        $this->addSql('ALTER INDEX idx_27a6ddccfa2425b9 RENAME TO idx_91c5e33afa2425b9');
        $this->addSql('ALTER INDEX idx_2bbeed7234dcd176 RENAME TO idx_4fcba0e434dcd176');
        $this->addSql('ALTER INDEX idx_2bbeed729474526c RENAME TO idx_4fcba0e49474526c');
        $this->addSql('ALTER INDEX idx_2ecef71a5bf54558 RENAME TO idx_209211df5bf54558');
        $this->addSql('ALTER INDEX idx_2ecef71ad4db71b5 RENAME TO idx_209211dfd4db71b5');
        $this->addSql('ALTER INDEX idx_3092f0eb40549b08 RENAME TO idx_5794156740549b08');
        $this->addSql('ALTER INDEX idx_34839066d044d5d4 RENAME TO idx_867139d6d044d5d4');
        $this->addSql('ALTER INDEX idx_354995013be452f7 RENAME TO idx_a793da913be452f7');
        $this->addSql('ALTER INDEX idx_35499501d4db71b5 RENAME TO idx_a793da91d4db71b5');
        $this->addSql('ALTER INDEX idx_36c452a623a0e66 RENAME TO idx_9bdbf87823a0e66');
        $this->addSql('ALTER INDEX idx_437f4540275ae721 RENAME TO idx_58bf6cb6275ae721');
        $this->addSql('ALTER INDEX idx_437f454040549b08 RENAME TO idx_58bf6cb640549b08');
        $this->addSql('ALTER INDEX idx_43eb0aff275ae721 RENAME TO idx_1d3d89a5275ae721');
        $this->addSql('ALTER INDEX idx_43eb0aff40549b08 RENAME TO idx_1d3d89a540549b08');
        $this->addSql('ALTER INDEX idx_43eb0afffbce3e7a RENAME TO idx_1d3d89a5fbce3e7a');
        $this->addSql('ALTER INDEX idx_464923e3e98f2859 RENAME TO idx_ef5b3808e98f2859');
        $this->addSql('ALTER INDEX idx_46802b3d77153098 RENAME TO idx_6a84f9e577153098');
        $this->addSql('ALTER INDEX idx_46802b3d89919cbd RENAME TO idx_6a84f9e589919cbd');
        $this->addSql('ALTER INDEX idx_46802b3d993853e0 RENAME TO idx_6a84f9e5993853e0');
        $this->addSql('ALTER INDEX idx_479b6f45275ae721 RENAME TO idx_1be768a5275ae721');
        $this->addSql('ALTER INDEX idx_479b6f4534dcd176 RENAME TO idx_1be768a534dcd176');
        $this->addSql('ALTER INDEX idx_4c21bc24275ae721 RENAME TO idx_b2bc4946275ae721');
        $this->addSql('ALTER INDEX idx_4c21bc2440549b08 RENAME TO idx_b2bc494640549b08');
        $this->addSql('ALTER INDEX idx_4c21bc24e67f9749 RENAME TO idx_b2bc4946e67f9749');
        $this->addSql('ALTER INDEX idx_4fc1300f22b35429 RENAME TO idx_199a0bc122b35429');
        $this->addSql('ALTER INDEX idx_4fc1300f3d8e604f RENAME TO idx_199a0bc13d8e604f');
        $this->addSql('ALTER INDEX idx_553524a368e27c3 RENAME TO idx_f10bb04a368e27c3');
        $this->addSql('ALTER INDEX idx_553524afbd8e0f8 RENAME TO idx_f10bb04afbd8e0f8');
        $this->addSql('ALTER INDEX idx_56b6d2b2d5b0234 RENAME TO idx_110b79bd2d5b0234');
        $this->addSql('ALTER INDEX idx_56cc985cee87e006 RENAME TO idx_3ef86a6ee87e006');
        $this->addSql('ALTER INDEX idx_5a3b59ea275ae721 RENAME TO idx_7023f415275ae721');
        $this->addSql('ALTER INDEX idx_5a3b59ea40549b08 RENAME TO idx_7023f41540549b08');
        $this->addSql('ALTER INDEX idx_5a57d06739986e43 RENAME TO idx_596d88f839986e43');
        $this->addSql('ALTER INDEX idx_5a57d067d4db71b5 RENAME TO idx_596d88f8d4db71b5');
        $this->addSql('ALTER INDEX idx_5aa02d9e5288fd4f RENAME TO idx_3fa1e7485288fd4f');
        $this->addSql('ALTER INDEX idx_5aa02d9ed4db71b5 RENAME TO idx_3fa1e748d4db71b5');
        $this->addSql('ALTER INDEX idx_5c17e7d464cc34ab RENAME TO idx_58af757064cc34ab');
        $this->addSql('ALTER INDEX idx_5c17e7d4de091aaf RENAME TO idx_58af7570de091aaf');
        $this->addSql('ALTER INDEX idx_64ef60c940c1fea7 RENAME TO idx_cc6d710640c1fea7');
        $this->addSql('ALTER INDEX idx_64ef60c9c4663e4 RENAME TO idx_cc6d7106c4663e4');
        $this->addSql('ALTER INDEX idx_69fc2c07275ae721 RENAME TO idx_edc63e43275ae721');
        $this->addSql('ALTER INDEX idx_710c3e2571f7e88b RENAME TO idx_1e92e16671f7e88b');
        $this->addSql('ALTER INDEX idx_710c3e25979b1ad6 RENAME TO idx_1e92e166979b1ad6');
        $this->addSql('ALTER INDEX idx_718fd80b90651744 RENAME TO idx_41bd94b990651744');
        $this->addSql('ALTER INDEX idx_73c0f10234dcd176 RENAME TO idx_13a9183934dcd176');
        $this->addSql('ALTER INDEX idx_74d85ece23a0e66 RENAME TO idx_1a697b9823a0e66');
        $this->addSql('ALTER INDEX idx_74d85ece55ece9f9 RENAME TO idx_1a697b9855ece9f9');
        $this->addSql('ALTER INDEX idx_7a15d64f275ae721 RENAME TO idx_930feca7275ae721');
        $this->addSql('ALTER INDEX idx_7c985c5723edc87 RENAME TO idx_4fef45a023edc87');
        $this->addSql('ALTER INDEX idx_7c985c57275ae721 RENAME TO idx_4fef45a0275ae721');
        $this->addSql('ALTER INDEX idx_7e18892fa2425b9 RENAME TO idx_d98f235cfa2425b9');
        $this->addSql('ALTER INDEX idx_7e8761ae23a0e66 RENAME TO idx_6afa3f6023a0e66');
        $this->addSql('ALTER INDEX idx_7e8761aeba4b6de8 RENAME TO idx_6afa3f60ba4b6de8');
        $this->addSql('ALTER INDEX idx_7e8761aed044d5d4 RENAME TO idx_6afa3f60d044d5d4');
        $this->addSql('ALTER INDEX idx_80aa45a44c8f818 RENAME TO idx_5033c98344c8f818');
        $this->addSql('ALTER INDEX idx_80aa45a57698a6a RENAME TO idx_5033c98357698a6a');
        $this->addSql('ALTER INDEX idx_83b0121023a0e66 RENAME TO idx_9d7a990b23a0e66');
        $this->addSql('ALTER INDEX idx_83b0121097601f83 RENAME TO idx_9d7a990b97601f83');
        $this->addSql('ALTER INDEX idx_83b01210c1ee637c RENAME TO idx_9d7a990bc1ee637c');
        $this->addSql('ALTER INDEX idx_8ae87a7d275ae721 RENAME TO idx_a0faabcb275ae721');
        $this->addSql('ALTER INDEX idx_8b7ec4c334dcd176 RENAME TO idx_469751f34dcd176');
        $this->addSql('ALTER INDEX idx_8c4fa64bb0ccc06 RENAME TO idx_1c94d3feb0ccc06');
        $this->addSql('ALTER INDEX idx_8c4fa64bf50d82f4 RENAME TO idx_1c94d3fef50d82f4');
        $this->addSql('ALTER INDEX idx_8d5e3059368e27c3 RENAME TO idx_1efab04a368e27c3');
        $this->addSql('ALTER INDEX idx_8d5e3059fbd8e0f8 RENAME TO idx_1efab04afbd8e0f8');
        $this->addSql('ALTER INDEX idx_91197e7d97e625e8 RENAME TO idx_baabb0dc97e625e8');
        $this->addSql('ALTER INDEX idx_91197e7da50b3b45 RENAME TO idx_baabb0dca50b3b45');
        $this->addSql('ALTER INDEX idx_9173ee21275ae721 RENAME TO idx_15b1107f275ae721');
        $this->addSql('ALTER INDEX idx_9173ee2134dcd176 RENAME TO idx_15b1107f34dcd176');
        $this->addSql('ALTER INDEX idx_992412a5275ae721 RENAME TO idx_d80b5374275ae721');
        $this->addSql('ALTER INDEX idx_a16943297a999bce RENAME TO idx_ba5d823a7a999bce');
        $this->addSql('ALTER INDEX idx_a1694329e67f9749 RENAME TO idx_ba5d823ae67f9749');
        $this->addSql('ALTER INDEX idx_a1bb235234dcd176 RENAME TO idx_c97cf35f34dcd176');
        $this->addSql('ALTER INDEX idx_a1bb23525288fd4f RENAME TO idx_c97cf35f5288fd4f');
        $this->addSql('ALTER INDEX idx_a48cd11d7a999bce RENAME TO idx_a84829b27a999bce');
        $this->addSql('ALTER INDEX idx_a48cd11de67f9749 RENAME TO idx_a84829b2e67f9749');
        $this->addSql('ALTER INDEX idx_a7df95e8275ae721 RENAME TO idx_28a10335275ae721');
        $this->addSql('ALTER INDEX idx_a7df95e8dcbb0c53 RENAME TO idx_28a10335dcbb0c53');
        $this->addSql('ALTER INDEX idx_aa3dc5894c62e638 RENAME TO idx_8fc0c94a4c62e638');
        $this->addSql('ALTER INDEX idx_aa92441bd4db71b5 RENAME TO idx_fb137aabd4db71b5');
        $this->addSql('ALTER INDEX idx_aa92441bee87e006 RENAME TO idx_fb137aabee87e006');
        $this->addSql('ALTER INDEX idx_aaf50c7b39986e43 RENAME TO idx_aadd2f2339986e43');
        $this->addSql('ALTER INDEX idx_ae03e7c6275ae721 RENAME TO idx_f47ed288275ae721');
        $this->addSql('ALTER INDEX idx_ae03e7c640549b08 RENAME TO idx_f47ed28840549b08');
        $this->addSql('ALTER INDEX idx_ae03e7c6c1ee637c RENAME TO idx_f47ed288c1ee637c');
        $this->addSql('ALTER INDEX idx_ba5abc8d5475e8c4 RENAME TO idx_88bd6a2a5475e8c4');
        $this->addSql('ALTER INDEX idx_bd6cc8a95140dedb RENAME TO idx_9fea6dec5140dedb');
        $this->addSql('ALTER INDEX idx_bd6cc8a9a50b3b45 RENAME TO idx_9fea6deca50b3b45');
        $this->addSql('ALTER INDEX idx_c1a2f1bf34dcd176 RENAME TO idx_588ac4a634dcd176');
        $this->addSql('ALTER INDEX idx_c1a2f1bf3bae0aa7 RENAME TO idx_588ac4a63bae0aa7');
        $this->addSql('ALTER INDEX idx_c1a2f1bf5a8600b0 RENAME TO idx_588ac4a65a8600b0');
        $this->addSql('ALTER INDEX idx_c1a2f1bfef336c0a RENAME TO idx_588ac4a6ef336c0a');
        $this->addSql('ALTER INDEX idx_c705dc685bf54558 RENAME TO idx_2b86707c5bf54558');
        $this->addSql('ALTER INDEX idx_c705dc68d4db71b5 RENAME TO idx_2b86707cd4db71b5');
        $this->addSql('ALTER INDEX idx_c827039557698a6a RENAME TO idx_2e4739c957698a6a');
        $this->addSql('ALTER INDEX idx_c8270395a50b3b45 RENAME TO idx_2e4739c9a50b3b45');
        $this->addSql('ALTER INDEX idx_c9eb340e1f1b251e RENAME TO idx_8c2647af1f1b251e');
        $this->addSql('ALTER INDEX idx_c9eb340e9846c1a6 RENAME TO idx_8c2647af9846c1a6');
        $this->addSql('ALTER INDEX idx_c9eb340eeed90630 RENAME TO idx_8c2647afeed90630');
        $this->addSql('ALTER INDEX idx_cfad4da45126ac48 RENAME TO idx_48c3baac5126ac48');
        $this->addSql('ALTER INDEX idx_cfad4da4d4db71b5 RENAME TO idx_48c3baacd4db71b5');
        $this->addSql('ALTER INDEX idx_cfc27abc969bd765 RENAME TO idx_57f81256969bd765');
        $this->addSql('ALTER INDEX idx_cfc27abcd4db71b5 RENAME TO idx_57f81256d4db71b5');
        $this->addSql('ALTER INDEX idx_d18815f440549b08 RENAME TO idx_8e9e039740549b08');
        $this->addSql('ALTER INDEX idx_d193298c40549b08 RENAME TO idx_e56dfc3640549b08');
        $this->addSql('ALTER INDEX idx_d193298c44c8f818 RENAME TO idx_e56dfc3644c8f818');
        $this->addSql('ALTER INDEX idx_d19c0718bf5476ca RENAME TO idx_1bdaf4d3bf5476ca');
        $this->addSql('ALTER INDEX idx_d19c0718d4db71b5 RENAME TO idx_1bdaf4d3d4db71b5');
        $this->addSql('ALTER INDEX idx_d4309ce623a0e66 RENAME TO idx_a0aff50123a0e66');
        $this->addSql('ALTER INDEX idx_db87b31723a0e66 RENAME TO idx_22d215923a0e66');
        $this->addSql('ALTER INDEX idx_db87b317ba4b6de8 RENAME TO idx_22d2159ba4b6de8');
        $this->addSql('ALTER INDEX idx_db87b317d044d5d4 RENAME TO idx_22d2159d044d5d4');
        $this->addSql('ALTER INDEX idx_dfcb609a34dcd176 RENAME TO idx_6e7f237334dcd176');
        $this->addSql('ALTER INDEX idx_e1d73fd825e4c122 RENAME TO idx_78bb6dc525e4c122');
        $this->addSql('ALTER INDEX idx_e1d73fd8d34a04ad RENAME TO idx_78bb6dc5d34a04ad');
        $this->addSql('ALTER INDEX idx_e70429b423a0e66 RENAME TO idx_396a827a23a0e66');
        $this->addSql('ALTER INDEX idx_e70429b434dcd176 RENAME TO idx_396a827a34dcd176');
        $this->addSql('ALTER INDEX idx_e7daf0434fbf094f RENAME TO idx_678443384fbf094f');
        $this->addSql('ALTER INDEX idx_e7daf043bb827337 RENAME TO idx_67844338bb827337');
        $this->addSql('ALTER INDEX idx_e8a41db63bae0aa7 RENAME TO idx_7fac985e3bae0aa7');
        $this->addSql('ALTER INDEX idx_e9f93a6c34dcd176 RENAME TO idx_ec1bda0134dcd176');
        $this->addSql('ALTER INDEX idx_f1819d50e7b003e9 RENAME TO idx_ed4d6683e7b003e9');
        $this->addSql('ALTER INDEX idx_f1819d50fe54d947 RENAME TO idx_ed4d6683fe54d947');
        $this->addSql('ALTER INDEX idx_f6a43c70d044d5d4 RENAME TO idx_36d9e24bd044d5d4');
        $this->addSql('ALTER INDEX idx_f6a43c70d34a04ad RENAME TO idx_36d9e24bd34a04ad');
        $this->addSql('ALTER INDEX idx_f898caa434dcd176 RENAME TO idx_8450584d34dcd176');
        $this->addSql('ALTER INDEX idx_f898caa456f7f6c1 RENAME TO idx_8450584d56f7f6c1');
        $this->addSql('ALTER INDEX idx_f898caa4d044d5d4 RENAME TO idx_8450584dd044d5d4');
        $this->addSql('ALTER INDEX idx_fb99ee724c62e638 RENAME TO idx_c7f0e8574c62e638');
        $this->addSql('ALTER INDEX idx_fb99ee725a8b0903 RENAME TO idx_c7f0e8575a8b0903');
        $this->addSql('ALTER INDEX idx_ffa2759940549b08 RENAME TO idx_e418722c40549b08');

        $this->addSql('ALTER INDEX IF EXISTS idx_14cf43634dcd176 RENAME TO idx_d4b5166634dcd176');

        $this->addSql('ALTER INDEX uniq_14cf436e00cedde RENAME TO uniq_d4b51666e00cedde');
        $this->addSql('ALTER INDEX uniq_328ec2a34dcd176 RENAME TO uniq_c0b62e3134dcd176');
        $this->addSql('ALTER INDEX uniq_56cc985c5288fd4f RENAME TO uniq_3ef86a65288fd4f');
        $this->addSql('ALTER INDEX uniq_7e1889228b6a9f5 RENAME TO uniq_d98f235c28b6a9f5');
        $this->addSql('ALTER INDEX uniq_7e188927b20d77e RENAME TO uniq_d98f235c7b20d77e');
        $this->addSql('ALTER INDEX uniq_8a19e82ac74095a RENAME TO uniq_29c53d81ac74095a');
        $this->addSql('ALTER INDEX uniq_8ae87a7d34dcd176 RENAME TO uniq_a0faabcb34dcd176');
        $this->addSql('ALTER INDEX uniq_ce32a9d697ae0266 RENAME TO uniq_4c930b597ae0266');
        $this->addSql('ALTER INDEX uniq_ee0218873950b5f6 RENAME TO uniq_edcda3d13950b5f6');
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
