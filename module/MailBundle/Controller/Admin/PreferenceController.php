<?php

namespace MailBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use MailBundle\Entity\Preference;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\StreamInterface;

class PreferenceController extends \MailBundle\Component\Controller\AdminController
{
    public function manageAction()
    {
        $preferences = $this->paginator()->createFromEntity(
            'MailBundle\Entity\Preference',
            $this->getParam('page'),
            array(),
            array(
                'name' => 'ASC',
            )
        );

        return new ViewModel(
            array(
                'preferences'         => $preferences,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     * @throws GuzzleException
     */
    public function addAction()
    {
        $form = $this->getForm('mail_preference_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);
            if ($form->isValid()) {
                $preference = $form->hydrateObject();

                // sendinblue attribute character checking
                if(!preg_match('/^[A-Za-z0-9_]+$/',$preference->getAttribute())) {
                    $this->flashMessenger()->error(
                        'Error',
                        'The SIB Attribute can only contain alphanumeric characters and underscore(_).'
                    );
                }
                else {
                    $this->getEntityManager()->persist($preference);
                    $this->getEntityManager()->flush();

                    // config value that indicates if the api should be used or skipped (for local development)
                    $enableSibApi = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('mail.enable_sib_api');
                    if ($enableSibApi == "1") {
                        $this->sibAddAttribute($preference->getAttribute());
                        $this->sibUpdateAllUsers($preference->getAttribute(), $preference->getDefaultValue());
                    }

                    $this->flashMessenger()->success(
                        'Success',
                        'The preference was succesfully added!'
                    );
                }

                $this->redirect()->toRoute(
                    'mail_admin_preference',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        $preference = $this->getPreferenceEntity();
        if ($preference === null) {
            return new ViewModel();
        }

        $form = $this->getForm('mail_preference_edit', $preference);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The preference was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'mail_admin_preference',
                    array(
                        'action' => 'edit',
                        'id'     => $preference->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'  => $form,
                'preference' => $preference,
            )
        );
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws GuzzleException
     * @throws \Doctrine\ORM\ORMException
     */
    public function deleteAction()
    {
        $this->initAjax();

        $preference = $this->getPreferenceEntity();
        if ($preference === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($preference);
        $this->getEntityManager()->flush();

        $enableSibApi = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('mail.enable_sib_api');
        if ($enableSibApi == "1") {
            $this->sibRemoveAttribute($preference->getName());
        }

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Preference|null
     */
    private function getPreferenceEntity()
    {
        $preference = $this->getEntityById('MailBundle\Entity\Preference');

        if (!($preference instanceof Preference)) {
            $this->flashMessenger()->error(
                'Error',
                'No preference was found!'
            );

            $this->redirect()->toRoute(
                'mail_admin_preference',
                array(
                    'action' => 'manage',
                )
            );
            return;
        }
        return $preference;
    }

    /**
     * Add attribute of type boolean in SendInBlue with name $name.
     *
     * @param string $name
     * @return void
     * @throws GuzzleException
     */
    public function sibAddAttribute(string $name) {
        $api = $this->sibGetAPI();
        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', 'https://api.sendinblue.com/v3/contacts/attributes/normal/'.$name, [
            'body' => '{"type":"boolean"}',
            'headers' => [
                'accept' => 'application/json',
                'api-key' => $api,
                'content-type' => 'application/json',
            ],
        ]);
    }

    /**
     * Remove attribute in SendInBlue with name $name.
     *
     * @param string $name
     * @return void
     * @throws GuzzleException
     */
    public function sibRemoveAttribute(string $name) {
        $api = $this->sibGetAPI();
        $client = new \GuzzleHttp\Client();

        $response = $client->request('DELETE', 'https://api.sendinblue.com/v3/contacts/attributes/normal/'.$name, [
            'body' => '{"type":"boolean"}',
            'headers' => [
                'accept' => 'application/json',
                'api-key' => $api,
                'content-type' => 'application/json',
            ],
        ]);
    }

    /**
     * Updates an attribute of a SendInBlue contact to a value, or leaves it unchanged if
     * the new value is the same as the old value.
     *
     * @param int $id
     * @param string $attributeName
     * @param bool $value
     * @return void
     * @throws GuzzleException
     */
    public function updateContact(int $id, string $attributeName, bool $value) {
        $api = $this->sibGetAPI();
        $client = new \GuzzleHttp\Client();
        $value = $value ? "true" : "false";
        $client->request('POST', 'https://api.sendinblue.com/v3/contacts/batch', [
            'body' => '{"contacts":[{"attributes":{"'.$attributeName.'":'.$value.'},"id":'.$id.'}]}',
            'headers' => [
                'accept' => 'application/json',
                'api-key' => $api,
                'content-type' => 'application/json',
            ],
        ]);
    }

    /**
     * Returns an array containing all the positions of $needle in $haystack.
     *
     * @param string $haystack
     * @param string $needle
     * @return array
     */
    public function strPosAll($haystack, $needle) {
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

    /**
     * Returns an array containing al the ids of SendInBlue contacts.
     *
     * @return array
     * @throws GuzzleException
     */
    public function getAllUserIds() {
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
     * Returns a StreamInterface object which contains information of all SendInBlue contacts that are
     * between $offset and $limit in the SendInBlue contact list.
     *
     * @param int $offset
     * @param int $limit
     * @return StreamInterface
     * @throws GuzzleException
     */
    public function getUserBatch(int $offset, int $limit) {
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
     * Returns an array containing all ids of SendInBlue contacts that are between $offset and $limit in the SendInBlue contact list.
     *
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws GuzzleException
     */
    public function getUserIds(int $offset, int $limit)
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
     * Updates all SendInBlue contacts' attribute with name $attributeName to a value, or leaves it unchanged if
     * the new value is the same as the old value.
     *
     * @param string $attributeName
     * @param bool $value
     * @return void
     * @throws GuzzleException
     */
    public function sibUpdateAllUsers(string $attributeName, bool $value) {
        set_time_limit(900); // increase php timeout limit
        $ids = $this->getAllUserIds();
        foreach($ids as $id) {
            $this->updateContact($id, $attributeName, $value);
        }
        set_time_limit(90); // set back to default php timeout limit
    }

    public function sibGetAPI() {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('mail.sib_api');
    }

}