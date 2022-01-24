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
 * Checks if the form does not have more than the maximum allowed features for the importance levels
 *
 * @author Robin Wroblowski
 */
class FeatureImportanceConstraint extends \CommonBundle\Component\Validator\AbstractValidator
{
    const TOO_MANY = 'notValid';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::TOO_MANY => 'There are too many features with this importance level',
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

        $levels = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.match_profile_max_importances')
        );

        // INIT counts array
        $counts = array();
        foreach (array_keys($levels) as $key) {
            $counts[$key] = 0;
        }

        foreach ($context as $key => $val) {
            if (str_contains($key, 'feature_')) {
                $counts[$val] += 1;
            }
        }

        if ($counts[$value] > $levels[$value]) {
            if ($levels[$value] > 0) {
                $this->error(self::TOO_MANY);
                return false;
            }
        }

        return true;
    }
}
