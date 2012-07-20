<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
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
	            	'key'         => 'mail.start_cudi_mail_subject',
	            	'value'       => '[VTK Cursusdienst] Cursussen 1e semester academiejaar 2011-2012',
	            	'description' => 'The subject of the mail send to profs at the start of a new semester',
	            ),
	            array(
	            	'key'         => 'mail.start_cudi_mail',
	            	'value'       => 'Geachte professor,
Geachte docent,

Net zoals elk jaar verdeelt VTK (studentenkring burgerlijk ingenieur(-architect)) studiemateriaal onder alle studenten aan de faculteit ingenieurswetenschappen. U ontvangt deze mail omdat het belangrijk is dat we tijdig over de juiste informatie beschikken, zo kunnen we de studenten in het begin van het academiejaar zo snel mogelijk verder helpen. Ook indien wij uw cursus ongewijzigd mogen heruitgeven, wachten wij hiervoor op uw bericht.
Het gaat om volgende vakken:

{{ subjects }}

Om uw cursussen eenvoudig te kunnen beheren, bieden wij u graag onze webapplicatie aan.
Deze webapplicatie is beschikbaar op http://www.vtk.be/prof/cursusdienst.
U kunt hierop inloggen met uw u-nummer en paswoord via de centrale KULeuven log-in.
Indien u niets veranderd hebt aan het origineel van afgelopen academiejaar, gelieve dan toch de nodige gegevens in te vullen in de webapplicatie.

Graag hadden wij tegen 1 september de originelen in ons bezit gehad zodat we ze tijdig kunnen laten drukken en de cursussen tegen het begin van het semester beschikbaar zijn. Indien dit niet mogelijk is hopen wij ze zo snel mogelijk te kunnen ontvangen, maar kunnen we niet verzekeren dat deze tijdig beschikbaar zullen zijn.

Indien u reeds uw cursussen heeft doorgegeven, of als deze mail niet voor u bestemd is, wensen wij ons te excuseren voor de overlast.

Bij vragen kan u ons altijd mailen op cursusdienst@vtk.be

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
	                'admin_mail' => array(
	                    'groups', 'send'
	                ),
	                'admin_mail_prof' => array(
	                    'cudi', 'send'
	                ),
	            )
	        )
	    );
	    
	    $this->installRoles(
	        array(
	        )
	    );
	}
}