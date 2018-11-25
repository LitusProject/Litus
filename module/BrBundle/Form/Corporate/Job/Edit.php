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

namespace BrBundle\Form\Corporate\Job;

use BrBundle\Entity\Company\Job;

/**
 * Edit Job
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \BrBundle\Form\Corporate\Job\Add
{
    /**
     * @var Job
     */
    private $job;

    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save Changes');

        if ($this->job !== null) {
            $hydrator = $this->getHydrator();
            $this->populateValues($hydrator->extract($this->job));
        }
    }

    public function setJob(Job $job)
    {
        $this->job = $job;

        return $this;
    }
}
