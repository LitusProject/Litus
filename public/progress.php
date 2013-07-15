<?php

if ('development' == getenv('APPLICATION_ENV')) {
    ini_set('display_errors', true);
    error_reporting(E_ALL);

    define('REQUEST_MICROTIME', microtime(true));
}

require '../vendor/zendframework/zendframework/library/Zend/ProgressBar/Upload/UploadHandlerInterface.php';
require '../vendor/zendframework/zendframework/library/Zend/ProgressBar/Upload/AbstractUploadHandler.php';
require '../vendor/zendframework/zendframework/library/Zend/ProgressBar/Upload/SessionProgress.php';

$progress = new \Zend\ProgressBar\Upload\SessionProgress();

echo json_encode(
    array(
        'result' => $progress->getProgress($_POST['upload_id']),
    )
);