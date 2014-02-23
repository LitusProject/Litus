<?php

return array(
    array(
        'key'         => 'sport.run_result_page',
        'value'       => 'http://live.24urenloop.be/tussenstand.json',
        'description' => 'The URL where the official result page can be found',
    ),
    array(
        'key'         => 'sport.run_team_id',
        'value'       => '4',
        'description' => 'The ID of the organization on the official result page',
    ),
    array(
        'key'         => 'sport.queue_socket_file',
        'value'       => 'tcp://127.0.0.1:8897',
        'description' => 'The file used for the websocket of the queue',
    ),
    array(
        'key'         => 'sport.queue_socket_public',
        'value'       => '127.0.0.1:8897',
        'description' => 'The public address for the websocket of the queue',
    ),
    array(
        'key'         => 'sport.queue_socket_key',
        'value'       => md5(uniqid(rand(), true)),
        'description' => 'The key used for the websocket of the queue',
    ),
    array(
        'key'         => 'sport.points_criteria',
        'value'       => serialize(
            array(
                array(
                    'limit'  => '90',
                    'points' => '1',
                ),
                array(
                    'limit'  => '87',
                    'points' => '3',
                ),
                array(
                    'limit'  => '84',
                    'points' => '4',
                ),
                array(
                    'limit'  => '81',
                    'points' => '5',
                ),
                array(
                    'limit'  => '79',
                    'points' => '6',
                ),
            )
        ),
        'description' => 'The criteria for the lap times that determine the number of points it is worth (times should decrease)',
    ),
);
