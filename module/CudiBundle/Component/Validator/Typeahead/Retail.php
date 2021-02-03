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

namespace CudiBundle\Component\Validator\Typeahead;

class Retail extends \CommonBundle\Component\Validator\Typeahead
{
    const NOT_ALLOWED = 'notAllowed';
    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This article does not exits',
        self::NOT_ALLOWED => 'This article is not allowed for retail',
    );

    /**
     * Create a new typeahead validator
     */
    public function __construct()
    {
        parent::__construct('CudiBundle\Entity\Article');
    }

    public function isValid($value, $context = null)
    {
        $allowedRetailTypes = unserialize($this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.retail_allowed_types'));

        if (!parent::isValid($value, $context))
            return false;

        $retailValid = in_array($this->entityObject->getType(), $allowedRetailTypes);

        if (!$retailValid) {
            $this->error(self::NOT_ALLOWED);
            return false;
        }

        return true;

    }
}
