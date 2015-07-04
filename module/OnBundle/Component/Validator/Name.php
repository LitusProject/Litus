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

namespace OnBundle\Component\Validator;

/**
 * Checks whether a slug name already exists.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Name extends \CommonBundle\Component\Validator\AbstractValidator
{
    const TITLE_EXISTS = 'nameExists';

    protected $options = array(
        'slug' => null,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::TITLE_EXISTS => 'There already is a slug with this title',
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
            $options['slug'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if no publication with this title exists.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $slug = $this->getDocumentManager()
            ->getRepository('OnBundle\Document\Slug')
            ->findOneByName($value);

        if (null === $slug || ($this->options['slug'] && $slug == $this->options['slug'])) {
            return true;
        }

        $this->error(self::TITLE_EXISTS);

        return false;
    }
}
