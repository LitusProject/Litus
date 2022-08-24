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

namespace BrBundle\Component\Validator;

/**
 * Checks if the form does not have more than the maximum allowed importance points allocated for the sectors
 *
 * @author Robin Wroblowski
 */
class SectorImportanceConstraint extends \CommonBundle\Component\Validator\AbstractValidator
{
    const TOO_MANY = 'notValid';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::TOO_MANY => 'You have allocated too many points to the sector features',
    );

    /**
     * @var array The message variables
     */
    protected $messageVariables = array(
        'level' => array('options' => 'level'),
    );

    protected $options = array(
        'level' => null,
    );

    /**
     * The feature's level
     * @var string
     */
    protected $level;

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
            $options['level'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if
     *          the config constraints for maximum number of features with certain importance level are not violated
     *
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $points = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.match_sector_feature_max_points');

        $counts = 0;

        foreach ($context as $key => $val) {
            if ($val != 0 && str_contains($key, 'sector_feature_')) {
                $id = substr($key, strlen('sector_feature_'));
                $feature = $this->getEntityManager()->getRepository('BrBundle\Entity\Match\Feature')
                    ->findOneById($id);
                if ($feature->isSector()) {
                    $counts += $val;
                }
            }
        }

        if ($counts > $points) {
            $this->error(self::TOO_MANY);
            return false;
        }

        return true;
    }
}
