<?php
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

return array(
    array(
        'key'         => 'secretary.enable_registration',
        'value'       => '1',
        'description' => 'Flag whether the registration module is enabled or not',
    ),
    array(
        'key'         => 'secretary.enable_other_organization',
        'value'       => '0',
        'description' => 'Flag whether the "other organization" option is enabled',
    ),
    array(
        'key'         => 'secretary.mail',
        'value'       => 'secretaris@vtk.be',
        'description' => 'The mail address the secretary mails will be send from',
    ),
    array(
        'key'         => 'secretary.mail_name',
        'value'       => 'Student IT',
        'description' => 'The mail address name',
    ),
    array(
        'key'   => 'secretary.membership_article',
        'value' => serialize(
            array(
                1 => 427,
            )
        ),
        'description' => 'The article for the membership',
    ),
    array(
        'key'   => 'secretary.terms_and_conditions',
        'value' => serialize(
            array(
                'nl' => 'Aan het lidmaatschap zijn een aantal gebruiksvoorwaarden verbonden:

* Deze voorwaarden zijn van toepassing op iedere overeenkomst tussen het lid en Student IT vzw, hierna Student IT genoemd. Het lid heeft deze voorwaarden van toepassing verklaard voor zover van deze voorwaarden niet door partijen uitdrukkelijk en schriftelijk is afgeweken.

* Indien onduidelijkheden blijken omtrent de interpretatie van een of meerdere bepalingen van deze voorwaarden, dient men de bepaling naar de geest van deze voorwaarden te interpreteren.

* Indien zich tussen de partijen een omstandigheid voordoet die niet geregeld is in deze bepalingen, dan dient deze beoordeeld te worden naar de geest van de algemene voorwaarden.

* Ieder lid dat deze overeenkomst ondertekent, verklaart zich akkoord met de analyses die gemaakt worden via de lidkaarten, waarbij de privacy gewaarborgd blijft. De gegevens zullen enkel door Student IT gebruikt worden en niet aan derden worden doorgegeven. Er zullen geen identiteitsgegevens worden vrijgegeven.

* Ieder lid dat deze overeenkomst ondertekent, verklaart zich akkoord dat Student IT afstand doet van elke actie van een lid die schade berokkent aan Student IT. Geen lid zal Student IT kunnen vertegenwoordigen zonder uitdrukkelijke toestemming. Diegene die zich op deze toestemming beroept, zal dit moeten bewijzen. Ieder lid dat deze overeenkomst ondertekent, mag aanwezig zijn op de Algemene Vergadering van Student IT.

* Ieder lid kan via schriftelijk schrijven aan de secretaris zijn lidmaatschap ten allen tijde opzeggen. Ook Student IT kan steeds, omwille van gegronde redenen, de overeenkomst doen ontbinden.

* Op deze overeenkomst en alle overeenkomsten en geschillen die daaruit voortvloeien, is enkel Belgisch recht van toepassing.',
                'en' => 'The terms of use of the membership are:

* These conditions apply to all contracts between the member and the Student IT vzw, hereafter called Student IT. The member has declared these conditions applicable if the parties have not - explicitly and in writing - decided otherwise.

* If ambiguities appear on the interpretation of any of these conditions, the conditions should be interpreted according to the spirit of these terms.

* If between the parties, a circumstance occurs that is not governed by those provisions, then it should be judged by the spirit of the general conditions.

* Each member signing this agreement, agrees with the analyses made ​​through the membership cards, where privacy remains guaranteed. The information will only be used by Student IT and will not be passed to third parties. Identity information will not be released.

* Each member signing this agreement agrees that Student IT waives any action by a member that causes damage to Student IT. No member can represent Student IT without explicit permission. Those who rely on this authorization, will have to prove it.

* Every member who signs this agreement, may be present at the General Assembly (Algemene Vergadering) of Student IT.

* Each member may put a hold to his membership at any time by a written demand  to the Secretary. Also Student IT can always, for serious reasons, terminate the Agreement.

* On this agreement and all contracts and disputes arising therefrom, only Belgian law applies.',
            )
        ),
        'description' => 'The organization\'s terms and conditions',
    ),
    array(
        'key'         => 'secretary.date_info_message_interval',
        'value'       => 'P15D',
        'description' => 'The date info message is displayed from this amount of days before the start of the new organizational year.',
    ),
    array(
        'key'         => 'secretary.isic_membership',
        'value'       => '0',
        'description' => 'Set this to 1 if ISIC cards are needed for membership. People who want to become member will be redirected to the ISIC form.',
    ),
);
