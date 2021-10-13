<?php

namespace OnBundle\Form\Admin\Slug;

use DateTime;
use OnBundle\Entity\Slug as SlugEntity;

/**
 * Add Slug
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'OnBundle\Hydrator\Slug';

    /**
     * @var SlugEntity|null
     */
    protected $slug;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'name',
                'label'    => 'Name',
                'required' => false,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'SlugName',
                                'options' => array(
                                    'slug' => $this->slug,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'url',
                'label'    => 'URL',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Uri'),
                        ),
                    ),
                ),
            )
        );

        $expirationInterval = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.slugExpirationInterval');

        $this->add(
            array(
                'type'     => 'date',
                'name'     => 'expiration_date',
                'label'    => 'Expiration Date',
                'value'    => date_add(new DateTime(), new \DateInterval($expirationInterval))->format('d/m/Y'),
                'required' => true,
            )
        );

        $this->add(
            array(
                'type'     => 'checkbox',
                'name'     => 'active',
                'label'    => 'Active',
                'value'    => true,
                'required' => true,
            )
        );

        $this->addSubmit('Add', 'slug_add');

        if ($this->slug !== null) {
            $this->bind($this->slug);
        }
    }

    /**
     * @param  SlugEntity $slug
     * @return self
     */
    public function setSlug(SlugEntity $slug)
    {
        $this->slug = $slug;

        return $this;
    }
}
