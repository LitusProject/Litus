<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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
                    'key'         => 'cudi.front_page_cache_dir',
                    'value'       => 'data/cache/article',
                    'description' => 'The path to the article front page cache files',
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
                    'key'         => 'cudi.booking_assigned_mail',
                    'value'       => serialize(
                        array(
                            'en' => array(
                                'subject' => 'New Assignments',
                                'content' => 'Dear,

The following bookings are assigned to you:
{{ bookings }}#expires#expires on#expires#

These reservations will expire after the first sale session after its expiration date.

Please cancel a reservation if you don\'t need the article, this way we can help other students.

The opening hours of Cudi are:
{{ openingHours }}#no_opening_hours#No opening hours known.#no_opening_hours#

VTK Cudi

-- This is an automatically generated email, please do not reply --'
                            ),
                            'nl' => array(
                                'subject' => 'Nieuwe Toewijzingen',
                                'content' => 'Beste,

De volgende reservaties zijn aan u toegewezen:
{{ bookings }}#expires#vervalt op#expires#

Deze reservaties zullen vervallen op het einde van de eerste verkoop sessie na de vervaldatum.

Gelieve een reservatie te annuleren als je het artikel niet meer nodig hebt, op deze manier kunnen we andere studenten helpen.

De openingsuren van cudi zijn:
{{ openingHours }}#no_opening_hours#Geen openingsuren gekend.#no_opening_hours#

VTK Cudi

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --'
                            ),
                        )
                    ),
                    'description' => 'The mail sent when a booking is assigned'
                ),
                array(
                    'key'         => 'cudi.booking_expire_warning_mail',
                    'value'       => serialize(
                        array(
                            'en' => array(
                                'subject' => 'Assignment Expiration Warning',
                                'content' => 'Dear,

The following bookings are going to expire soon:
{{ bookings }}#expires#expires on#expires#

These reservations will expire after the first sale session after its expiration date.

Please cancel a reservation if you don\'t need the article, this way we can help other students.

The opening hours of Cudi are:
{{ openingHours }}#no_opening_hours#No opening hours known.#no_opening_hours#

VTK Cudi

-- This is an automatically generated email, please do not reply --'
                            ),
                            'nl' => array(
                                'subject' => 'Waarschuwing Vervallen Toewijzingen',
                                'content' => 'Beste,

De volgende reservaties gaan binnekort vervallen:
{{ bookings }}#expires#vervalt op#expires#

Deze reservaties zullen vervallen op het einde van de eerste verkoop sessie na de vervaldatum.

Gelieve een reservatie te annuleren als je het artikel niet meer nodig hebt, op deze manier kunnen we andere studenten helpen.

De openingsuren van cudi zijn:
{{ openingHours }}#no_opening_hours#Geen openingsuren gekend.#no_opening_hours#

VTK Cudi

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --'
                            ),
                        )
                    ),
                    'description' => 'The mail sent when a booking is about to expire'
                ),
                array(
                    'key'         => 'cudi.booking_expire_mail',
                    'value'       => serialize(
                        array(
                            'en' => array(
                                'subject' => 'Assignment Expiration',
                                'content' => 'Dear,

The following bookings have expired:
{{ bookings }}#expires#expired on#expires#

VTK Cudi

-- This is an automatically generated email, please do not reply --'
                            ),
                            'nl' => array(
                                'subject' => 'Vervallen Toewijzingen',
                                'content' => 'Beste,

De volgende reservaties zijn vervallen:
{{ bookings }}#expires#verviel op#expires#

VTK Cudi

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --'
                            ),
                        )
                    ),
                    'description' => 'The mail sent when a booking is expired'
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
                    'key'         => 'cudi.queue_socket_key',
                    'value'       => md5(uniqid(rand(), true)),
                    'description' => 'The key used for the websocket of the queue',
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
                            'hardcover'         => 0,
                        )
                    ),
                    'description' => 'The purchase prices of an internal article (multiplied by 100 000)',
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
                            'hardcover'         => 0,
                        )
                    ),
                    'description' => 'The purchase prices of an internal article (multiplied by 100)',
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
                    'key'         => 'cudi.enable_automatic_expire',
                    'value'       => '1',
                    'description' => 'Enable automatic expire of bookings',
                ),
                array(
                    'key'         => 'cudi.enable_bookings',
                    'value'       => '1',
                    'description' => 'Enable users to create bookings',
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
                array(
                    'key'         => 'cudi.enable_printers',
                    'value'       => '1',
                    'description' => 'Flag whether the printers are enabled',
                ),
                array(
                    'key'         => 'cudi.printer_socket_key',
                    'value'       => md5(uniqid(rand(), true)),
                    'description' => 'The key used for printing',
                ),
                array(
                    'key'         => 'cudi.ticket_title',
                    'value'       => 'Litus Cursusdienst',
                    'description' => 'The title printed on a ticket',
                ),
                array(
                    'key'         => 'cudi.printers',
                    'value'       => serialize(
                        array(
                            'signin'    => 'LITUS-SignIn',
                            'collect_1' => 'LITUS-Collect',
                            'collect_2' => 'LITUS-Collect',
                            'collect_3' => 'LITUS-Collect',
                            'paydesk_1' => 'LITUS-SaleOne',
                            'paydesk_2' => 'LITUS-SaleTwo',
                            'paydesk_3' => 'LITUS-SaleThree',
                        )
                    ),
                    'description' => 'The names of the printers',
                ),
                array(
                    'key'         => 'cudi.tshirt_article',
                    'value'       => serialize(
                        array(
                            'F_S'   => 232,
                            'F_M'   => 233,
                            'F_L'   => 234,
                            'F_XL'  => 235,

                            'M_S'   => 228,
                            'M_M'   => 229,
                            'M_L'   => 230,
                            'M_XL'  => 231,
                        )
                    ),
                    'description' => 'The T-shirt articles',
                ),
                array(
                    'key'         => 'cudi.registration_articles',
                    'value'       => serialize(
                        array()
                    ),
                    'description' => 'The articles assigned at registration',
                ),
                array(
                    'key'         => 'cudi.bookings_closed_exceptions',
                    'value'       => serialize(
                        array()
                    ),
                    'description' => 'The articles assigned at registration',
                ),
                array(
                    'key'         => 'cudi.number_queue_items',
                    'value'       => '50',
                    'description' => 'The number of queue items shown in sale app',
                ),
                array(
                    'key'         => 'cudi.opening_hours_page',
                    'value'       => '0',
                    'description' => 'The id of the opening hour page',
                ),
                array(
                    'key'         => 'cudi.expiration_warning_interval',
                    'value'       => 'P4D',
                    'description' => 'The interval for sending a warning mail before expiring a booking',
                ),
                array(
                    'key'         => 'cudi.catalog_update_mail',
                    'value'       => serialize(
                        array(
                            'en' => array(
                                'subject' => 'Catalog Updates',
                                'content' => 'Dear,

The catalog of our cudi has been updated:
{{ updates }}#bookable#is now bookable#bookable# #unbookable#is not bookable anymore#unbookable# #added#is added to the catalog#added# #removed#is removed from the catalog#removed#

VTK Cudi

-- This is an automatically generated email, please do not reply --'
                            ),
                            'nl' => array(
                                'subject' => 'Catalogus Aanpassingen',
                                'content' => 'Beste,

De catalogus van onze cudi is aangepast:
{{ updates }}#bookable#is nu reserveerbaar#bookable# #unbookable#is niet meer reserveerbaar#unbookable# #added#is toegevoegd aan de catalogus#added# #removed#is verwijderd van de catalogus#removed#

VTK Cudi

-- Dit is een automatisch gegenereerde email, gelieve niet te antwoorden --'
                            ),
                        )
                    ),
                    'description' => 'The content of the mail send for catalog updates',
                ),
                array(
                    'key'         => 'cudi.sale_light_version',
                    'value'       => '0',
                    'description' => 'Flag whether to show the light version of the sale app (no queue)',
                ),
                array(
                    'key'         => 'cudi.order_job_id',
                    'value'       => 'vtk-{{ date }}',
                    'description' => 'The job id for a XML exported order',
                ),
                array(
                    'key'         => 'cudi.booking_mails_to_cudi',
                    'value'       => '1',
                    'description' => 'Send the cudi booking mails (assigned, expired, warning) to the cudi address',
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
                    'cudi_admin_article' => array(
                        'add', 'convertToExternal', 'convertToInternal', 'delete', 'duplicate', 'edit', 'history', 'manage', 'search'
                    ),
                    'cudi_admin_article_comment' => array(
                        'delete', 'manage'
                    ),
                    'cudi_admin_article_file' => array(
                        'delete', 'download', 'edit', 'front', 'manage', 'progress', 'upload'
                    ),
                    'cudi_admin_article_subject' => array(
                        'delete', 'manage'
                    ),
                    'cudi_admin_mail' => array(
                        'send'
                    ),
                    'cudi_admin_prof_action' => array(
                        'completed', 'confirmArticle', 'confirmFile', 'confirm', 'manage', 'refused', 'refuse', 'view'
                    ),
                    'cudi_admin_sales_article' => array(
                        'activate', 'add', 'assignAll', 'cancelBookings', 'delete', 'edit', 'history', 'mail', 'manage', 'search', 'typeahead', 'view'
                    ),
                    'cudi_admin_sales_article_barcode' => array(
                        'delete', 'manage'
                    ),
                    'cudi_admin_sales_booking' => array(
                        'actions', 'add', 'article', 'assign', 'assignAll', 'delete', 'deleteAll', 'edit', 'extendAll', 'expire', 'expireAll', 'extend', 'inactive', 'manage', 'person', 'search', 'unassign', 'undo'
                    ),
                    'cudi_admin_sales_article_discount' => array(
                        'delete', 'manage'
                    ),
                    'cudi_admin_sales_article_discount_template' => array(
                        'add', 'delete', 'edit', 'manage'
                    ),
                    'cudi_admin_sales_financial' => array(
                        'export', 'overview', 'period'
                    ),
                    'cudi_admin_sales_financial_delivered' => array(
                        'article', 'articlesSearch', 'articles', 'individualSearch', 'individual', 'supplierSearch', 'supplier', 'suppliers'
                    ),
                    'cudi_admin_sales_financial_ordered' => array(
                        'individualSearch', 'individual', 'orderSearch', 'order', 'orders', 'ordersSearch', 'supplierSearch', 'supplier', 'suppliers'
                    ),
                    'cudi_admin_sales_financial_sold' => array(
                        'article', 'articleSearch', 'articlesSearch', 'articles', 'individualSearch', 'individual', 'sessionSearch', 'session', 'sessions', 'supplierSearch', 'supplier', 'suppliers'
                    ),
                    'cudi_admin_sales_financial_returned' => array(
                        'article', 'articleSearch', 'articlesSearch', 'articles', 'individualSearch', 'individual', 'sessionSearch', 'session', 'sessions'
                    ),
                    'cudi_admin_sales_session' => array(
                        'add', 'close', 'edit', 'editRegister', 'manage', 'queueItems', 'killSocket'
                    ),
                    'cudi_admin_sales_session_restriction' => array(
                        'delete', 'manage'
                    ),
                    'cudi_admin_sales_session_openinghour' => array(
                        'add', 'edit', 'delete', 'manage', 'old'
                    ),
                    'cudi_admin_stock' => array(
                        'bulkUpdate', 'delta', 'download', 'edit', 'export', 'manage', 'notDelivered', 'search', 'searchNotDelivered', 'view'
                    ),
                    'cudi_admin_stock_delivery' => array(
                        'add', 'delete', 'manage', 'supplier', 'typeahead'
                    ),
                    'cudi_admin_stock_order' => array(
                        'add', 'cancel', 'delete', 'edit', 'editItem', 'export', 'manage', 'overview', 'place', 'pdf', 'search', 'supplier'
                    ),
                    'cudi_admin_stock_period' => array(
                        'manage', 'new', 'search', 'view'
                    ),
                    'cudi_admin_stock_retour' => array(
                        'add', 'delete', 'manage', 'supplier'
                    ),
                    'cudi_admin_supplier' => array(
                        'add', 'edit', 'manage'
                    ),
                    'cudi_admin_supplier_user' => array(
                        'add', 'delete', 'edit', 'manage'
                    ),
                    'cudi_prof_auth' => array(
                        'login', 'logout', 'shibboleth',
                    ),
                    'cudi_prof_article' => array(
                        'add', 'addFromSubject', 'edit', 'manage', 'typeahead'
                    ),
                    'cudi_prof_article_mapping' => array(
                        'activate', 'add', 'delete'
                    ),
                    'cudi_prof_article_comment' => array(
                        'delete', 'manage'
                    ),
                    'cudi_prof_subject_comment' => array(
                        'delete', 'manage'
                    ),
                    'cudi_prof_file' => array(
                        'delete', 'download', 'manage', 'progress', 'upload'
                    ),
                    'cudi_prof_index' => array(
                        'index'
                    ),
                    'cudi_prof_prof' => array(
                        'add', 'delete', 'typeahead'
                    ),
                    'cudi_prof_subject' => array(
                        'manage', 'subject', 'typeahead'
                    ),
                    'cudi_prof_help' => array(
                        'index'
                    ),
                    'cudi_sale_queue' => array(
                        'overview', 'screen', 'signin'
                    ),
                    'cudi_sale_sale' => array(
                        'return', 'returnPrice', 'sale'
                    ),
                    'cudi_supplier_article' => array(
                        'manage'
                    ),
                    'cudi_supplier_index' => array(
                        'index'
                    ),
                    'cudi_booking' => array(
                        'book', 'bookSearch', 'cancel', 'keepUpdated', 'search', 'view'
                    ),
                    'cudi_opening_hour' => array(
                        'week'
                    )
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
                        'cudi_supplier_article' => array(
                            'manage'
                        ),
                        'cudi_supplier_index' => array(
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
                        'cudi_prof_article' => array(
                            'add', 'addFromSubject', 'edit', 'manage', 'typeahead'
                        ),
                        'cudi_prof_article_mapping' => array(
                            'activate', 'add', 'delete'
                        ),
                        'cudi_prof_article_comment' => array(
                            'delete', 'manage'
                        ),
                        'cudi_prof_file' => array(
                            'delete', 'download', 'manage', 'progress', 'upload'
                        ),
                        'cudi_prof_index' => array(
                            'index'
                        ),
                        'cudi_prof_prof' => array(
                            'add', 'delete', 'typeahead'
                        ),
                        'cudi_prof_subject' => array(
                            'manage', 'subject', 'typeahead'
                        ),
                    )
                ),
                'student' => array(
                    'system' => true,
                    'parents' => array(
                        'guest',
                    ),
                    'actions' => array(
                        'cudi_booking' => array(
                            'book', 'bookSearch', 'cancel', 'keepUpdated', 'search', 'view'
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
                ->getRepository('CudiBundle\Entity\Article\Option\Binding')
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
                ->getRepository('CudiBundle\Entity\Article\Option\Color')
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
                null,
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
                '6',
                '0',
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
                ->getRepository('CudiBundle\Entity\Sale\PayDesk')
                ->findOneByCode($code);
            if (null == $paydesk) {
                $paydesk = new PayDesk($code, $name);
                $this->getEntityManager()->persist($paydesk);
            }
        }
        $this->getEntityManager()->flush();
    }
}
