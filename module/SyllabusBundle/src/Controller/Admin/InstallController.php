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
 
namespace SyllabusBundle\Controller\Admin;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    DateInterval,
    DateTime;

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
                    'key'         => 'syllabus.update_socket_port',
                    'value'       => '8898',
                    'description' => 'The port used for the websocket of the syllabus update',
                ),
                array(
                    'key'         => 'syllabus.update_socket_remote_host',
                    'value'       => '127.0.0.1',
                    'description' => 'The remote host for the websocket of the syllabus update',
                ),
                array(
                    'key'         => 'syllabus.update_socket_host',
                    'value'       => '127.0.0.1',
                    'description' => 'The host used for the websocket of the syllabus update',
                ),
                array(
                    'key'         => 'search_max_results',
                    'value'       => '30',
                    'description' => 'The maximum number of search results shown',
                ),
                array(
                    'key'         => 'syllabus.xml_url',
                    'value'       => serialize(
                        array(
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016934.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016770.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016996.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016995.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016994.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016994.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51014044.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016991.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016990.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_50999914.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51017068.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016873.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51360430.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51370066.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51520356.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51384442.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51479778.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51379853.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51017034.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016868.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51547941.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51547571.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51017016.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51230411.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51527879.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51518550.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51017033.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016866.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51017013.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51976691.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016877.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51016983.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51976515.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51550182.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51196392.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51017014.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016928.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_50999176.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016867.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51228300.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016933.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51016883.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016773.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51551389.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51016989.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51016862.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51926826.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51017067.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51923163.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51924142.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016932.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51016874.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51016875.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51016891.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51016778.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51016865.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51016880.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51880042.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016777.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51016979.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_50046649.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_50046650.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51017062.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016721.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016956.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51094625.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51017080.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51671703.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016938.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51016957.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_51562161.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/e/xml/SC_51977178.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_50630363.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_50630367.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_50629737.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_50630369.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_50630435.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_50630449.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_50630309.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_50630314.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_50630521.xml',
                            'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/SC_50630513.xml',
                        )
                    ),
                    'description' => 'The url to the xml',
                ),
            )
        );
        
        $this->_installAcademicYear();
    }
    
    protected function initAcl()
    {
        $this->installAcl(
            array(
                'syllabusbundle' => array(
                    'admin_prof' => array(
                        'add', 'delete', 'typeahead'
                    ),
                    'admin_study' => array(
                        'manage', 'search'
                    ),
                    'admin_subject' => array(
                        'manage', 'search', 'subject', 'typeahead'
                    ),
                    'admin_update_syllabus' => array(
                        'index'
                    ),
                )
            )
        );
        
        $this->installRoles(
            array(
                'prof' => array(
                    'system' => true,
                    'parents' => array(
                        'guest',
                    ),
                    'actions' => array(
                    ),
                ),
                'cudi' => array(
                    'system' => true,
                    'parents' => array(
                    ),
                    'actions' => array(
                        'admin_prof' => array(
                            'add', 'delete', 'typeahead'
                        ),
                        'admin_study' => array(
                            'manage', 'search'
                        ),
                        'admin_subject' => array(
                            'manage', 'search', 'subject', 'typeahead'
                        ),
                    )
                ),
                'cudi_groco' => array(
                    'system' => true,
                    'parents' => array(
                    ),
                    'actions' => array(
                        'admin_update_syllabus' => array(
                            'index'
                        ),
                    )
                ),
            )
        );
    }
    
    private function _installAcademicYear()
    {
        $now = new DateTime('now');
        $startAcademicYear = AcademicYear::getStartOfAcademicYear(
            $now
        );

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($startAcademicYear);
            
        $organizationStart = str_replace(
            '{{ year }}',
            $startAcademicYear->format('Y'),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('start_organization_year')
        );
        $organizationStart = new DateTime($organizationStart);

        if (null === $academicYear) {
            $academicYear = new AcademicYearEntity($organizationStart, $startAcademicYear);
            $this->getEntityManager()->persist($academicYear);
            $this->getEntityManager()->flush();
        }
        
        $organizationStart->add(
            new DateInterval('P1Y')
        );
        
        if ($organizationStart < new DateTime()) {
            $academicYear = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\AcademicYear')
                ->findOneByStart($organizationStart);
            if (null == $academicYear) {
                $startAcademicYear = AcademicYear::getEndOfAcademicYear(
                    $organizationStart
                );
                $academicYear = new AcademicYearEntity($organizationStart, $startAcademicYear);
                $this->getEntityManager()->persist($academicYear);
                $this->getEntityManager()->flush();
            }
        }
    }
}
