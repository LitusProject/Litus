<?php

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
            'aliases' => array(
                'groupname'                  => Component\Validator\GroupName::class,
                'groupName'                  => Component\Validator\GroupName::class,
                'GroupName'                  => Component\Validator\GroupName::class,
                'studyexternalid'            => Component\Validator\Study\ExternalId::class,
                'studyExternalId'            => Component\Validator\Study\ExternalId::class,
                'StudyExternalId'            => Component\Validator\Study\ExternalId::class,
                'studyModuleGroupExternalId' => Component\Validator\Study\ModuleGroup\ExternalId::class,
                'StudyModuleGroupExternalId' => Component\Validator\Study\ModuleGroup\ExternalId::class,
                'subjectcode'                => Component\Validator\Subject\Code::class,
                'subjectCode'                => Component\Validator\Subject\Code::class,
                'SubjectCode'                => Component\Validator\Subject\Code::class,
                'subjectmodulegroup'         => Component\Validator\Subject\ModuleGroup::class,
                'subjectModuleGroup'         => Component\Validator\Subject\ModuleGroup::class,
                'SubjectModuleGroup'         => Component\Validator\Subject\ModuleGroup::class,
                'typeaheadstudy'             => Component\Validator\Typeahead\Study::class,
                'typeaheadStudy'             => Component\Validator\Typeahead\Study::class,
                'TypeaheadStudy'             => Component\Validator\Typeahead\Study::class,
                'typeaheadstudymodulegroup'  => Component\Validator\Typeahead\Study\ModuleGroup::class,
                'typeaheadStudyModuleGroup'  => Component\Validator\Typeahead\Study\ModuleGroup::class,
                'TypeaheadStudyModuleGroup'  => Component\Validator\Typeahead\Study\ModuleGroup::class,
                'typeaheadsubject'           => Component\Validator\Typeahead\Subject::class,
                'typeaheadSubject'           => Component\Validator\Typeahead\Subject::class,
                'TypeaheadSubject'           => Component\Validator\Typeahead\Subject::class,
            ),
        ),
    )
);
