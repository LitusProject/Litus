<?php

return array(
    'brbundle' => array(
        'br_admin_collaborator' => array(
            'add', 'retire', 'edit', 'manage', 'rehire',
        ),
        'br_admin_event' => array(
            'add', 'delete', 'edit', 'manage', 'old', 'deleteAttendee'
        ),
        'br_admin_company' => array(
            'add', 'delete', 'editLogo', 'edit', 'manage', 'search', 'upload', 'csv', 'pdf'
        ),
        'br_admin_company_event' => array(
            'add', 'delete', 'edit', 'editPoster', 'manage', 'upload',
        ),
        'br_admin_company_job' => array(
            'add', 'delete', 'edit', 'manage',
        ),
        'br_admin_company_user' => array(
            'add', 'delete', 'edit', 'manage', 'activate',
        ),
        'br_admin_company_logo' => array(
            'manage', 'add', 'delete',
        ),
        'br_admin_contract' => array(
            'manage', 'edit', 'view', 'history', 'sign', 'signedList', 'download', 'delete', 'csv', 'unfinishedList', 'archiveUnsigned'
        ),
        'br_admin_cv_entry' => array(
            'manage', 'delete', 'export', 'exportAcademics', 'exportCvCsv'
        ),
        'br_admin_event' => array(
            'manage', 'delete', 'add', 'edit', 'old', 'statistics','deleteAttendee', 'editAttendee'
        ),
        'br_admin_event_company' => array(
            'manage', 'edit', 'delete', 'csv',
        ),
        'br_admin_event_subscription' => array(
            'overview', 'add','edit', 'delete', 'mail' ,'search', 'csv',
        ),
        'br_admin_event_location' => array(
            'draw', 'add', 'edit', 'delete',
        ),
        'br_admin_invoice' => array(
            'history', 'view', 'edit', 'manage', 'download', 'payed', 'pay', 'manualAdd', 'csv', 'payedList', 'downloadAll'
        ),
        'br_admin_order' => array(
            'product', 'edit', 'delete', 'deleteProduct', 'editProduct', 'view', 'add', 'old', 'manage', 'signed', 'generate',
        ),
        'br_admin_overview' => array(
            'person', 'company', 'view', 'personView', 'companyView', 'pdf', 'csv',
        ),
        'br_admin_product' => array(
            'add', 'delete', 'manage', 'edit', 'old', 'companiesCsv', 'companies',
        ),
        'br_admin_request' => array(
            'reject', 'approve', 'manage', 'view',
        ),
        'br_admin_communication' => array(
            'add', 'manage', 'delete', 'view',
        ),
        'br_career_index' => array(
            'index',
        ),
        'br_career_company' => array(
            'view', 'overview', 'file', 'search',
        ),
        'br_career_event' => array(
             'overview','view','subscribe','map','qr','scanQr','overviewMatches','removeMatch'
        ),
        'br_career_vacancy' => array(
            'view', 'overview',
        ),
        'br_career_internship' => array(
            'view', 'overview',
        ),
        'br_career_student_job' => array(
            'view', 'overview',
        ),
        'br_career_internshipfair' => array(
            'view', 'overview', 'file', 'search',
        ),
        'br_cv_index' => array(
            'cv', 'edit', 'complete', 'delete', 'download', 'uploadProfileImage',
        ),
        'br_corporate_auth' => array(
            'login', 'logout',
        ),
        'br_corporate_index' => array(
            'index', 'events', 'login'
        ),
        'br_corporate_jobfair' => array(
            'overview',
        ),
        'br_corporate_cv' => array(
            'downloadArchive', 'grouped', 'list', 'search',
        ),
        'br_corporate_internship' => array(
            'overview', 'add', 'delete', 'edit',
        ),
        'br_corporate_vacancy' => array(
            'overview', 'add', 'delete', 'edit',
        ),
        'br_corporate_student_job' => array(
            'overview', 'add', 'delete', 'edit',
        ),
        'br_corporate_company' => array(
            'edit',
        )
    ),
);
