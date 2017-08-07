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

namespace CudiBundle\Component\Validator\Sale\Article\Barcode;

use CudiBundle\Entity\Sale\Article;
/**
 * Matches the given article barcode against the database to check whether it is unique or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Unique extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'sale_article' => null,
    );

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The article barcode already exists',
    );

    /**
     * Sets validator options
     *
     * @param int|array|\Traversable $options
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            $args = func_get_args();
            $options = array();
            $options['sale_article'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if and only if a field name has been set, the field name is available in the
     * context, and the value of that field unique and valid.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if (! is_numeric($value)) {
            $this->error(self::NOT_VALID);

            return false;
        }

        $barcode = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article\Barcode')
            ->findOneByBarcode($value);

        if (null === $barcode || $barcode->getArticle() == $this->options['sale_article']) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}
