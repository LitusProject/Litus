<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace MailBundle\Controller\Admin;

use Exception;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function initConfig()
    {
        $this->installConfig(
            array(
                array(
                    'key'         => 'mail.bakske_mail',
                    'value'       => 'bakske@vtk.be',
                    'description' => 'The mail address from which Het Bakske is sent',
                ),
                array(
                    'key'         => 'mail.bakske_mail_name',
                    'value'       => 'Het Bakske',
                    'description' => 'The mail address name from which Het Bakske is sent',
                ),
                array(
                    'key'         => 'mail.start_cudi_mail_subject',
                    'value'       => '[VTK Cursusdienst] Cursussen {{ semester }} Semester Academiejaar {{ academicYear }}',
                    'description' => 'The subject of the mail send to profs at the start of a new semester',
                ),
                array(
                    'key'         => 'mail.start_cudi_mail',
                    'value'       => 'Geachte professor,
Geachte docent,

Net zoals elk jaar verdeelt VTK (studentenkring burgerlijk ingenieur(-architect)) studiemateriaal onder alle studenten aan de faculteit ingenieurswetenschappen. Meer informatie over onze werking kan u vinden op: http://praesidium.vtk.be/~tvandervoorde/brochure.pdf.
U ontvangt deze mail omdat het belangrijk is dat we tijdig over de juiste informatie beschikken, zo kunnen we de studenten in het begin van het academiejaar zo snel mogelijk verder helpen. Ook indien wij uw cursus ongewijzigd mogen heruitgeven, wachten wij hiervoor op uw bericht.
Het gaat om volgende vakken:

{{ subjects }}

Om uw cursussen eenvoudig te kunnen beheren, bieden wij u graag onze webapplicatie aan.
Deze webapplicatie is beschikbaar op https://vtk.be/cudi/prof.
U kunt hierop inloggen met uw u-nummer en paswoord via de centrale KULeuven log-in.
Indien u niets veranderd hebt aan het origineel van afgelopen academiejaar, gelieve dan toch de nodige gegevens in te vullen in de webapplicatie.
Dit jaar werken we immers met een totaal nieuwe applicatie en is het belangrijk om over de juiste informatie te beschikken.

Graag hadden wij tegen 1 september de originelen in ons bezit gehad zodat we ze tijdig kunnen laten drukken en de cursussen tegen het begin van het semester beschikbaar zijn. Indien dit niet mogelijk is hopen wij ze zo snel mogelijk te kunnen ontvangen, maar kunnen we niet verzekeren dat deze tijdig beschikbaar zullen zijn.

Indien u ook nog enkele handboeken door omstandigheden niet hebt kunnen doorgeven vragen wij om dit zo snel mogelijk te doen aangezien de deadline hiervoor 8 augustus is. Indien wij handboeken later bestellen kan men ons immers niet garanderen dat ze bij het begin van het academiejaar aanwezig zullen zijn.

Indien u reeds uw cursussen en of handboeken heeft doorgegeven, of als deze mail niet voor u bestemd is, wensen wij ons te excuseren voor de overlast.

Bij vragen kan u ons altijd mailen op cursusdienst@vtk.be.

Met vriendelijke groeten en hartelijk dank bij voorbaat,

Tom Van der Voorde,
Philippe Blondeel,
Jorn Hendrickx',
                    'description' => 'The mail send to profs at the start of a new semester',
                ),
            )
        );
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'mailbundle' => array(
                    'mail_admin_mail' => array(
                        'groups', 'send'
                    ),
                    'mail_admin_mail_list' => array(
                        'manage', 'add', 'entries', 'admins', 'delete', 'deleteEntry', 'deleteAdmin'
                    ),
                    'mail_admin_mail_alias' => array(
                        'manage', 'add', 'delete',
                    ),
                    'amail_dmin_mail_prof' => array(
                        'cudi'
                    ),
                    'mail_admin_mail_study' => array(
                        'send'
                    ),
                    'mail_admin_mail_bakske' => array(
                        'send'
                    ),
                    'mail_admin_mail_volunteer' => array(
                        'send'
                    ),
                )
            )
        );
    }
}
