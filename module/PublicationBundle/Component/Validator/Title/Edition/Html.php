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

namespace PublicationBundle\Component\Validator\Title\Edition;

/**
 * Checks whether a publication title already exists.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Html extends \CommonBundle\Component\Validator\AbstractValidator
{
    const TITLE_EXISTS = 'titleExists';

    protected $options = array(
        'publication' => null,
        'academic_year' => null,
        'exclude' => null,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::TITLE_EXISTS => 'There already is a HTML edition with this title for this publication',
    );

    /**
     * Sets validator options
     *
     * @param int|array|\Traversable $options
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            $options = func_get_args();
            $temp['publication'] = array_shift($options);
            $temp['academic_year'] = array_shift($options);
            $temp['exclude'] = array_shift($options);
            $options = $temp;
        }

        parent::__construct($options);
    }

    /**
     * Returns true if no edition with this title exists.
     *
     * @param  string  $value   The value of the field that will be validated
     * @param  array   $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $edition = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Edition\Html')
            ->findOneByPublicationTitleAndAcademicYear($this->options['publication'], $value, $this->options['academic_year']);

        if (null !== $edition) {
            if (null === $this->options['exclude'] || $edition->getId() !== $this->options['exclude']) {
                $this->error(self::TITLE_EXISTS);

                return false;
            }
        }

        return true;
    }
}
