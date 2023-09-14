<?php

namespace MailBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use MailBundle\Component\Api\SibApi\SibApiHelper;
use MailBundle\Entity\Preference;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\StreamInterface;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api\AccountApi;

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

    public function addAction()
    {
        $form = $this->getForm('mail_preference_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);
            if ($form->isValid()) {
                $preference = $form->hydrateObject();

                // sendinblue attribute character checking
                // TODO: put in form validator
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
                        $sibApiHelper = new SibApiHelper($this->getEntityManager());

                        // create attribute
//                        $sibApiHelperResponse = $sibApiHelper->createAttribute($preference->getAttribute());
//                        if (!$sibApiHelperResponse->success) {
//                            $this->flashMessenger()->error(
//                                'Error',
//                                'Exception when calling Sendinblue AttributesApi->createAttribute: ' . $e->getMessage()
//                            );
//                        }

                        // assign default value of preference to sib attribute for all sib contacts
                        $sibApiHelper->updateAttributeForAllContacts($preference->getAttribute(), $preference->getDefaultValue());
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
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $api);

        $apiInstance = new AccountApi(
            new HttpClient(),
            $config
        );

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

}