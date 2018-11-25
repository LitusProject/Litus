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
        'key'   => 'sport.points_criteria',
        'value' => serialize(
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
    array(
        'key'         => 'sport.reward_time_limit',
        'value'       => '87',
        'description' => 'The lap time limit to get the reward (in seconds)',
    ),
);
