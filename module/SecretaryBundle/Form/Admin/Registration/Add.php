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

namespace SecretaryBundle\Form\Admin\Registration;

use SecretaryBundle\Entity\Organization\MetaData;

/**
 * Add Registration
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'typeahead',
            'name'       => 'person',
            'label'      => 'Person',
            'required'   => true,
            'options'    => array(
                'input' => array(
                    'validators' => array(
                        array('name' => 'typeahead_person'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'payed',
            'label'      => 'Has Payed',
            'required'   => true,
        ));

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'irreeel',
            'label'      => 'Ir.Reëel at CuDi',
            'required'   => true,
        ));

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'bakske',
            'label'      => 'Bakske by E-mail',
            'required'   => true,
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'tshirt_size',
            'label'      => 'T-shirt Size',
            'required'   => true,
            'attributes' => array(
                'options' => MetaData::$possibleSizes,
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'organization',
            'label'      => 'Organization',
            'required'   => true,
            'attributes' => array(
                'options' => $this->getOrganizations(),
            ),
        ));

        $this->addSubmit('Save', 'secretary_edit');
    }

    private function getOrganizations()
    {
        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $organizationOptions = array();
        foreach ($organizations as $organization) {
            $organizationOptions[$organization->getId()] = $organization->getName();
        }

        return $organizationOptions;
    }
}
