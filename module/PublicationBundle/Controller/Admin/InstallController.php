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

namespace PublicationBundle\Controller\Admin;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\Address,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    CommonBundle\Entity\General\Bank\BankDevice,
    CommonBundle\Entity\General\Bank\MoneyUnit,
    CommonBundle\Entity\General\Config,
    CudiBundle\Entity\Article\Option\Binding,
    CudiBundle\Entity\Article\Option\Color,
    CudiBundle\Entity\Sale\PayDesk,
    DateInterval,
    DateTime,
    Exception;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function initConfig()
    {
        $this->installConfig(
            array(
                array(
                    'key'         => 'publication.bakske_id',
                    'value'       => '1',
                    'description' => 'The ID of the publication that represents the weekly magazine',
                ),
                array(
                    'key'         => 'publication.public_pdf_directory',
                    'value'       => '/_publications/pdf/',
                    'description' => 'The public pdf direction of publication',
                ),
                array(
                    'key'         => 'publication.public_html_directory',
                    'value'       => '/_publications/html/',
                    'description' => 'The public html direction of publication',
                ),
            )
        );
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'publicationbundle' => array(
                    'publication_admin_publication' => array(
                        'add', 'delete', 'edit', 'manage',
                    ),
                    'publication_admin_edition_html' => array(
                        'add', 'delete', 'manage', 'progress', 'upload'
                    ),
                    'publication_admin_edition_pdf' => array(
                        'add', 'delete', 'manage', 'progress', 'upload', 'view'
                    ),

                    'publication_edition_html' => array(
                        'view',
                    ),
                    'publication_archive' => array(
                        'overview', 'view', 'year',
                    ),
                )
            )
        );
    }
}
