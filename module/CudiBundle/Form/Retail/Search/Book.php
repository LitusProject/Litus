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

namespace CudiBundle\Form\Retail\Search;

/**
 * Search Book
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Book extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function __construct($name = null)
    {
        parent::__construct($name, false, false);
    }

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'search_string',
                'label'      => 'Search String',
                'required'   => true,
                'attributes' => array(
                    'id'      => 'search_string',
                    'pattern' => '.{3}.*',
                ),
            )
        );

        $this->remove('csrf');
    }
}
