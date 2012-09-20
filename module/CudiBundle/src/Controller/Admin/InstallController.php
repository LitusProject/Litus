<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Controller\Admin;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\Address,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    CommonBundle\Entity\General\Bank\BankDevice,
    CommonBundle\Entity\General\Bank\MoneyUnit,
    CommonBundle\Entity\General\Config,
    CudiBundle\Entity\Articles\Options\Binding,
    CudiBundle\Entity\Articles\Options\Color,
    CudiBundle\Entity\Sales\PayDesk,
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
                    'key'         => 'cudi.file_path',
                    'value'       => 'data/cudi/files',
                    'description' => 'The path to the cudi article files',
                ),
                array(
                    'key'         => 'cudi.pdf_generator_path',
                    'value'       => 'data/cudi/pdf_generator',
                    'description' => 'The path to the PDF generator files',
                ),
                array(
                    'key'         => 'fop_command',
                    'value'       => '/usr/local/bin/fop',
                    'description' => 'The command to call Apache FOP',
                ),
                array(
                    'key'         => 'search_max_results',
                    'value'       => '30',
                    'description' => 'The maximum number of search results shown',
                ),
                array(
                    'key'         => 'cudi.mail',
                    'value'       => 'cudi@vtk.be',
                    'description' => 'The mail address of cudi',
                ),
                array(
                    'key'         => 'cudi.mail_name',
                    'value'       => 'VTK Cursusdienst',
                    'description' => 'The name of the mail sender',
                ),
                array(
                    'key'         => 'union_short_name',
                    'value'       => 'VTK',
                    'description' => 'The short name of this union',
                ),
                array(
                    'key'         => 'union_name',
                    'value'       => 'VTK vzw',
                    'description' => 'The full name of this union',
                ),
                array(
                    'key'         => 'union_logo',
                    'value'       => 'data/images/logo/logo.svg',
                    'description' => 'The path to the logo of the union',
                ),
                array(
                    'key'         => 'union_url',
                    'value'       => 'http://www.vtk.be',
                    'description' => 'The URL of the union',
                ),
                array(
                    'key'         => 'university',
                    'value'       => 'KU Leuven',
                    'description' => 'The name of the university',
                ),
                array(
                    'key'         => 'faculty',
                    'value'       => 'Faculty of Engineering',
                    'description' => 'The name of the faculty',
                ),
                array(
                    'key'         => 'cudi.name',
                    'value'       => 'Cudi',
                    'description' => 'The name of the cudi',
                ),
                array(
                    'key'         => 'cudi.person',
                    'value'       => '1',
                    'description' => 'The ID of the person responsible for the cudi',
                ),
                array(
                    'key'         => 'cudi.delivery_address_name',
                    'value'       => 'VTK Cursusdienst',
                    'description' => 'The name of the delivery address of the cudi',
                ),
                array(
                    'key'         => 'cudi.delivery_address_extra',
                    'value'       => '(inrit via Celestijnenlaan)',
                    'description' => 'The extra information of the delivery address of the cudi',
                ),
                array(
                    'key'         => 'cudi.billing_address_name',
                    'value'       => 'VTK vzw',
                    'description' => 'The name of the billing organization of the cudi',
                ),
                array(
                    'key'         => 'cudi.reservation_expire_time',
                    'value'       => 'P2W',
                    'description' => 'The time after which a reservation expires',
                ),
                array(
                    'key'         => 'cudi.reservation_extend_time',
                    'value'       => 'P2W',
                    'description' => 'The time a reservation can be extended',
                ),
                array(
                    'key'         => 'cudi.booking_assigned_mail_subject',
                    'value'       => 'New Assignments',
                    'description' => 'The subject of the mail sent by new assignments',
                ),
                array(
                    'key'         => 'cudi.booking_assigned_mail',
                    'value'       => 'Dear,

The following bookings are assigned to you:
{{ bookings }}

These reservations will expire after the first sale session after it\'s expiration date.

Please cancel a reservation if you don\'t need the article, this way we can help other students.

VTK Cudi

-- This is an automatically generated email, please do not reply --',
                    'description' => 'The mail sent when a booking is assigned'
                ),
                array(
                    'key'         => 'cudi.queue_item_barcode_prefix',
                    'value'       => '988000000000',
                    'description' => 'The start for a serving queue item barcode',
                ),
                array(
                    'key'         => 'cudi.queue_socket_port',
                    'value'       => '8899',
                    'description' => 'The port used for the websocket of the queue',
                ),
                array(
                    'key'         => 'cudi.queue_socket_remote_host',
                    'value'       => '127.0.0.1',
                    'description' => 'The remote host for the websocket of the queue',
                ),
                array(
                    'key'         => 'cudi.queue_socket_host',
                    'value'       => '127.0.0.1',
                    'description' => 'The host used for the websocket of the queue',
                ),
                array(
                    'key'         => 'cudi.prof_start_academic_year',
                    'value'       => '2012-7-15 0:0:0',
                    'description' => 'The start date of the academic year for a prof',
                ),
                array(
                    'key'         => 'cudi.purchase_prices',
                    'value'       => serialize(
                        array(
                            'binding_glued'     => 81620,
                            'binding_stapled'   => 6360,
                            'binding_none'      => 19080,
                            'recto_bw'          => 2862,
                            'recto_verso_bw'    => 2862,
                            'recto_color'       => 6360,
                            'recto_verso_color' => 10600,
                        )
                    ),
                    'description' => 'The purchase prices of an internal article ( * 100 000 )',
                ),
                array(
                    'key'         => 'cudi.sell_prices',
                    'value'       => serialize(
                        array(
                            'binding_glued'     => 83,
                            'binding_stapled'   => 7,
                            'binding_none'      => 20,
                            'recto_bw'          => 2,
                            'recto_verso_bw'    => 2,
                            'recto_color'       => 7,
                            'recto_verso_color' => 7,
                        )
                    ),
                    'description' => 'The purchase prices of an internal article ( * 100 )',
                ),
                array(
                    'key'         => 'cudi.front_address_name',
                    'value'       => 'CuDi VTK vzw',
                    'description' => 'The name of the address on the front of an article',
                ),
                array(
                    'key'         => 'cudi.article_barcode_prefix',
                    'value'       => '978',
                    'description' => 'The start for a serving queue item barcode',
                ),
                array(
                    'key'         => 'cudi.enable_collect_scanning',
                    'value'       => '1',
                    'description' => 'Enable scanning of collected items before selling',
                ),
                array(
                    'key'         => 'cudi.enable_automatic_assignment',
                    'value'       => '1',
                    'description' => 'Enable automatic assignment of bookings',
                ),
                array(
                    'key'         => 'cudi.print_socket_address',
                    'value'       => '127.0.0.1',
                    'description' => 'The ip address of the print socket',
                ),
                array(
                    'key'         => 'cudi.print_socket_port',
                    'value'       => '4444',
                    'description' => 'The port of the print socket',
                ),
            )
        );

        $this->_installAddresses();
        $this->_installBinding();
        $this->_installAcademicYear();
        $this->_installColor();
        $this->_installMoneyUnit();
        $this->_installBankDevice();
        $this->_installPayDesks();
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'cudibundle' => array(
                    'admin_article' => array(
                        'add', 'delete', 'edit', 'manage', 'search'
                    ),
                    'admin_article_comment' => array(
                        'delete', 'manage'
                    ),
                    'admin_article_file' => array(
                        'delete', 'download', 'edit', 'front', 'manage', 'progress', 'upload'
                    ),
                    'admin_article_subject' => array(
                        'delete', 'manage'
                    ),
                    'admin_cudi_mail' => array(
                        'send'
                    ),
                    'admin_prof_action' => array(
                        'completed', 'confirmArticle', 'confirmFile', 'confirm', 'manage', 'refused', 'refuse', 'view'
                    ),
                    'admin_sales_article' => array(
                        'activate', 'add', 'delete', 'edit', 'manage', 'search', 'sellProf', 'typeahead'
                    ),
                    'admin_sales_booking' => array(
                        'add', 'article', 'assign', 'assignAll', 'delete', 'edit', 'expire', 'extend', 'inactive', 'manage', 'person', 'search', 'unassign'
                    ),
                    'admin_sales_discount' => array(
                        'delete', 'manage'
                    ),
                    'admin_sales_financial' => array(
                        'deliveries', 'retours', 'sales', 'stock', 'supplier'
                    ),
                    'admin_sales_session' => array(
                        'add', 'close', 'edit', 'editRegister', 'manage', 'queueItems'
                    ),
                    'admin_stock' => array(
                        'delta', 'edit', 'manage', 'notDelivered', 'search', 'searchNotDelivered'
                    ),
                    'admin_stock_delivery' => array(
                        'add', 'delete', 'manage', 'supplier', 'typeahead'
                    ),
                    'admin_stock_order' => array(
                        'add', 'cancel', 'delete', 'edit', 'export', 'manage', 'place', 'pdf', 'supplier'
                    ),
                    'admin_stock_period' => array(
                        'manage', 'new', 'search', 'view'
                    ),
                    'admin_stock_retour' => array(
                        'add', 'delete', 'manage', 'supplier'
                    ),
                    'admin_supplier' => array(
                        'add', 'edit', 'manage'
                    ),
                    'admin_supplier_user' => array(
                        'add', 'delete', 'edit', 'manage'
                    ),
                    'prof_article' => array(
                        'add', 'edit', 'manage', 'typeahead'
                    ),
                    'prof_article_mapping' => array(
                        'add', 'delete'
                    ),
                    'prof_article_comment' => array(
                        'delete', 'manage'
                    ),
                    'prof_subject_comment' => array(
                        'delete', 'manage'
                    ),
                    'prof_file' => array(
                        'delete', 'download', 'manage', 'progress', 'upload'
                    ),
                    'prof_index' => array(
                        'index'
                    ),
                    'prof_prof' => array(
                        'add', 'delete', 'typeahead'
                    ),
                    'prof_subject' => array(
                        'manage', 'subject', 'typeahead'
                    ),
                    'sale_queue' => array(
                        'overview', 'screen', 'signin'
                    ),
                    'sale_sale' => array(
                        'return', 'sale', 'saveComment'
                    ),
                    'supplier_article' => array(
                        'manage'
                    ),
                    'supplier_index' => array(
                        'index'
                    ),
                    'booking' => array(
                        'book', 'cancel', 'view',
                    ),
                )
            )
        );

        $this->installRoles(
            array(
                'supplier' => array(
                    'system' => true,
                    'parents' => array(
                        'guest',
                    ),
                    'actions' => array(
                        'supplier_article' => array(
                            'manage'
                        ),
                        'supplier_index' => array(
                            'index'
                        ),
                    )
                ),
                'prof' => array(
                    'system' => true,
                    'parents' => array(
                        'guest',
                    ),
                    'actions' => array(
                        'prof_article' => array(
                            'add', 'edit', 'manage', 'typeahead'
                        ),
                        'prof_article_mapping' => array(
                            'add', 'delete'
                        ),
                        'prof_article_comment' => array(
                            'delete', 'manage'
                        ),
                        'prof_file' => array(
                            'delete', 'download', 'manage', 'progress', 'upload'
                        ),
                        'prof_index' => array(
                            'index'
                        ),
                        'prof_prof' => array(
                            'add', 'delete', 'typeahead'
                        ),
                        'prof_subject' => array(
                            'manage', 'subject', 'typeahead'
                        ),
                    )
                ),
                'student' => array(
                    'actions' => array(
                        'booking' => array(
                            'book', 'cancel', 'view',
                        ),
                    ),
                ),
            )
        );
    }

    private function _installBinding()
    {
        $bindings = array(
            'glued' => 'Glued',
            'none' => 'None',
            'stapled' => 'Stapled',
        );

        foreach($bindings as $code => $name) {
            $binding = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Articles\Options\Binding')
                ->findOneByCode($code);
            if (null == $binding) {
                $binding = new Binding($code, $name);
                $this->getEntityManager()->persist($binding);
            }
        }
        $this->getEntityManager()->flush();
    }

    private function _installColor()
    {
        $colors = array('White');

        foreach($colors as $item) {
            $color = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Articles\Options\Color')
                ->findOneByName($item);
            if (null == $color) {
                $color = new Color($item);
                $this->getEntityManager()->persist($color);
            }
        }
        $this->getEntityManager()->flush();
    }

    private function _installAcademicYear()
    {
        $now = new DateTime('now');
        $startAcademicYear = AcademicYear::getStartOfAcademicYear(
            $now
        );

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($startAcademicYear);

        $organizationStart = str_replace(
            '{{ year }}',
            $startAcademicYear->format('Y'),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('start_organization_year')
        );
        $organizationStart = new DateTime($organizationStart);

        if (null === $academicYear) {
            $academicYear = new AcademicYearEntity($organizationStart, $startAcademicYear);
            $this->getEntityManager()->persist($academicYear);
            $this->getEntityManager()->flush();
        }

        $organizationStart->add(
            new DateInterval('P1Y')
        );

        if ($organizationStart < new DateTime()) {
            $academicYear = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\AcademicYear')
                ->findOneByStart($organizationStart);
            if (null == $academicYear) {
                $startAcademicYear = AcademicYear::getEndOfAcademicYear(
                    $organizationStart
                );
                $academicYear = new AcademicYearEntity($organizationStart, $startAcademicYear);
                $this->getEntityManager()->persist($academicYear);
                $this->getEntityManager()->flush();
            }
        }
    }

    private function _installAddresses()
    {
        try {
            $config = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.delivery_address');
        } catch(Exception $e) {
            $address = new Address(
                'Kasteelpark Arenberg',
                41,
                3001,
                'Heverlee',
                'BE'
            );
            $this->getEntityManager()->persist($address);
            $config = new Config('cudi.delivery_address', (string) $address->getId());
            $config->setDescription('The delivery address of the cudi');
            $this->getEntityManager()->persist($config);
        }

        try {
            $config = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.billing_address');
        } catch(Exception $e) {
            $address = new Address(
                'Studentenwijk Arenberg',
                '6/0',
                3001,
                'Heverlee',
                'BE'
            );
            $this->getEntityManager()->persist($address);
            $config = new Config('cudi.billing_address', (string) $address->getId());
            $config->setDescription('The billing address of the cudi');
            $this->getEntityManager()->persist($config);
        }
    }

    private function _installMoneyUnit()
    {
        $units = array(500, 200, 100, 50, 20, 10, 5, 2, 1, 0.50, 0.20, 0.10, 0.05, 0.02, 0.01);

        foreach($units as $item) {
            $unit = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Bank\MoneyUnit')
                ->findOneByUnit($item);
            if (null == $unit) {
                $unit = new MoneyUnit($item);
                $this->getEntityManager()->persist($unit);
            }
        }
        $this->getEntityManager()->flush();
    }

    private function _installBankDevice()
    {
        $bankdevices = array('Device 1', 'Device 2');

        foreach($bankdevices as $item) {
            $bankdevice = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Bank\BankDevice')
                ->findOneByName($item);
            if (null == $bankdevice) {
                $bankdevice = new BankDevice($item);
                $this->getEntityManager()->persist($bankdevice);
            }
        }
        $this->getEntityManager()->flush();
    }

    private function _installPayDesks()
    {
        $paydesks = array(
            'paydesk_1' => '1',
            'paydesk_2' => '2',
            'paydesk_3' => '3',
        );

        foreach($paydesks as $code => $name) {
            $paydesk = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sales\PayDesk')
                ->findOneByCode($code);
            if (null == $paydesk) {
                $paydesk = new PayDesk($code, $name);
                $this->getEntityManager()->persist($paydesk);
            }
        }
        $this->getEntityManager()->flush();
    }
}
