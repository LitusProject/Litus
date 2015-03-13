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
 *
 * @license http://litus.cc/LICENSE
 */

namespace FormBundle\Form\Admin\Field\Field;

use CommonBundle\Component\Form\FieldsetInterface,
    CommonBundle\Entity\General\Language;

/**
 * Add Dropdown Field
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Dropdown extends \CommonBundle\Component\Form\Admin\Fieldset\Tabbable
{
    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(array(
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
        ));
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
