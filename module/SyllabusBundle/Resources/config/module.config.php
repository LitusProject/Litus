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
 *
 * @license http://litus.cc/LICENSE
 */

namespace SyllabusBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('validator'),
    ),
    array(
        'validators' => array(
            'invokables' => array(
                'syllabus_group_name'                     => 'SyllabusBundle\Component\Validator\Group\Name',
                'syllabus_study_external_id'              => 'SyllabusBundle\Component\Validator\Study\ExternalId',
                'syllabus_study_recursion'                => 'SyllabusBundle\Component\Validator\Study\Recursion',
                'syllabus_study_module-group_external_id' => 'SyllabusBundle\Component\Validator\Study\ModuleGroup\ExternalId',
                'syllabus_subject_code'                   => 'SyllabusBundle\Component\Validator\Subject\Code',
                'syllabus_subject_study'                  => 'SyllabusBundle\Component\Validator\Subject\Study',
                'syllabus_typeahead_study'                => 'SyllabusBundle\Component\Validator\Typeahead\Study',
                'syllabus_typeahead_subject'              => 'SyllabusBundle\Component\Validator\Typeahead\Subject',
                'syllabus_typeahead_study_module-group'   => 'SyllabusBundle\Component\Validator\Typeahead\ModuleGroup',
            ),
        ),
    )
);
