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

namespace QuizBundle\Controller\Admin;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function initConfig()
    {
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'quizbundle' => array(
                    'quiz_admin_quiz' => array(
                        'manage', 'add', 'delete', 'edit'
                    ),
                    'quiz_admin_round' => array(
                        'manage', 'add', 'delete', 'edit'
                    ),
                    'quiz_admin_team' => array(
                        'manage', 'add', 'delete', 'edit'
                    ),
                    'quiz_quiz' => array(
                        'manage', 'update', 'view', 'results'
                    )
                ),
            )
        );
    }
}
