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

namespace PublicationBundle\Component\Validator\Title;

/**
 * Checks whether a publication title already exists.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Publication extends \CommonBundle\Component\Validator\AbstractValidator
{
    const TITLE_EXISTS = 'titleExists';

    protected $options = array(
        'exclude' => null,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::TITLE_EXISTS => 'There already is a publication with this title',
    );

    /**
     * Sets validator options
     *
     * @param integer|array|\Traversable $options
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            $args = func_get_args();
            $options = array();
            $options['exclude'] = array_shift($args);
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
        $publication = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Publication')
            ->findOneByTitle($value);

        if ($publication !== null) {
            if ($this->options['exclude'] === null || $publication->getId() !== $this->options['exclude']) {
                $this->error(self::TITLE_EXISTS);

                return false;
            }
        }

        return true;
    }
}
