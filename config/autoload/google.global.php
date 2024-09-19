<?php

use Google\Service\Directory;
use Google\Service\Groupssettings;

if (file_exists(__DIR__ . '/../google.config.php')) {
    // TODO: Remove this branch once all deployments have been containerized
    $googleConfig = include __DIR__ . '/../google.config.php';
} else {
    $privateKey = $_ENV['LITUS_GOOGLE_PRIVATE_KEY'] ?? '';
    if (isset($_ENV['LITUS_GOOGLE_PRIVATE_KEY_FILE'])) {
        $privateKey = file_get_contents($_ENV['LITUS_GOOGLE_PRIVATE_KEY_FILE']);
    }

    $googleConfig = array(
        'auth' => array(
            'project_id'           => $_ENV['LITUS_GOOGLE_PROJECT_ID'] ?? '',
            'private_key_id'       => $_ENV['LITUS_GOOGLE_PRIVATE_KEY_ID'] ?? '',
            'private_key'          => $privateKey,
            'client_email'         => $_ENV['LITUS_GOOGLE_CLIENT_EMAIL'],
            'client_id'            => $_ENV['LITUS_GOOGLE_CLIENT_ID'],
            'client_x509_cert_url' => $_ENV['LITUS_GOOGLE_CLIENT_X509_CERT_URL'],
        ),
    );
}

return array(
    'google' => array(
        'auth' => array_merge(
            array(
                'type'                        => 'service_account',
                'auth_uri'                    => 'https://accounts.google.com/o/oauth2/auth',
                'token_uri'                   => 'https://oauth2.googleapis.com/token',
                'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
            ),
            $googleConfig['auth'],
        ),
        'services' => array(
            'directory' => array(
                'scopes' => array(
                    Directory::ADMIN_DIRECTORY_GROUP,
                    Directory::ADMIN_DIRECTORY_GROUP_READONLY,
                    Directory::ADMIN_DIRECTORY_GROUP_MEMBER,
                    Directory::ADMIN_DIRECTORY_GROUP_MEMBER_READONLY,
                ),
            ),
            'groupssettings' => array(
                'scopes' => array(
                    Groupssettings::APPS_GROUPS_SETTINGS,
                ),
            ),
        ),
    ),
);
