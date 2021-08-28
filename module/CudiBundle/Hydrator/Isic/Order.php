<?php

namespace CudiBundle\Hydrator\Isic;

class Order extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected $entity = 'CommonBundle\Entity\User\Person\Academic';

    protected static $stdKeysPersonal = array(
        'first_name',
        'last_name',
        'sex',
    );

    protected static $stdKeysContact = array(
        'email',
        'phone_number',
    );

    private function convertBase64($file)
    {
        return base64_encode(file_get_contents($file['tmp_name']));
    }

    protected function doHydrate(array $data, $object = null)
    {
        $result = array();

        $result['ISICCardNumber'] = '';
        $result['Firstname'] = $data['personal_info']['first_name'];
        $result['Lastname'] = $data['personal_info']['last_name'];
        $result['BirthDate'] = $data['personal_info']['birthday'];
        $result['Gender'] = $data['personal_info']['sex'];
        $result['language'] = $data['personal_info']['language'];
        $result['Street'] = $data['address']['street'] . ' ' . $data['address']['number'];
        $result['PostalCode'] = $data['address']['postal'];
        $result['City'] = $data['address']['city'];
        $result['email'] = $data['contact_details']['email'];
        $result['PhoneNumber'] = $data['contact_details']['phone_number'];
        $result['Course'] = $data['studies']['course'];
        $result['School'] = $data['studies']['school'];
        $result['StudentCity'] = $data['studies']['student_city'];
        $result['Year'] = $data['studies']['year'];

        $result['Optin'] = '0';
        if (isset($data['optins']['newsletter'])) {
            $result['Optin'] = $data['optins']['newsletter'];
        }

        $result['partnerOptin'] = '0';
        if (isset($data['optins']['partners'])) {
            $result['partnerOptin'] = $data['optins']['partners'];
        }

        $result['Photo'] = $this->convertBase64($data['photo_group']['photo']);
        $result['ImageExtension'] = pathinfo($data['photo_group']['photo']['name'], PATHINFO_EXTENSION);

        $result['postOptOutThird'] = '1';

        return $result;
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = array();

        $data['personal_info'] = $this->stdExtract($object, array(self::$stdKeysPersonal));
        $data['contact_details'] = $this->stdExtract($object, array(self::$stdKeysContact));

        $data['personal_info']['birthday'] = $object->getBirthday() !== null ? $object->getBirthday()->format('d/m/Y') : '';

        $hydratorAddress = $this->getHydrator('CommonBundle\Hydrator\General\Address');
        $data['address'] = $hydratorAddress->extract($object->getSecondaryAddress());

        return $data;
    }
}
