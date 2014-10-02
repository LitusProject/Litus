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

namespace CommonBundle\Component\InputFilter;

use Zend\InputFilter\Factory as ZendFactory,
    Zend\InputFilter\InputFilter;

class Factory extends ZendFactory
{
    public function createInput($inputSpecification)
    {
        if (is_array($inputSpecification)
                && isset($inputSpecification['type'])
                && is_array($inputSpecification['type'])) {
            return $this->createInputFilter($inputSpecification);
        }

        return parent::createInput($inputSpecification);
    }

    public function createInputFilter($inputFilterSpecification)
    {
        if (!is_array($inputFilterSpecification)
                || !isset($inputFilterSpecification['type'])
                || !is_array($inputFilterSpecification['type'])) {
            return parent::createInputFilter($inputFilterSpecification);
        }

        $typeInput = $inputFilterSpecification['type'];
        $inputFilterSpecification['type'] = 'inputfilter';

        $inputFilter = parent::createInputFilter($inputFilterSpecification);

        $inputFilter->add(
            $this->createInput($typeInput),
            'type'
        );

        return $inputFilter;
    }
}
