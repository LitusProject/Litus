<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

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
        $type = pathinfo($file['name'], PATHINFO_EXTENSION);
        $data = file_get_contents($file['tmp_name']);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        return base64_encode($data);
    }

    protected function doHydrate(array $data, $object = null)
    {
        $result = array();

        $result['ISICCardNumber'] = '';
        $result['Firstname'] = $data['personal_info']['first_name'];
        $result['Lastname'] = $data['personal_info']['last_name'];
        $result['BirthDate'] = $data['personal_info']['birthday'];
        $result['BirthPlace'] = $data['personal_info']['birthplace'];
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
        if (isset($data['optins']['newsletter'])) {
            $result['Optin'] = $data['optins']['newsletter'] == true ? '1' : '0';
        }
        $result['postOptOut'] = $data['optins']['post'] == true ? '0' : '1';
        $result['postOptOutThird'] = $data['optins']['post_third'] == true ? '0' : '1';

        $result['Photo'] = $this->convertBase64($data['photo_group']['photo']);
        $result['ImageExtension'] = pathinfo($data['photo_group']['photo']['name'], PATHINFO_EXTENSION);

        return $result;
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = array();

        $data['personal_info'] = $this->stdExtract($object, array(self::$stdKeysPersonal));
        $data['contact_details'] = $this->stdExtract($object, array(self::$stdKeysContact));

        $data['personal_info']['birthday'] = $object->getBirthday() !== null
            ? $object->getBirthday()->format('d/m/Y')
            : '';

        $hydratorAddress = $this->getHydrator('CommonBundle\Hydrator\General\Address');
        $data['address'] = $hydratorAddress->extract($object->getSecondaryAddress());

        return $data;
    }
}
