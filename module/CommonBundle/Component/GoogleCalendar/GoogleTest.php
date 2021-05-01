<?php
namespace CommonBundle\Component\GoogleCalendar;
require '../../../../vendor/autoload.php';
use DateTime;
use Google\Exception;
use Google_Client;
use Google_Service_Gmail;

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient()
{
    $client = new Google_Client();
    $credentials = './serviceAcc.json';
    $client->setAuthConfig($credentials);
    $client->setApplicationName("Shifts");
    $client->setScopes(array(\Google_Service_Gmail::GMAIL_READONLY, Google_Service_Gmail::GMAIL_SETTINGS_SHARING));
    $client->setSubject('robin.wroblowski@vtk.be');

    return $client;
}


// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Gmail($client);

$forward = new \Google_Service_Gmail_ForwardingAddress(true,'robin.wroblowski@gmail.com', 'LEAVE_IN_INBOX');

$service->users_settings_forwardingAddresses->create('robin.wroblowski@vtk.be', $forward);
print_r($service->users_settings_forwardingAddresses->get('robin.wroblowski@vtk.be', 'robin.wroblowski@gmail.com'));

