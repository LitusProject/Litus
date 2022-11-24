<?php

namespace MailBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use MailBundle\Command\SIB;
use MailBundle\Entity\Section;

class SectionController extends \MailBundle\Component\Controller\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'MailBundle\Entity\Section',
            $this->getParam('page'),
            array(),
            array(
                'name' => 'ASC',
            )
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('mail_section_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $section = $form->hydrateObject();

                $dup = $this->getEntityManager()
                    ->getRepository('MailBundle\Entity\Section')
                    ->findAllByNameQuery($section->getName());
                error_log(json_encode("DUP:"));
                error_log(json_encode($dup));

                if(!preg_match('/^[A-Za-z0-9_]+$/',$section->getAttribute())) {
                    $this->flashMessenger()->error(
                        'Error',
                        'The SIB Attribute can only contain alphanumeric characters and underscore(_).'
                    );
                }
                else {
                    $this->getEntityManager()->persist($section);
                    $this->getEntityManager()->flush();

                    $this->sibAddAttribute($section->getAttribute());
                    $this->sibUpdateAllUsers($section->getAttribute(), $section->getDefaultValue());

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

    public function deleteAction()
    {
        $this->initAjax();

        $section = $this->getSectionEntity();
        if ($section === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($section);
        $this->getEntityManager()->flush();
        $this->sibRemoveAttribute($section->getName());
        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $sections = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($sections as $section) {
            $item = (object) array();
            $item->id = $section->getId();
            $item->section = $section->getName();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return \Doctrine\ORM\Query|null
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'section':
                return $this->getEntityManager()
                    ->getRepository('MailBundle\Entity\Section')
                    ->findAllByNameQuery($this->getParam('string'));
        }
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
     * Add attribute in SendInBlue with name $name.
     *
     * @param string $name
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sibAddAttribute(string $name) {
        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', 'https://api.sendinblue.com/v3/contacts/attributes/normal/'.$name, [
            'body' => '{"type":"boolean"}',
            'headers' => [
                'accept' => 'application/json',
                'api-key' => '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',
                'content-type' => 'application/json',
            ],
        ]);
    }

    /**
     * Remove attribute in SendInBlue with name $name.
     *
     * @param string $name
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sibRemoveAttribute(string $name) {
        $client = new \GuzzleHttp\Client();

        $response = $client->request('DELETE', 'https://api.sendinblue.com/v3/contacts/attributes/normal/'.$name, [
            'body' => '{"type":"boolean"}',
            'headers' => [
                'accept' => 'application/json',
                'api-key' => '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',
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
     * @param string $value
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateContact(int $id, string $attributeName, string $value) {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'https://api.sendinblue.com/v3/contacts/batch', [
            'body' => '{"contacts":[{"attributes":{"'.$attributeName.'":'.$value.'},"id":'.$id.'}]}',
            'headers' => [
                'accept' => 'application/json',
                'api-key' => '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',
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
     */
    public function getAllUserIds() {
        $offset = 0;
        $limit = 1000;
        $ids = $this->getUserIds($offset, $limit); // add ids from first 1000 contacts
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
     * @return \Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserBatch(int $offset, int $limit) {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://api.sendinblue.com/v3/contacts?limit='.$limit.'&offset='.$offset.'&sort=desc', [
            'headers' => [
                'accept' => 'application/json',
                'api-key' => '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',
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserIds(int $offset, int $limit)
    {
        $batch = $this->getUserBatch($offset, $limit);
        $idsPos = $this->strPosAll($batch, "\"id");
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
     * @param string $value
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sibUpdateAllUsers(string $attributeName, bool $value) {
        $client = new \GuzzleHttp\Client();
        $value = $value ? "true" : "false";
        $response = $client->request('PUT', 'https://api.sendinblue.com/v3/contacts/dries.vanspauwen@vtk.be', [
            'body' => '{"attributes":{"'.$attributeName.'":'.$value.'}}',
            'headers' => [
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'api-key' => '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',
            ],
        ]);
//        $ids = $this->getAllUserIds();
//        foreach($ids as $id) {
////        echo $id;
////        echo "\r\n";
//            $this->updateContact($id, "CANTUS", "true");
//        }
    }

}