<?php

namespace GalleryBundle\Component\Validator;

use CommonBundle\Component\Form\Form;
use CommonBundle\Component\Util\Url;
use CommonBundle\Component\Validator\FormAwareInterface;
use DateTime;

/**
 * Matches the given page title against the database to check whether it is
 * unique or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AlbumName extends \CommonBundle\Component\Validator\AbstractValidator implements FormAwareInterface
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'album' => null,
    );

    /**
     * @var Form The form to validate
     */
    private $form;

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The album title already exists',
    );

    /**
     * @param Form $form
     * @return self
     */
    public function setForm(Form $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Returns true if no matching record is found in the database.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $date = DateTime::createFromFormat('d#m#Y', self::getFormValue($this->form, 'date'));

        if ($date) {
            $title = $date->format('Ymd') . '_' . Url::createSlug($value);

            $album = $this->getEntityManager()
                ->getRepository('GalleryBundle\Entity\Album')
                ->findOneByName($title);

            if ($album === null || ($this->options['album'] && $album == $this->options['album'])) {
                return true;
            }

            $this->error(self::NOT_VALID);
        }

        return false;
    }
}
