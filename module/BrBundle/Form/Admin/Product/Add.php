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

namespace BrBundle\Form\Admin\Product;

use BrBundle\Entity\Product;

/**
 * Add a product.
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Product';

    /**
     * @var Product
     */
    protected $product;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'name',
                'label'    => 'Name',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'ProductName',
                                'options' => array(
                                    'product' => $this->product,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'textarea',
                'name'     => 'description',
                'label'    => 'Description',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'price',
                'label'    => 'Price (in cents)',
                'required' => true,
                'value'    => 0,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Price'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'refund',
                'label' => 'Refund',
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'vat_type',
                'label'      => 'VAT Type',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->getVatTypes(),
                ),
            )
        );

        $this->add(
            array(
                'type'    => 'text',
                'name'    => 'invoice_description',
                'label'   => 'Invoice Text',
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'    => 'textarea',
                'name'    => 'contract_text',
                'label'   => 'Contract Text',
                'value'   => $this->getContractText(),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'ContractBullet'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'event',
                'label'      => 'Event',
                'attributes' => array(
                    'id'      => 'event',
                    'options' => $this->createEventsArray(),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'date',
                'name'       => 'delivery_date',
                'label'      => 'Delivery Date',
                'required'   => false,
                'attributes' => array(
                    'id' => 'delivery_date',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'product_add');

        if ($this->product !== null) {
            $this->bind($this->product);
        }
    }

    /**
     * @param  Product $product
     * @return self
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return array
     */
    private function getVatTypes()
    {
        $types = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.vat_types')
        );

        $typesArray = array();
        foreach ($types as $type => $value) {
            $typesArray[$type] = $value . '%';
        }

        return $typesArray;
    }

    /**
     * @return array
     */
    private function createEventsArray()
    {
        $events = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event')
            ->findAllActive();

        $eventsArray = array(
            array(
                'label' => '',
                'value' => '',
            ),
        );
        foreach ($events as $event) {
            $eventsArray[] = array(
                'label'      => $event->getTitle(),
                'value'      => $event->getId(),
                'attributes' => array(
                    'data-date' => $event->getStartDate()->format('d/m/Y'),
                ),
            );
        }

        return $eventsArray;
    }

    /**
     * @return string
     */
    private function getContractText()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.product_contract_text');
    }
}
