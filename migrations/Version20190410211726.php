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
use RuntimeException;

/**
 * Version 20190410211726
 */
class Version20190410211726 extends \Doctrine\Migrations\AbstractMigration
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

        $this->addSql('ALTER INDEX idx_124ac4af275ae721 RENAME TO idx_a7df95e8275ae721');
        $this->addSql('ALTER INDEX idx_124ac4afdcbb0c53 RENAME TO idx_a7df95e8dcbb0c53');
        $this->addSql('ALTER INDEX idx_14a2540334dcd176 RENAME TO idx_73c0f10234dcd176');
        $this->addSql('ALTER INDEX idx_169424d9275ae721 RENAME TO idx_f14cf34f275ae721');
        $this->addSql('ALTER INDEX idx_169424d9af3c6779 RENAME TO idx_f14cf34faf3c6779');
        $this->addSql('ALTER INDEX idx_16a8d24240549b08 RENAME TO idx_3092f0eb40549b08');
        $this->addSql('ALTER INDEX idx_172f9fa234dcd176 RENAME TO idx_f43d9a9234dcd176');
        $this->addSql('ALTER INDEX idx_172f9fa257698a6a RENAME TO idx_f43d9a9257698a6a');
        $this->addSql('ALTER INDEX idx_17c6034d275ae721 RENAME TO idx_8ae87a7d275ae721');
        $this->addSql('ALTER INDEX idx_18c71a90275ae721 RENAME TO idx_14cf436275ae721');
        $this->addSql('ALTER INDEX idx_18c71a9034dcd176 RENAME TO idx_14cf43634dcd176');
        $this->addSql('ALTER INDEX idx_1989916964cc34ab RENAME TO idx_5c17e7d464cc34ab');
        $this->addSql('ALTER INDEX idx_19899169de091aaf RENAME TO idx_5c17e7d4de091aaf');
        $this->addSql('ALTER INDEX idx_1b151a68275ae721 RENAME TO idx_dfa7d317275ae721');
        $this->addSql('ALTER INDEX idx_1b151a6840549b08 RENAME TO idx_dfa7d31740549b08');
        $this->addSql('ALTER INDEX idx_1b151a68fe54d947 RENAME TO idx_dfa7d317fe54d947');
        $this->addSql('ALTER INDEX idx_1bd9248f275ae721 RENAME TO idx_7b23b581275ae721');
        $this->addSql('ALTER INDEX idx_1bd9248f40549b08 RENAME TO idx_7b23b58140549b08');
        $this->addSql('ALTER INDEX idx_1bd9248fb56e06a RENAME TO idx_7b23b581b56e06a');
        $this->addSql('ALTER INDEX idx_1bd9248fcd1de18a RENAME TO idx_7b23b581cd1de18a');
        $this->addSql('ALTER INDEX idx_1c7e7072e5d6fc0 RENAME TO idx_5661abede5d6fc0');
        $this->addSql('ALTER INDEX idx_1e81f5af2b219d70 RENAME TO idx_a126f4eb2b219d70');
        $this->addSql('ALTER INDEX idx_23d1592834dcd176 RENAME TO idx_c4098ebe34dcd176');
        $this->addSql('ALTER INDEX idx_2452e5ad34dcd176 RENAME TO idx_8b7ec4c334dcd176');
        $this->addSql('ALTER INDEX idx_291bd1d534dcd176 RENAME TO idx_63040a4a34dcd176');
        $this->addSql('ALTER INDEX idx_2b3506a557698a6a RENAME TO idx_c827039557698a6a');
        $this->addSql('ALTER INDEX idx_2b3506a5a50b3b45 RENAME TO idx_c8270395a50b3b45');
        $this->addSql('ALTER INDEX idx_2bc427e023edc87 RENAME TO idx_9cd3debb23edc87');
        $this->addSql('ALTER INDEX idx_2bc427e0275ae721 RENAME TO idx_9cd3debb275ae721');
        $this->addSql('ALTER INDEX idx_2bc427e0d8116475 RENAME TO idx_9cd3debbd8116475');
        $this->addSql('ALTER INDEX idx_2c1954be57698a6a RENAME TO idx_998c05f957698a6a');
        $this->addSql('ALTER INDEX idx_2c1954bedcbb0c53 RENAME TO idx_998c05f9dcbb0c53');
        $this->addSql('ALTER INDEX idx_2d5b0bd9275ae721 RENAME TO idx_5a3b59ea275ae721');
        $this->addSql('ALTER INDEX idx_2d5b0bd940549b08 RENAME TO idx_5a3b59ea40549b08');
        $this->addSql('ALTER INDEX idx_2d71346e3d8e604f RENAME TO idx_929ef9f53d8e604f');
        $this->addSql('ALTER INDEX idx_2d71346e64c19c1 RENAME TO idx_929ef9f564c19c1');
        $this->addSql('ALTER INDEX idx_2e3086f344c8f818 RENAME TO idx_80aa45a44c8f818');
        $this->addSql('ALTER INDEX idx_2e3086f357698a6a RENAME TO idx_80aa45a57698a6a');
        $this->addSql('ALTER INDEX idx_2e5a405044c8f818 RENAME TO idx_593a126344c8f818');
        $this->addSql('ALTER INDEX idx_31e0790e77153098 RENAME TO idx_46802b3d77153098');
        $this->addSql('ALTER INDEX idx_31e0790e89919cbd RENAME TO idx_46802b3d89919cbd');
        $this->addSql('ALTER INDEX idx_31e0790e993853e0 RENAME TO idx_46802b3d993853e0');
        $this->addSql('ALTER INDEX idx_32c3ba1bd044d5d4 RENAME TO idx_34839066d044d5d4');
        $this->addSql('ALTER INDEX idx_386e7b622b219d70 RENAME TO idx_5f0cde632b219d70');
        $this->addSql('ALTER INDEX idx_3b80cfd23a0e66 RENAME TO idx_74d85ece23a0e66');
        $this->addSql('ALTER INDEX idx_3b80cfd55ece9f9 RENAME TO idx_74d85ece55ece9f9');
        $this->addSql('ALTER INDEX idx_3bd68e5f275ae721 RENAME TO idx_9173ee21275ae721');
        $this->addSql('ALTER INDEX idx_3bd68e5f34dcd176 RENAME TO idx_9173ee2134dcd176');
        $this->addSql('ALTER INDEX idx_3d35756639986e43 RENAME TO idx_5a57d06739986e43');
        $this->addSql('ALTER INDEX idx_3d357566d4db71b5 RENAME TO idx_5a57d067d4db71b5');
        $this->addSql('ALTER INDEX idx_4000a7ab2b219d70 RENAME TO idx_eaa5c7d52b219d70');
        $this->addSql('ALTER INDEX idx_40cd24f7368e27c3 RENAME TO idx_553524a368e27c3');
        $this->addSql('ALTER INDEX idx_40cd24f7fbd8e0f8 RENAME TO idx_553524afbd8e0f8');
        $this->addSql('ALTER INDEX idx_4162572c5140dedb RENAME TO idx_bd6cc8a95140dedb');
        $this->addSql('ALTER INDEX idx_4162572ca50b3b45 RENAME TO idx_bd6cc8a9a50b3b45');
        $this->addSql('ALTER INDEX idx_41a84fa964c19c1 RENAME TO idx_f43d1eee64c19c1');
        $this->addSql('ALTER INDEX idx_41a84fa9d4db71b5 RENAME TO idx_f43d1eeed4db71b5');
        $this->addSql('ALTER INDEX idx_45d312103bae0aa7 RENAME TO idx_ef76726e3bae0aa7');
        $this->addSql('ALTER INDEX idx_45d31210d4db71b5 RENAME TO idx_ef76726ed4db71b5');
        $this->addSql('ALTER INDEX idx_467a308223a0e66 RENAME TO idx_ba74af0723a0e66');
        $this->addSql('ALTER INDEX idx_467a3082275ae721 RENAME TO idx_ba74af07275ae721');
        $this->addSql('ALTER INDEX idx_467a3082fbce3e7a RENAME TO idx_ba74af07fbce3e7a');
        $this->addSql('ALTER INDEX idx_4cff8843275ae721 RENAME TO idx_884d413c275ae721');
        $this->addSql('ALTER INDEX idx_4cff8843cd1de18a RENAME TO idx_884d413ccd1de18a');
        $this->addSql('ALTER INDEX idx_4cff8843f92b8b3e RENAME TO idx_884d413cf92b8b3e');
        $this->addSql('ALTER INDEX idx_4efe429857698a6a RENAME TO idx_f15943dc57698a6a');
        $this->addSql('ALTER INDEX idx_4efe42988a90aba9 RENAME TO idx_f15943dc8a90aba9');
        $this->addSql('ALTER INDEX idx_500533f40549b08 RENAME TO idx_d18815f440549b08');
        $this->addSql('ALTER INDEX idx_5082acf1dd39950 RENAME TO idx_233208661dd39950');
        $this->addSql('ALTER INDEX idx_5082acfd4db71b5 RENAME TO idx_23320866d4db71b5');
        $this->addSql('ALTER INDEX idx_5190a61f23a0e66 RENAME TO idx_febc877123a0e66');
        $this->addSql('ALTER INDEX idx_5190a61f8d9f6d38 RENAME TO idx_febc87718d9f6d38');
        $this->addSql('ALTER INDEX idx_527cc8b613b7300a RENAME TO idx_1863132913b7300a');
        $this->addSql('ALTER INDEX idx_527cc8b65ff69b7d RENAME TO idx_186313295ff69b7d');
        $this->addSql('ALTER INDEX idx_53334a5b217bbb47 RENAME TO idx_2607cc36217bbb47');
        $this->addSql('ALTER INDEX idx_544a88d734dcd176 RENAME TO idx_232adae434dcd176');
        $this->addSql('ALTER INDEX idx_550715e740549b08 RENAME TO idx_ffa2759940549b08');
        $this->addSql('ALTER INDEX idx_57031cf23bae0aa7 RENAME TO idx_e8a41db63bae0aa7');
        $this->addSql('ALTER INDEX idx_570d5546217bbb47 RENAME TO idx_206d0775217bbb47');
        $this->addSql('ALTER INDEX idx_570d554640c1fea7 RENAME TO idx_206d077540c1fea7');
        $this->addSql('ALTER INDEX idx_57361c8c71f7e88b RENAME TO idx_710c3e2571f7e88b');
        $this->addSql('ALTER INDEX idx_57361c8c979b1ad6 RENAME TO idx_710c3e25979b1ad6');
        $this->addSql('ALTER INDEX idx_58a328f023a0e66 RENAME TO idx_e70429b423a0e66');
        $this->addSql('ALTER INDEX idx_58a328f034dcd176 RENAME TO idx_e70429b434dcd176');
        $this->addSql('ALTER INDEX idx_59790f334dcd176 RENAME TO idx_23adb25a34dcd176');
        $this->addSql('ALTER INDEX idx_59790f3b746170c RENAME TO idx_23adb25ab746170c');
        $this->addSql('ALTER INDEX idx_59790f3fbce3e7a RENAME TO idx_23adb25afbce3e7a');
        $this->addSql('ALTER INDEX idx_5a5e8ac2140ab620 RENAME TO idx_2b50a84a140ab620');
        $this->addSql('ALTER INDEX idx_5a5e8ac257698a6a RENAME TO idx_2b50a84a57698a6a');
        $this->addSql('ALTER INDEX idx_5b43edfa40c1fea7 RENAME TO idx_64ef60c940c1fea7');
        $this->addSql('ALTER INDEX idx_5b43edfac4663e4 RENAME TO idx_64ef60c9c4663e4');
        $this->addSql('ALTER INDEX idx_5bae632f57698a6a RENAME TO idx_8314f9cb57698a6a');
        $this->addSql('ALTER INDEX idx_5bae632fdcbb0c53 RENAME TO idx_8314f9cbdcbb0c53');
        $this->addSql('ALTER INDEX idx_5c0d265ed4db71b5 RENAME TO idx_aa92441bd4db71b5');
        $this->addSql('ALTER INDEX idx_5c0d265eee87e006 RENAME TO idx_aa92441bee87e006');
        $this->addSql('ALTER INDEX idx_5c6104c923a0e66 RENAME TO idx_1d4bcce423a0e66');
        $this->addSql('ALTER INDEX idx_5c6104c9c5b81ece RENAME TO idx_1d4bcce4c5b81ece');
        $this->addSql('ALTER INDEX idx_5e56eeee23a0e66 RENAME TO idx_e94117b523a0e66');
        $this->addSql('ALTER INDEX idx_5e703e9c25e4c122 RENAME TO idx_e1d73fd825e4c122');
        $this->addSql('ALTER INDEX idx_5e703e9cd34a04ad RENAME TO idx_e1d73fd8d34a04ad');
        $this->addSql('ALTER INDEX idx_600aa4964fbf094f RENAME TO idx_1104861e4fbf094f');
        $this->addSql('ALTER INDEX idx_600aa496bdafd8c8 RENAME TO idx_1104861ebdafd8c8');
        $this->addSql('ALTER INDEX idx_60816cca5126ac48 RENAME TO idx_cfad4da45126ac48');
        $this->addSql('ALTER INDEX idx_60816ccad4db71b5 RENAME TO idx_cfad4da4d4db71b5');
        $this->addSql('ALTER INDEX idx_622510f823a0e66 RENAME TO idx_813715c823a0e66');
        $this->addSql('ALTER INDEX idx_622510f834dcd176 RENAME TO idx_813715c834dcd176');
        $this->addSql('ALTER INDEX idx_64d7a0f3217bbb47 RENAME TO idx_13b7f2c0217bbb47');
        $this->addSql('ALTER INDEX idx_64d7a0f323a0e66 RENAME TO idx_13b7f2c023a0e66');
        $this->addSql('ALTER INDEX idx_675d8afd40549b08 RENAME TO idx_844f8fcd40549b08');
        $this->addSql('ALTER INDEX idx_6c544bd04fbf094f RENAME TO idx_8f464ee04fbf094f');
        $this->addSql('ALTER INDEX idx_6db90653275ae721 RENAME TO idx_27a6ddcc275ae721');
        $this->addSql('ALTER INDEX idx_6db906533bae0aa7 RENAME TO idx_27a6ddcc3bae0aa7');
        $this->addSql('ALTER INDEX idx_6db906535a8b0903 RENAME TO idx_27a6ddcc5a8b0903');
        $this->addSql('ALTER INDEX idx_6db906535e9e89cb RENAME TO idx_27a6ddcc5e9e89cb');
        $this->addSql('ALTER INDEX idx_6db90653dcbb0c53 RENAME TO idx_27a6ddccdcbb0c53');
        $this->addSql('ALTER INDEX idx_6db90653fa2425b9 RENAME TO idx_27a6ddccfa2425b9');
        $this->addSql('ALTER INDEX idx_6dee3f29d4db71b5 RENAME TO idx_27f1e4b6d4db71b5');
        $this->addSql('ALTER INDEX idx_6f9615f34dcd176 RENAME TO idx_e121b6c934dcd176');
        $this->addSql('ALTER INDEX idx_719ee2343d8e604f RENAME TO idx_db3b824a3d8e604f');
        $this->addSql('ALTER INDEX idx_719ee234c1ee637c RENAME TO idx_db3b824ac1ee637c');
        $this->addSql('ALTER INDEX idx_72e9f49bc06ea63 RENAME TO idx_4d3144d6bc06ea63');
        $this->addSql('ALTER INDEX idx_74654fa32d5b0234 RENAME TO idx_56b6d2b2d5b0234');
        $this->addSql('ALTER INDEX idx_74a2a4d247cc8c92 RENAME TO idx_5298867b47cc8c92');
        $this->addSql('ALTER INDEX idx_74a2a4d257698a6a RENAME TO idx_5298867b57698a6a');
        $this->addSql('ALTER INDEX idx_74ab927923a0e66 RENAME TO idx_db87b31723a0e66');
        $this->addSql('ALTER INDEX idx_74ab9279ba4b6de8 RENAME TO idx_db87b317ba4b6de8');
        $this->addSql('ALTER INDEX idx_74ab9279d044d5d4 RENAME TO idx_db87b317d044d5d4');
        $this->addSql('ALTER INDEX idx_7604f9951f1b251e RENAME TO idx_c9eb340e1f1b251e');
        $this->addSql('ALTER INDEX idx_7604f9959846c1a6 RENAME TO idx_c9eb340e9846c1a6');
        $this->addSql('ALTER INDEX idx_7604f995eed90630 RENAME TO idx_c9eb340eeed90630');
        $this->addSql('ALTER INDEX idx_764d867a217bbb47 RENAME TO idx_112f237b217bbb47');
        $this->addSql('ALTER INDEX idx_764d867a8af8ca98 RENAME TO idx_112f237b8af8ca98');
        $this->addSql('ALTER INDEX idx_7699cbd03befe8dd RENAME TO idx_958bcee03befe8dd');
        $this->addSql('ALTER INDEX idx_7699cbd0c3423909 RENAME TO idx_958bcee0c3423909');
        $this->addSql('ALTER INDEX idx_77ee9a8b23a0e66 RENAME TO idx_36c452a623a0e66');
        $this->addSql('ALTER INDEX idx_78b8de6823a0e66 RENAME TO idx_83b0121023a0e66');
        $this->addSql('ALTER INDEX idx_78b8de6897601f83 RENAME TO idx_83b0121097601f83');
        $this->addSql('ALTER INDEX idx_78b8de68c1ee637c RENAME TO idx_83b01210c1ee637c');
        $this->addSql('ALTER INDEX idx_7a4a60aa34dcd176 RENAME TO idx_1d28c5ab34dcd176');
        $this->addSql('ALTER INDEX idx_7a4a60aad044d5d4 RENAME TO idx_1d28c5abd044d5d4');
        $this->addSql('ALTER INDEX idx_7a4a60aad34a04ad RENAME TO idx_1d28c5abd34a04ad');
        $this->addSql('ALTER INDEX idx_7a8ba10d275ae721 RENAME TO idx_ae03e7c6275ae721');
        $this->addSql('ALTER INDEX idx_7a8ba10d40549b08 RENAME TO idx_ae03e7c640549b08');
        $this->addSql('ALTER INDEX idx_7a8ba10dc1ee637c RENAME TO idx_ae03e7c6c1ee637c');
        $this->addSql('ALTER INDEX idx_7c8cba545103debc RENAME TO idx_1bee1f555103debc');
        $this->addSql('ALTER INDEX idx_7c8cba54bc06ea63 RENAME TO idx_1bee1f55bc06ea63');
        $this->addSql('ALTER INDEX idx_7eb3d3344fbf094f RENAME TO idx_9da1d6044fbf094f');
        $this->addSql('ALTER INDEX idx_80dcc4463be452f7 RENAME TO idx_354995013be452f7');
        $this->addSql('ALTER INDEX idx_80dcc446d4db71b5 RENAME TO idx_35499501d4db71b5');
        $this->addSql('ALTER INDEX idx_80e1a5efc4e0a61f RENAME TO idx_cafe7e70c4e0a61f');
        $this->addSql('ALTER INDEX idx_80e1a5efc5eeea34 RENAME TO idx_cafe7e70c5eeea34');
        $this->addSql('ALTER INDEX idx_81e8333d3d8e604f RENAME TO idx_9863dd9b3d8e604f');
        $this->addSql('ALTER INDEX idx_81fa8390651744 RENAME TO idx_718fd80b90651744');
        $this->addSql('ALTER INDEX idx_846b97645bf54558 RENAME TO idx_2ecef71a5bf54558');
        $this->addSql('ALTER INDEX idx_846b9764d4db71b5 RENAME TO idx_2ecef71ad4db71b5');
        $this->addSql('ALTER INDEX idx_85abfcfc29ba651a RENAME TO idx_a391de5529ba651a');
        $this->addSql('ALTER INDEX idx_85abfcfc53563725 RENAME TO idx_a391de5553563725');
        $this->addSql('ALTER INDEX idx_862f14455bf54558 RENAME TO idx_c705dc685bf54558');
        $this->addSql('ALTER INDEX idx_862f1445d4db71b5 RENAME TO idx_c705dc68d4db71b5');
        $this->addSql('ALTER INDEX idx_8862f7ab275ae721 RENAME TO idx_e89866a5275ae721');
        $this->addSql('ALTER INDEX idx_8862f7abde091aaf RENAME TO idx_e89866a5de091aaf');
        $this->addSql('ALTER INDEX idx_8b1e1a24368e27c3 RENAME TO idx_8d5e3059368e27c3');
        $this->addSql('ALTER INDEX idx_8b1e1a24fbd8e0f8 RENAME TO idx_8d5e3059fbd8e0f8');
        $this->addSql('ALTER INDEX idx_8c07e7204c62e638 RENAME TO idx_aa3dc5894c62e638');
        $this->addSql('ALTER INDEX idx_921f6a4a7a999bce RENAME TO idx_a16943297a999bce');
        $this->addSql('ALTER INDEX idx_921f6a4ae67f9749 RENAME TO idx_a1694329e67f9749');
        $this->addSql('ALTER INDEX idx_9419ec3634dcd176 RENAME TO idx_2bbeed7234dcd176');
        $this->addSql('ALTER INDEX idx_9419ec369474526c RENAME TO idx_2bbeed729474526c');
        $this->addSql('ALTER INDEX idx_9c609e245475e8c4 RENAME TO idx_ba5abc8d5475e8c4');
        $this->addSql('ALTER INDEX idx_9d24fe92a412fa92 RENAME TO idx_d73b250da412fa92');
        $this->addSql('ALTER INDEX idx_9d95649e23a0e66 RENAME TO idx_7e8761ae23a0e66');
        $this->addSql('ALTER INDEX idx_9d95649eba4b6de8 RENAME TO idx_7e8761aeba4b6de8');
        $this->addSql('ALTER INDEX idx_9d95649ed044d5d4 RENAME TO idx_7e8761aed044d5d4');
        $this->addSql('ALTER INDEX idx_9dcd01d9275ae721 RENAME TO idx_7a15d64f275ae721');
        $this->addSql('ALTER INDEX idx_9dfb25ad23a0e66 RENAME TO idx_225c24e923a0e66');
        $this->addSql('ALTER INDEX idx_9dfb25ad9b2a6c7e RENAME TO idx_225c24e99b2a6c7e');
        $this->addSql('ALTER INDEX idx_9f7f1f31a76ed395 RENAME TO idx_d560c4aea76ed395');
        $this->addSql('ALTER INDEX idx_a15860b134dcd176 RENAME TO idx_c1a2f1bf34dcd176');
        $this->addSql('ALTER INDEX idx_a15860b13bae0aa7 RENAME TO idx_c1a2f1bf3bae0aa7');
        $this->addSql('ALTER INDEX idx_a15860b15a8600b0 RENAME TO idx_c1a2f1bf5a8600b0');
        $this->addSql('ALTER INDEX idx_a15860b1ef336c0a RENAME TO idx_c1a2f1bfef336c0a');
        $this->addSql('ALTER INDEX idx_a55b26d3e98f2859 RENAME TO idx_464923e3e98f2859');
        $this->addSql('ALTER INDEX idx_a6889f96275ae721 RENAME TO idx_992412a5275ae721');
        $this->addSql('ALTER INDEX idx_a73f51744fbf094f RENAME TO idx_beb4bfd24fbf094f');
        $this->addSql('ALTER INDEX idx_a73f5174bdafd8c8 RENAME TO idx_beb4bfd2bdafd8c8');
        $this->addSql('ALTER INDEX idx_a8ed520790651744 RENAME TO idx_4bff573790651744');
        $this->addSql('ALTER INDEX idx_a8ed5207a8bfe98d RENAME TO idx_4bff5737a8bfe98d');
        $this->addSql('ALTER INDEX idx_a93ff3b04fbf094f RENAME TO idx_d831d1384fbf094f');
        $this->addSql('ALTER INDEX idx_aaaa3f5d044d5d4 RENAME TO idx_f6a43c70d044d5d4');
        $this->addSql('ALTER INDEX idx_aaaa3f5d34a04ad RENAME TO idx_f6a43c70d34a04ad');
        $this->addSql('ALTER INDEX idx_aacf85bca412fa92 RENAME TO idx_6ab15bd8a412fa92');
        $this->addSql('ALTER INDEX idx_ab4bc2b5a917cc69 RENAME TO idx_5dd4a0f0a917cc69');
        $this->addSql('ALTER INDEX idx_ab4bc2b5f8bd700d RENAME TO idx_5dd4a0f0f8bd700d');
        $this->addSql('ALTER INDEX idx_ab8c1ad398197a65 RENAME TO idx_4a03bbd98197a65');
        $this->addSql('ALTER INDEX idx_ae593ae83d8e604f RENAME TO idx_1751b863d8e604f');
        $this->addSql('ALTER INDEX idx_af33b914275ae721 RENAME TO idx_4c21bc24275ae721');
        $this->addSql('ALTER INDEX idx_af33b91440549b08 RENAME TO idx_4c21bc2440549b08');
        $this->addSql('ALTER INDEX idx_af33b914e67f9749 RENAME TO idx_4c21bc24e67f9749');
        $this->addSql('ALTER INDEX idx_afe537c23a0e66 RENAME TO idx_a5d2721223a0e66');
        $this->addSql('ALTER INDEX idx_afe537c8c9f3610 RENAME TO idx_a5d272128c9f3610');
        $this->addSql('ALTER INDEX idx_b18635ed4c62e638 RENAME TO idx_fb99ee724c62e638');
        $this->addSql('ALTER INDEX idx_b18635ed5a8b0903 RENAME TO idx_fb99ee725a8b0903');
        $this->addSql('ALTER INDEX idx_b2c2b61734dcd176 RENAME TO idx_614171ee34dcd176');
        $this->addSql('ALTER INDEX idx_b3cfaf8a22b35429 RENAME TO idx_4fc1300f22b35429');
        $this->addSql('ALTER INDEX idx_b3cfaf8a3d8e604f RENAME TO idx_4fc1300f3d8e604f');
        $this->addSql('ALTER INDEX idx_b6f18c8d40549b08 RENAME TO idx_d193298c40549b08');
        $this->addSql('ALTER INDEX idx_b6f18c8d44c8f818 RENAME TO idx_d193298c44c8f818');
        $this->addSql('ALTER INDEX idx_b75d60a897e625e8 RENAME TO idx_91197e7d97e625e8');
        $this->addSql('ALTER INDEX idx_b75d60a8a50b3b45 RENAME TO idx_91197e7da50b3b45');
        $this->addSql('ALTER INDEX idx_b84689d6fa2425b9 RENAME TO idx_7e18892fa2425b9');
        $this->addSql('ALTER INDEX idx_ba7febfe275ae721 RENAME TO idx_69fc2c07275ae721');
        $this->addSql('ALTER INDEX idx_bb226d8334dcd176 RENAME TO idx_dc40c88234dcd176');
        $this->addSql('ALTER INDEX idx_bb226d839b2a6c7e RENAME TO idx_dc40c8829b2a6c7e');
        $this->addSql('ALTER INDEX idx_bb95f0c0275ae721 RENAME TO idx_479b6f45275ae721');
        $this->addSql('ALTER INDEX idx_bb95f0c034dcd176 RENAME TO idx_479b6f4534dcd176');
        $this->addSql('ALTER INDEX idx_bf71dac5275ae721 RENAME TO idx_437f4540275ae721');
        $this->addSql('ALTER INDEX idx_bf71dac540549b08 RENAME TO idx_437f454040549b08');
        $this->addSql('ALTER INDEX idx_c02a646f4fbf094f RENAME TO idx_7f8d652b4fbf094f');
        $this->addSql('ALTER INDEX idx_c141b25c34dcd176 RENAME TO idx_a1bb235234dcd176');
        $this->addSql('ALTER INDEX idx_c141b25c5288fd4f RENAME TO idx_a1bb23525288fd4f');
        $this->addSql('ALTER INDEX idx_c1b476d623a0e66 RENAME TO idx_22a673e623a0e66');
        $this->addSql('ALTER INDEX idx_c1e0d2ea4fbf094f RENAME TO idx_e7daf0434fbf094f');
        $this->addSql('ALTER INDEX idx_c1e0d2eabb827337 RENAME TO idx_e7daf043bb827337');
        $this->addSql('ALTER INDEX idx_c323d92fa8bfe98d RENAME TO idx_e519fb86a8bfe98d');
        $this->addSql('ALTER INDEX idx_c323d92fe98f2859 RENAME TO idx_e519fb86e98f2859');
        $this->addSql('ALTER INDEX idx_c3a0e9c2969bd765 RENAME TO idx_cfc27abc969bd765');
        $this->addSql('ALTER INDEX idx_c3a0e9c2d4db71b5 RENAME TO idx_cfc27abcd4db71b5');
        $this->addSql('ALTER INDEX idx_c74b77c734dcd176 RENAME TO idx_e9f93a6c34dcd176');
        $this->addSql('ALTER INDEX idx_c7803bf4140ab620 RENAME TO idx_68ac1a9a140ab620');
        $this->addSql('ALTER INDEX idx_c7803bf4d4db71b5 RENAME TO idx_68ac1a9ad4db71b5');
        $this->addSql('ALTER INDEX idx_c97421a93d8e604f RENAME TO idx_2a6624993d8e604f');
        $this->addSql('ALTER INDEX idx_cd0538b023a0e66 RENAME TO idx_310ba73523a0e66');
        $this->addSql('ALTER INDEX idx_cd0538b09474526c RENAME TO idx_310ba7359474526c');
        $this->addSql('ALTER INDEX idx_d1b857707a999bce RENAME TO idx_a48cd11d7a999bce');
        $this->addSql('ALTER INDEX idx_d1b85770e67f9749 RENAME TO idx_a48cd11de67f9749');
        $this->addSql('ALTER INDEX idx_d2817b2275ae721 RENAME TO idx_eaf0c024275ae721');
        $this->addSql('ALTER INDEX idx_d2817b23bae0aa7 RENAME TO idx_eaf0c0243bae0aa7');
        $this->addSql('ALTER INDEX idx_d2817b2bdafd8c8 RENAME TO idx_eaf0c024bdafd8c8');
        $this->addSql('ALTER INDEX idx_d3b47d3923edc87 RENAME TO idx_7c985c5723edc87');
        $this->addSql('ALTER INDEX idx_d3b47d39275ae721 RENAME TO idx_7c985c57275ae721');
        $this->addSql('ALTER INDEX idx_d63d91c03d8e604f RENAME TO idx_16434fa43d8e604f');
        $this->addSql('ALTER INDEX idx_d63d91c064c19c1 RENAME TO idx_16434fa464c19c1');
        $this->addSql('ALTER INDEX idx_d7bbbff9e7b003e9 RENAME TO idx_f1819d50e7b003e9');
        $this->addSql('ALTER INDEX idx_d7bbbff9fe54d947 RENAME TO idx_f1819d50fe54d947');
        $this->addSql('ALTER INDEX idx_d7dc2d65bf5476ca RENAME TO idx_d19c0718bf5476ca');
        $this->addSql('ALTER INDEX idx_d7dc2d65d4db71b5 RENAME TO idx_d19c0718d4db71b5');
        $this->addSql('ALTER INDEX idx_de60caa323a0e66 RENAME TO idx_9f4a028e23a0e66');
        $this->addSql('ALTER INDEX idx_de60caa334dcd176 RENAME TO idx_9f4a028e34dcd176');
        $this->addSql('ALTER INDEX idx_de60caa3c5b81ece RENAME TO idx_9f4a028ec5b81ece');
        $this->addSql('ALTER INDEX idx_dea2e80d34dcd176 RENAME TO idx_f898caa434dcd176');
        $this->addSql('ALTER INDEX idx_dea2e80d56f7f6c1 RENAME TO idx_f898caa456f7f6c1');
        $this->addSql('ALTER INDEX idx_dea2e80dd044d5d4 RENAME TO idx_f898caa4d044d5d4');
        $this->addSql('ALTER INDEX idx_dead364e275ae721 RENAME TO idx_94b2edd1275ae721');
        $this->addSql('ALTER INDEX idx_df4f252e9b2a6c7e RENAME TO idx_3c5d201e9b2a6c7e');
        $this->addSql('ALTER INDEX idx_e067eda934dcd176 RENAME TO idx_dfcb609a34dcd176');
        $this->addSql('ALTER INDEX idx_e0ead7e439986e43 RENAME TO idx_aaf50c7b39986e43');
        $this->addSql('ALTER INDEX idx_e28dc6a734dcd176 RENAME TO idx_c4b7e40e34dcd176');
        $this->addSql('ALTER INDEX idx_e52904e923edc87 RENAME TO idx_4a05258723edc87');
        $this->addSql('ALTER INDEX idx_e52904e9275ae721 RENAME TO idx_4a052587275ae721');
        $this->addSql('ALTER INDEX idx_e52904e9abc1f7fe RENAME TO idx_4a052587abc1f7fe');
        $this->addSql('ALTER INDEX idx_e55d74e95a8b0903 RENAME TO idx_923d26da5a8b0903');
        $this->addSql('ALTER INDEX idx_e55d74e95ff69b7d RENAME TO idx_923d26da5ff69b7d');
        $this->addSql('ALTER INDEX idx_e55d74e9ef336c0a RENAME TO idx_923d26daef336c0a');
        $this->addSql('ALTER INDEX idx_e826b22bc91f416 RENAME TO idx_6e78fa2cbc91f416');
        $this->addSql('ALTER INDEX idx_e864e28857698a6a RENAME TO idx_b76e7b857698a6a');
        $this->addSql('ALTER INDEX idx_e864e288a412fa92 RENAME TO idx_b76e7b8a412fa92');
        $this->addSql('ALTER INDEX idx_eb7471495a8b0903 RENAME TO idx_2b0aaf2d5a8b0903');
        $this->addSql('ALTER INDEX idx_ebc7a15ec1ee637c RENAME TO idx_10cf6d26c1ee637c');
        $this->addSql('ALTER INDEX idx_ecb53745b0ccc06 RENAME TO idx_8c4fa64bb0ccc06');
        $this->addSql('ALTER INDEX idx_ecb53745f50d82f4 RENAME TO idx_8c4fa64bf50d82f4');
        $this->addSql('ALTER INDEX idx_ecc72b91275ae721 RENAME TO idx_43eb0aff275ae721');
        $this->addSql('ALTER INDEX idx_ecc72b9140549b08 RENAME TO idx_43eb0aff40549b08');
        $this->addSql('ALTER INDEX idx_ecc72b91fbce3e7a RENAME TO idx_43eb0afffbce3e7a');
        $this->addSql('ALTER INDEX idx_f274823323a0e66 RENAME TO idx_d4309ce623a0e66');
        $this->addSql('ALTER INDEX idx_f278b52b40549b08 RENAME TO idx_47ede46c40549b08');
        $this->addSql('ALTER INDEX idx_f58c0cf05288fd4f RENAME TO idx_5aa02d9e5288fd4f');
        $this->addSql('ALTER INDEX idx_f58c0cf0d4db71b5 RENAME TO idx_5aa02d9ed4db71b5');
        $this->addSql('ALTER INDEX idx_f70c353a94a4c7d4 RENAME TO idx_f14c1f4794a4c7d4');
        $this->addSql('ALTER INDEX idx_f70c353aa917cc69 RENAME TO idx_f14c1f47a917cc69');
        $this->addSql('ALTER INDEX idx_f8bb1ad040549b08 RENAME TO idx_c71797e340549b08');
        $this->addSql('ALTER INDEX idx_f8bb1ad0bb827337 RENAME TO idx_c71797e3bb827337');
        $this->addSql('ALTER INDEX idx_f8bb1ad0d4e6f81 RENAME TO idx_c71797e3d4e6f81');
        $this->addSql('ALTER INDEX idx_f8bb1ad0e67f9749 RENAME TO idx_c71797e3e67f9749');
        $this->addSql('ALTER INDEX idx_fad966e836ac99f1 RENAME TO idx_c4604ad36ac99f1');
        $this->addSql('ALTER INDEX idx_fad966e8d4db71b5 RENAME TO idx_c4604add4db71b5');
        $this->addSql('ALTER INDEX idx_fc69f822ee87e006 RENAME TO idx_56cc985cee87e006');

        $this->addSql('ALTER INDEX uniq_161f168a4fbf094f RENAME TO uniq_671134024fbf094f');
        $this->addSql('ALTER INDEX uniq_169424d9d7df1668 RENAME TO uniq_f14cf34fd7df1668');
        $this->addSql('ALTER INDEX uniq_17c6034d34dcd176 RENAME TO uniq_8ae87a7d34dcd176');
        $this->addSql('ALTER INDEX uniq_18c71a90e00cedde RENAME TO uniq_14cf436e00cedde');
        $this->addSql('ALTER INDEX uniq_1bd9248ff59262fa RENAME TO uniq_7b23b581f59262fa');
        $this->addSql('ALTER INDEX uniq_2c2b523dd4e6f81 RENAME TO uniq_35a0bc9bd4e6f81');
        $this->addSql('ALTER INDEX uniq_2c2b523dff975952 RENAME TO uniq_35a0bc9bff975952');
        $this->addSql('ALTER INDEX uniq_42be451dac74095a RENAME TO uniq_8a19e82ac74095a');
        $this->addSql('ALTER INDEX uniq_47009a039f79558f RENAME TO uniq_f8a79b479f79558f');
        $this->addSql('ALTER INDEX uniq_51cbfb6077153098 RENAME TO uniq_9579321f77153098');
        $this->addSql('ALTER INDEX uniq_51edd51c3950b5f6 RENAME TO uniq_ee0218873950b5f6');
        $this->addSql('ALTER INDEX uniq_5a6eaf58f62e912c RENAME TO uniq_43e541fef62e912c');
        $this->addSql('ALTER INDEX uniq_644a492b34dcd176 RENAME TO uniq_328ec2a34dcd176');
        $this->addSql('ALTER INDEX uniq_6dee3f2957f1d4b RENAME TO uniq_27f1e4b657f1d4b');
        $this->addSql('ALTER INDEX uniq_6dee3f2977153098 RENAME TO uniq_27f1e4b677153098');
        $this->addSql('ALTER INDEX uniq_6dee3f29d4e6f81 RENAME TO uniq_27f1e4b6d4e6f81');
        $this->addSql('ALTER INDEX uniq_71dd644d97ae0266 RENAME TO uniq_ce32a9d697ae0266');
        $this->addSql('ALTER INDEX uniq_7eb3d3343bae0aa7 RENAME TO uniq_9da1d6043bae0aa7');
        $this->addSql('ALTER INDEX uniq_9da47df9d4e6f81 RENAME TO uniq_842f935fd4e6f81');
        $this->addSql('ALTER INDEX uniq_a73f51745475e8c4 RENAME TO uniq_beb4bfd25475e8c4');
        $this->addSql('ALTER INDEX uniq_b4bb963934dcd176 RENAME TO uniq_57a9930934dcd176');
        $this->addSql('ALTER INDEX uniq_b766f425126ac48 RENAME TO uniq_cb08b1265126ac48');
        $this->addSql('ALTER INDEX uniq_b84689d628b6a9f5 RENAME TO uniq_7e1889228b6a9f5');
        $this->addSql('ALTER INDEX uniq_b84689d67b20d77e RENAME TO uniq_7e188927b20d77e');
        $this->addSql('ALTER INDEX uniq_bc8b71d577153098 RENAME TO uniq_5f9974e577153098');
        $this->addSql('ALTER INDEX uniq_c1b476d655ece9f9 RENAME TO uniq_22a673e655ece9f9');
        $this->addSql('ALTER INDEX uniq_c2a4bb6077153098 RENAME TO uniq_2da650477153098');
        $this->addSql('ALTER INDEX uniq_ecffc0c346da7061 RENAME TO uniq_9b9f92f046da7061');
        $this->addSql('ALTER INDEX uniq_ed59b69bd4e6f81 RENAME TO uniq_f4d2583dd4e6f81');
        $this->addSql('ALTER INDEX uniq_fbde6a8fc6d72bb5 RENAME TO uniq_8ad04807c6d72bb5');
        $this->addSql('ALTER INDEX uniq_fbde6a8fc8509941 RENAME TO uniq_8ad04807c8509941');
        $this->addSql('ALTER INDEX uniq_fc69f8225288fd4f RENAME TO uniq_56cc985c5288fd4f');
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
