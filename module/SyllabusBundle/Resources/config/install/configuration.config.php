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
        'key'         => 'syllabus.update_socket_file',
        'value'       => 'tcp://127.0.0.1:8898',
        'description' => 'The file used for the WebSocket of the syllabus update',
    ),
    array(
        'key'         => 'syllabus.update_socket_public',
        'value'       => ':8898',
        'description' => 'The public address for the WebSocket of the syllabus update',
    ),
    array(
        'key'         => 'syllabus.update_socket_key',
        'value'       => md5(uniqid(rand(), true)),
        'description' => 'The key used for the WebSocket of the queue',
    ),
    array(
        'key'         => 'syllabus.update_socket_enabled',
        'value'       => '1',
        'description' => 'Whether the queue socket is enabled',
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
        'value'       => 'https://onderwijsaanbod.kuleuven.be/{{ year }}/opleidingen/n/xml/index.xml',
        'description' => 'The KU Leuven education URL',
    ),
    array(
        'key'         => 'syllabus.department_url',
        'value'       => 'https://onderwijsaanbod.kuleuven.be/{{ year }}/opleidingen/{{ language }}/xml/CQ_{{ id }}.xml',
        'description' => 'The department URL',
    ),
    array(
        'key'         => 'syllabus.study_url',
        'value'       => 'https://onderwijsaanbod.kuleuven.be/{{ year }}/opleidingen/{{ language }}/xml/SC_{{ id }}.xml',
        'description' => 'The study URL',
    ),
    array(
        'key'         => 'syllabus.persons_api_url',
        'value'       => 'https://webwsp.aps.kuleuven.be/esap/public/odata/sap/zh_person_srv/Persons',
        'description' => 'The KU Leuven Persons API URL',
    ),
    array(
        'key'         => 'syllabus.enable_update',
        'value'       => '0',
        'description' => 'Enable Syllabus update',
    ),
);
