<?php

namespace CommonBundle\Form\Admin\FAQ;

use CommonBundle\Component\Form\FieldsetInterface;
use CommonBundle\Entity\General\Language;
use CommonBundle\Entity\General\Node\FAQ\FAQ as FAQEntity;
use RuntimeException;

/**
 * Add FAQ
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    protected $hydrator = 'CommonBundle\Hydrator\General\Node\FAQ\FAQ';

    /**
     * @var FAQEntity
     */
    private $faq;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'name',
                'label'    => 'Name',
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
                'type'       => 'select',
                'name'       => 'edit_roles',
                'label'      => 'Edit Roles',
                'required'   => true,
                'attributes' => array(
                    'multiple' => true,
                ),
                'options' => array(
                    'options' => $this->createEditRolesArray(),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'forced_language',
                'label'      => 'Force Language',
                'required'   => true,
                'options' => array(
                    'options' => $this->createForcedLanguagesArray(),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'active',
                'label' => 'Active',
                'value' => true,
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'order_number',
                'label'    => 'Ordering Number',
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'faq_add');

        if ($this->getFAQ() !== null) {
            $this->bind($this->getFAQ());
        }
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(
            array(
                'type'       => 'text',
                'name'       => 'title',
                'label'      => 'Title',
                'required'   => $isDefault,
                'attributes' => array(
                    'width' => '400px',
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

        $container->add(
            array(
                'type'     => 'textarea',
                'name'     => 'content',
                'label'    => 'Content',
                'required' => $isDefault,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );
    }

    private function createEditRolesArray()
    {
        $roles = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findBy(array(), array('name' => 'ASC'));

        $rolesArray = array();
        foreach ($roles as $role) {
            if (!$role->getSystem()) {
                $rolesArray[$role->getName()] = $role->getName();
            }
        }

        if (count($rolesArray) == 0) {
            throw new RuntimeException('There needs to be at least one role before you can add a faq');
        }

        return $rolesArray;
    }

    private function createForcedLanguagesArray()
    {
        $languages = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();

        $langArray = array();
        $langArray['None'] = 'None';
        foreach ($languages as $language) {
            $langArray[$language->getAbbrev()] = $language->getName();
        }

        return $langArray;
    }

    /**
     * @param FAQEntity $faq
     * @return self
     */
    public function setFAQ(FAQEntity $faq)
    {
        $this->faq = $faq;

        return $this;
    }

    /**
     * @return FAQEntity
     */
    public function getFAQ()
    {
        return $this->faq;
    }
}
