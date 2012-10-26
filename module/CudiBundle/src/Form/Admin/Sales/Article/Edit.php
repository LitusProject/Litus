<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Form\Admin\Sales\Article;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Entity\General\AcademicYear as AcademicYear,
    CudiBundle\Component\Validator\Sales\Article\Barcodes\Unique as UniqueBarcodeValidator,
    CudiBundle\Entity\Sales\Article,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edit Sale Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CudiBundle\Form\Admin\Sales\Article\Add
{
    /**
     * @var \CudiBundle\Entity\Sales\Article
     */
    private $_article;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     * @param \CudiBundle\Entity\Sales\Article $article
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, AcademicYear $academicYear, Article $article, $name = null)
    {
        parent::__construct($entityManager, $academicYear, $name);

        $this->_article = $article;

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'article_edit');
        $this->add($field);

        $this->populateFromArticle($article);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->remove('barcode');
        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'barcode',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'barcode',
                            'options' => array(
                                'adapter'     => 'Ean12',
                                'useChecksum' => false,
                            ),
                        ),
                        new UniqueBarcodeValidator($this->_entityManager, $this->_academicYear, array($this->_article->getId())),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
