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

namespace CudiBundle\Component\Validator;

use CommonBundle\Component\Form\Form;
use CommonBundle\Component\Validator\FormAwareInterface;

/**
 * Check if user has bought an aritcle
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class RetailPrice extends \CommonBundle\Component\Validator\AbstractValidator implements FormAwareInterface
{
    const NOT_VALID = 'notValid';
    const TOO_HIGH = 'tooHigh';
    const NO_SALE = 'noSale';

    /**
     * @var Form
     */
    private $form;

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The price is not valid',
        self::NO_SALE => 'There is no sale article for this article',
        self::TOO_HIGH => 'The price is too high',
    );

    /**
     * Returns true if and only if a field name has been set, the field name is available in the
     * context, and the value of that field is valid.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {

        $articleField = $this->form->get('article');
        if ($articleField->get('id')->getValue() == '' ) {
            $this->error(self::NOT_VALID);

            return false;
        }
        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findOneById($articleField->get('id')->getValue());

        $maxRelPrice = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.retail_maximal_relative_price');

        $article->setEntityManager($this->getEntityManager());
        if (!$value > 0){
            $this->error(self::NOT_VALID);
            return false;
        }
        if ($article->getSaleArticle() === null){
            $this->error(self::NO_SALE);
            return false;
        }
        elseif ($value <= $article->getSaleArticle()->getSellPrice()*$maxRelPrice/100) {
            return true;
        }

        $this->error(self::TOO_HIGH);

        return false;
    }

    /**
     * @param  Form $form
     * @return self
     */
    public function setForm(Form $form)
    {
        $this->form = $form;

        return $this;
    }
}
