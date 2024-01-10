<?php

namespace MailBundle\Controller\Admin;

use GuzzleHttp\Client;
use Laminas\View\Model\ViewModel;
use MailBundle\Component\Api\SibApi\SibApiHelper;
use MailBundle\Entity\Preference;
use GuzzleHttp\Exception\GuzzleException;

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
                'paginationControl'   => $this->paginator()->createControl(true),
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
                if (!preg_match('/^[A-Za-z0-9_]+$/', $preference->getAttribute())) {
                    $this->flashMessenger()->error(
                        'Error',
                        'The SIB Attribute can only contain alphanumeric characters and underscore(_).'
                    );
                } else {
                    $this->getEntityManager()->persist($preference);
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->success(
                        'Success',
                        'The preference was succesfully added!'
                    );
                }
            }

            $this->redirect()->toRoute(
                'mail_admin_preference',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();

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

        $enableSibApi = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('mail.enable_sib_api');
        if ($enableSibApi == "1") {
            $sibApiHelper = new SibApiHelper($this->getEntityManager());
            $sibApiHelperResponse = $sibApiHelper->deleteAttribute($preference->getAttribute());
            if (!$sibApiHelperResponse->success) {
                $this->flashMessenger()->error(
                    'Error',
                    'Exception when calling Sendinblue AttributesApi->deleteAttribute: ' . $sibApiHelperResponse->exception->getMessage()
                );
            }
            else {
                $this->getEntityManager()->remove($preference);
                $this->getEntityManager()->flush();
                $this->flashMessenger()->error(
                    'Success',
                    'The preference was successfully deleted in sendinblue!'
                );
            }
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
     * Remove attribute in SendInBlue with name $name.
     *
     * @param string $name
     * @return void
     * @throws GuzzleException
     */
    public function sibRemoveAttribute(string $name) {
        $api = $this->sibGetAPI();
        $client = new Client();

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