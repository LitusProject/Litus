<?php

namespace FormBundle\Form\Admin\Field\Field;

use CommonBundle\Component\Form\FieldsetInterface;
use CommonBundle\Entity\General\Language;

/**
 * Add Dropdown Field
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Dropdown extends \CommonBundle\Component\Form\Admin\Fieldset\Tabbable
{
    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(
            array(
                'type'       => 'text',
                'name'       => 'options',
                'label'      => 'Options',
                'required'   => $isDefault,
                'attributes' => array(
                    'data-help' => 'The options comma separated.',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        foreach ($this->getLanguages() as $language) {
            $specs['tab_content']['tab_' . $language->getAbbrev()]['options']['required'] = $specs['tab_content']['tab_' . $language->getAbbrev()]['options']['required'] && $this->isRequired();
        }

        return $specs;
    }

    public function setRequired($required = true)
    {
        return $this->setElementRequired($required);
    }
}
