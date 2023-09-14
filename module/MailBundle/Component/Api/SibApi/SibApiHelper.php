<?php

namespace MailBundle\Component\Api\SibApi;

use Doctrine\ORM\EntityManager;
use GuzzleHttp\Exception\GuzzleException;
use Ko\Process;
use Ko\ProcessManager;
use Laminas\Mail\Message;
use MailBundle\Controller\Admin\PreferenceController;
use Psr\Http\Message\StreamInterface;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api;
use GuzzleHttp\Client as HttpClient;
use Exception;

class SibApiHelper extends PreferenceController
{
    private $config;

    public function __construct(EntityManager $entityManager) {
        $api_key = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('mail.sib_api');
        $this->config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $api_key);
    }

    public function createAttribute(string $name) {
        $attributesApiInstance = new Api\AttributesApi(
            new HttpClient(),
            $this->config
        );

        $attributeCategory = "normal";
        $attributeName = $name;
        $createAttribute = new \SendinBlue\Client\Model\CreateAttribute();

        try {
            $this->$attributesApiInstance->createAttribute($attributeCategory, $attributeName, $createAttribute);
            return SibApiHelperResponse::successful();
        } catch (Exception $e) {
            error_log('Exception when calling Sendinblue AttributesApi->createAttribute: ', $e->getMessage());
            return SibApiHelperResponse::unsuccessful($e);
        }
    }


    public function updateAttributeForAllContacts(string $attributeName, bool $value) {
        $manager = new ProcessManager();
        $manager->fork(function(Process $p) use($attributeName, $value) {
//            $ids = $this->getAllUserIds();
//            foreach($ids as $id) {
//                $this->updateContact($id, $attributeName, $value);
//            }
            sleep(10);
        })->onSuccess(function() use ($attributeName) {
//            $mail = new Message();
//            $mail->setBody('Your Sendinblue attribute with name ' . $attributeName . ' has been succesfully created!')
//                ->setFrom("it@vtk.be", "VTK IT")
//                ->addTo("it@vtk.be", "VTK IT")
//                ->setSubject('Sendinblue Attribute created successfully.');
//
//            if (getenv('APPLICATION_ENV') != 'development') {
//                $this->getMailTransport()->send($mail);
//            }
            error_log("attribute confirmation mail has been sent");
        });
    }

    public function updateAttributeForAllContactsImplementation(string $attributeName, bool $value) {
        $ids = $this->getAllUserIds();
        foreach($ids as $id) {
            $this->updateContact($id, $attributeName, $value);
        }
    }

    /**
     * Updates an attribute of a SendInBlue contact to a value, or leaves it unchanged if
     * the new value is the same as the old value.
     */
    public function updateAttributeForContact(int $id, string $attributeName, bool $value) {
        $apiInstance = new Api\ContactsApi(
            new HttpClient(),
            $this->config
        );

        $identifier = strval($id); // string | Email (urlencoded) OR ID of the contact
        $updateContact = new \SendinBlue\Client\Model\UpdateContact();
        $updateContact->setAttributes(`{ \".$attributeName.\":\".$value.\"}`);
        try {
            $apiInstance->updateContact($identifier, $updateContact);
            return SibApiHelperResponse::successful();
        } catch (Exception $e) {
            error_log('Exception when calling Sendinblue ContactsApi->updateContact: ', $e->getMessage());
            return SibApiHelperResponse::unsuccessful($e);
        }
    }

    /**
     * Returns an array containing al the ids of SendInBlue contacts.
     *
     * @return array
     * @throws GuzzleException
     */
    private function getAllUserIds() {
        $offset = 0;
        $limit = 1000;
        $ids = $this->getUserIds($offset, $limit); // add ids from first 1000 contacts (limit imposed by sendinblue)
        while (count($ids)%1000 == 0) { // take next thousand, as long as they exist
            $offset += 1000;
            $ids = array_merge($ids, $this->getUserIds($offset, $limit));
        }
        return $ids;
    }

    /**
     * Returns an array containing all ids of SendInBlue contacts that are between $offset and $limit in the SendInBlue contact list.
     *
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws GuzzleException
     */
    private function getUserIds(int $offset, int $limit)
    {
        $batch = $this->getUserBatch($offset, $limit);
        $idsPos = $this->strPosAll($batch, "\"id"); // code to subtract id's from all user information
        $ids = array();
        foreach ($idsPos as $pos) {
            $beginPos = $pos + 5;
            $endPos = strpos($batch, ",", $beginPos);
            $ids[] = intval(mb_substr($batch, $beginPos, $endPos - $beginPos));
        }
        return $ids;
    }

    /**
     * Returns a StreamInterface object which contains information of all SendInBlue contacts that are
     * between $offset and $limit in the SendInBlue contact list.
     *
     * @param int $offset
     * @param int $limit
     * @return StreamInterface
     * @throws GuzzleException
     */
    private function getUserBatch(int $offset, int $limit) {
        $api = $this->sibGetAPI();
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://api.sendinblue.com/v3/contacts?limit='.$limit.'&offset='.$offset.'&sort=desc', [
            'headers' => [
                'accept' => 'application/json',
                'api-key' => $api,
            ],
        ]);
        return $response->getBody();
    }

    /**
     * Returns an array containing all the positions of $needle in $haystack.
     *
     * @param string $haystack
     * @param string $needle
     * @return array
     */
    private function strPosAll($haystack, $needle) {
        $s = 0;
        $i = 0;
        while(is_integer($i)) {
            $i = mb_stripos($haystack, $needle, $s);
            if(is_integer($i)) {
                $aStrPos[] = $i;
                $s = $i + mb_strlen($needle);
            }
        }
        if(isset($aStrPos)) return $aStrPos;
        else return array();
    }

}