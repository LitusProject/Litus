<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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
                    'key'         => 'syllabus.queue_socket_key',
                    'value'       => md5(uniqid(rand(), true)),
                    'description' => 'The key used for the websocket of the queue',
                ),
                array(
                    'key'         => 'search_max_results',
                    'value'       => '30',
                    'description' => 'The maximum number of search results shown',
                ),
                array(
                    'key'         => 'syllabus.department_ids',
                    'value'       => serialize(array(50000486)),
                    'description' => 'The ids of the departments to be imported',
                ),
                array(
                    'key'         => 'syllabus.root_xml',
                    'value'       => 'http://onderwijsaanbod.kuleuven.be/opleidingen/n/xml/index.xml',
                    'description' => 'The root XML of KU Leuven',
                ),
                array(
                    'key'         => 'syllabus.department_url',
                    'value'       => 'http://onderwijsaanbod.kuleuven.be/opleidingen/{{ language }}/xml/CQ_{{ id }}.xml',
                    'description' => 'The department url',
                ),
                array(
                    'key'         => 'syllabus.study_url',
                    'value'       => 'http://onderwijsaanbod.kuleuven.be/opleidingen/{{ language }}/xml/SC_{{ id }}.xml',
                    'description' => 'The department url',
                ),
                array(
                    'key'         => 'syllabus.enable_update',
                    'value'       => '0',
                    'description' => 'Enable Syllabus update',
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
                    'syllabus_admin_prof' => array(
                        'add', 'delete', 'typeahead'
                    ),
                    'syllabus_admin_academic' => array(
                        'addStudy', 'addSubject', 'deleteStudy', 'deleteSubject', 'edit', 'manage', 'search'
                    ),
                    'syllabus_admin_group' => array(
                        'add', 'delete', 'deleteStudy', 'edit', 'manage', 'studies'
                    ),
                    'syllabus_admin_study' => array(
                        'edit', 'manage', 'search', 'searchSubject', 'typeahead'
                    ),
                    'syllabus_admin_subject' => array(
                        'edit', 'manage', 'search', 'typeahead'
                    ),
                    'syllabus_admin_subject_comment' => array(
                        'delete', 'manage', 'subject', 'reply'
                    ),
                    'syllabus_admin_update' => array(
                        'index', 'updateNow'
                    ),
                    'syllabus_subject' => array(
                        'typeahead'
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
                'guest' => array(
                    'system' => true,
                    'actions' => array(
                        'syllabus_subject' => array(
                            'typeahead'
                        ),
                    ),
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
