<?php

return array(
    array(
        'key'         => 'syllabus.update_socket_file',
        'value'       => 'tcp://127.0.0.1:8898',
        'description' => 'The file used for the websocket of the syllabus update',
    ),
    array(
        'key'         => 'syllabus.update_socket_public',
        'value'       => '127.0.0.1:8898',
        'description' => 'The public address for the websocket of the syllabus update',
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
);
