<?php

namespace MailBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use MailBundle\Entity\Section;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use MailBundle\Entity\Section\Group;
use Psr\Http\Message\StreamInterface;

class SectionController extends \MailBundle\Component\Controller\AdminController
{
    public function manageAction()
    {
        $sections = $this->paginator()->createFromEntity(
            'MailBundle\Entity\Section',
            $this->getParam('page'),
            array(),
            array(
                'name' => 'ASC',
            )
        );

        return new ViewModel(
            array(
                'sections'         => $sections,
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
        $form = $this->getForm('mail_section_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);
            if ($form->isValid()) {
                $section = $form->hydrateObject();

                // sendinblue attribute character checking
                if(!preg_match('/^[A-Za-z0-9_]+$/',$section->getAttribute())) {
                    $this->flashMessenger()->error(
                        'Error',
                        'The SIB Attribute can only contain alphanumeric characters and underscore(_).'
                    );
                }
                else {
                    $this->getEntityManager()->persist($section);
                    $this->getEntityManager()->flush();

                    // config value that indicates if the api should be used or skipped (for local development)
                    $enableSibApi = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('mail.enable_sib_api');
                    if ($enableSibApi == "1") {
                        $this->sibAddAttribute($section->getAttribute());
                        $this->sibUpdateAllUsers($section->getAttribute(), $section->getDefaultValue());
                    }

                    $this->flashMessenger()->success(
                        'Success',
                        'The section was succesfully added!'
                    );
                }

                $this->redirect()->toRoute(
                    'mail_admin_section',
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

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws GuzzleException
     * @throws \Doctrine\ORM\ORMException
     */
    public function deleteAction()
    {
        $this->initAjax();

        $section = $this->getSectionEntity();
        if ($section === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($section);
        $this->getEntityManager()->flush();

        $enableSibApi = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('mail.enable_sib_api');
        if ($enableSibApi == "1") {
            $this->sibRemoveAttribute($section->getName());
        }

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Section|null
     */
    private function getSectionEntity()
    {
        $section = $this->getEntityById('MailBundle\Entity\Section');

        if (!($section instanceof Section)) {
            $this->flashMessenger()->error(
                'Error',
                'No section was found!'
            );

            $this->redirect()->toRoute(
                'mail_admin_section',
                array(
                    'action' => 'manage',
                )
            );
            return;
        }
        return $section;
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

        error_log(json_encode($response->getBody()));
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