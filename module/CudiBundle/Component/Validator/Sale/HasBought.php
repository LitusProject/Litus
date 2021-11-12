<?php

namespace CudiBundle\Component\Validator\Sale;

use CommonBundle\Component\Form\Form;
use CommonBundle\Component\Validator\FormAwareInterface;

/**
 * Check if user has bought an aritcle
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class HasBought extends \CommonBundle\Component\Validator\AbstractValidator implements FormAwareInterface
{
    const NOT_VALID = 'notValid';

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
        self::NOT_VALID => 'The article was never bought by this user',
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
        $this->setValue($value);

        $personField = $this->form->get('person');
        if ($personField->get('id')->getValue() == '' || $context['id'] == null) {
            $this->error(self::NOT_VALID);

            return false;
        }

        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findOneById($personField->get('id')->getValue());

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findOneById($context['id']);

        $booking = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findOneSoldByArticleAndPerson($article, $person, false);

        if ($booking !== null) {
            return true;
        }

        $this->error(self::NOT_VALID);

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
