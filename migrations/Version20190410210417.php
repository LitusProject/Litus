<?php
declare(strict_types=1);

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

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use RuntimeException;

/**
 * Version 20190410210417
 */
class Version20190410210417 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on PostgreSQL.'
        );

        $this->addSql('ALTER TABLE acl.acl_actions RENAME CONSTRAINT actions_pkey TO acl_actions_pkey');
        $this->addSql('ALTER TABLE acl.acl_resources RENAME CONSTRAINT resources_pkey TO acl_resources_pkey');
        $this->addSql('ALTER TABLE acl.acl_roles RENAME CONSTRAINT roles_pkey TO acl_roles_pkey');
        $this->addSql('ALTER TABLE acl.acl_roles_actions_map RENAME CONSTRAINT roles_actions_map_pkey TO acl_roles_actions_map_pkey');
        $this->addSql('ALTER TABLE acl.acl_roles_inheritance_map RENAME CONSTRAINT roles_inheritance_map_pkey TO acl_roles_inheritance_map_pkey');
        $this->addSql('ALTER TABLE api.api_keys RENAME CONSTRAINT keys_pkey TO api_keys_pkey');
        $this->addSql('ALTER TABLE api.api_keys_roles_map RENAME CONSTRAINT keys_roles_map_pkey TO api_keys_roles_map_pkey');
        $this->addSql('ALTER TABLE br.br_collaborator RENAME CONSTRAINT collaborator_pkey TO br_collaborator_pkey');
        $this->addSql('ALTER TABLE br.br_companies RENAME CONSTRAINT companies_pkey TO br_companies_pkey');
        $this->addSql('ALTER TABLE br.br_companies_cvbooks RENAME CONSTRAINT companies_cvbooks_pkey TO br_companies_cvbooks_pkey');
        $this->addSql('ALTER TABLE br.br_companies_events RENAME CONSTRAINT companies_events_pkey TO br_companies_events_pkey');
        $this->addSql('ALTER TABLE br.br_companies_jobs RENAME CONSTRAINT companies_jobs_pkey TO br_companies_jobs_pkey');
        $this->addSql('ALTER TABLE br.br_companies_logos RENAME CONSTRAINT companies_logos_pkey TO br_companies_logos_pkey');
        $this->addSql('ALTER TABLE br.br_companies_pages RENAME CONSTRAINT companies_pages_pkey TO br_companies_pages_pkey');
        $this->addSql('ALTER TABLE br.br_companies_request RENAME CONSTRAINT companies_request_pkey TO br_companies_request_pkey');
        $this->addSql('ALTER TABLE br.br_companies_request_internship RENAME CONSTRAINT companies_request_internship_pkey TO br_companies_request_internship_pkey');
        $this->addSql('ALTER TABLE br.br_companies_request_vacancy RENAME CONSTRAINT companies_request_vacancy_pkey TO br_companies_request_vacancy_pkey');
        $this->addSql('ALTER TABLE br.br_contract_history RENAME CONSTRAINT contract_history_pkey TO br_contract_history_pkey');
        $this->addSql('ALTER TABLE br.br_contracts RENAME CONSTRAINT contracts_pkey TO br_contracts_pkey');
        $this->addSql('ALTER TABLE br.br_contracts_entries RENAME CONSTRAINT contracts_entries_pkey TO br_contracts_entries_pkey');
        $this->addSql('ALTER TABLE br.br_cv_entries RENAME CONSTRAINT cv_entries_pkey TO br_cv_entries_pkey');
        $this->addSql('ALTER TABLE br.br_cv_experiences RENAME CONSTRAINT cv_experiences_pkey TO br_cv_experiences_pkey');
        $this->addSql('ALTER TABLE br.br_cv_languages RENAME CONSTRAINT cv_languages_pkey TO br_cv_languages_pkey');
        $this->addSql('ALTER TABLE br.br_event_company_map RENAME CONSTRAINT event_company_map_pkey TO br_event_company_map_pkey');
        $this->addSql('ALTER TABLE br.br_events RENAME CONSTRAINT events_pkey TO br_events_pkey');
        $this->addSql('ALTER TABLE br.br_invoice_history RENAME CONSTRAINT invoice_history_pkey TO br_invoice_history_pkey');
        $this->addSql('ALTER TABLE br.br_invoices RENAME CONSTRAINT invoices_pkey TO br_invoices_pkey');
        $this->addSql('ALTER TABLE br.br_invoices_contract RENAME CONSTRAINT invoices_contract_pkey TO br_invoices_contract_pkey');
        $this->addSql('ALTER TABLE br.br_invoices_entries RENAME CONSTRAINT invoices_entries_pkey TO br_invoices_entries_pkey');
        $this->addSql('ALTER TABLE br.br_invoices_manual RENAME CONSTRAINT invoices_manual_pkey TO br_invoices_manual_pkey');
        $this->addSql('ALTER TABLE br.br_orders RENAME CONSTRAINT orders_pkey TO br_orders_pkey');
        $this->addSql('ALTER TABLE br.br_orders_entries RENAME CONSTRAINT orders_entries_pkey TO br_orders_entries_pkey');
        $this->addSql('ALTER TABLE br.br_page_years RENAME CONSTRAINT page_years_pkey TO br_page_years_pkey');
        $this->addSql('ALTER TABLE br.br_products RENAME CONSTRAINT products_pkey TO br_products_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_articles RENAME CONSTRAINT articles_pkey TO cudi_articles_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_articles_external RENAME CONSTRAINT articles_external_pkey TO cudi_articles_external_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_articles_history RENAME CONSTRAINT articles_history_pkey TO cudi_articles_history_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_articles_internal RENAME CONSTRAINT articles_internal_pkey TO cudi_articles_internal_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_articles_notifications_subscriptions RENAME CONSTRAINT articles_notifications_subscriptions_pkey TO cudi_articles_notifications_subscriptions_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_articles_options_bindings RENAME CONSTRAINT articles_options_bindings_pkey TO cudi_articles_options_bindings_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_articles_options_colors RENAME CONSTRAINT articles_options_colors_pkey TO cudi_articles_options_colors_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_articles_subjects_map RENAME CONSTRAINT articles_subjects_map_pkey TO cudi_articles_subjects_map_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_comments_articles_map RENAME CONSTRAINT comments_articles_map_pkey TO cudi_comments_articles_map_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_comments_comments RENAME CONSTRAINT comments_comments_pkey TO cudi_comments_comments_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_files_articles_map RENAME CONSTRAINT files_articles_map_pkey TO cudi_files_articles_map_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_files_files RENAME CONSTRAINT files_files_pkey TO cudi_files_files_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_isic_card RENAME CONSTRAINT isic_card_pkey TO cudi_isic_card_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_log RENAME CONSTRAINT log_pkey TO cudi_log_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_log_articles_sales_bookable RENAME CONSTRAINT log_articles_sales_bookable_pkey TO cudi_log_articles_sales_bookable_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_log_articles_sales_unbookable RENAME CONSTRAINT log_articles_sales_unbookable_pkey TO cudi_log_articles_sales_unbookable_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_log_articles_subject_map_add RENAME CONSTRAINT log_articles_subject_map_add_pkey TO cudi_log_articles_subject_map_add_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_log_articles_subject_map_remove RENAME CONSTRAINT log_articles_subject_map_remove_pkey TO cudi_log_articles_subject_map_remove_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_log_sales_assignments RENAME CONSTRAINT log_sales_assignments_pkey TO cudi_log_sales_assignments_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_log_sales_cancellations RENAME CONSTRAINT log_sales_cancellations_pkey TO cudi_log_sales_cancellations_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_log_sales_prof_version RENAME CONSTRAINT log_sales_prof_version_pkey TO cudi_log_sales_prof_version_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_prof_actions RENAME CONSTRAINT prof_actions_pkey TO cudi_prof_actions_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_articles RENAME CONSTRAINT sales_articles_pkey TO cudi_sales_articles_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_articles_barcodes RENAME CONSTRAINT sales_articles_barcodes_pkey TO cudi_sales_articles_barcodes_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_articles_discounts_discounts RENAME CONSTRAINT sales_articles_discounts_discounts_pkey TO cudi_sales_articles_discounts_discounts_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_articles_discounts_templates RENAME CONSTRAINT sales_articles_discounts_templates_pkey TO cudi_sales_articles_discounts_templates_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_articles_restrictions RENAME CONSTRAINT sales_articles_restrictions_pkey TO cudi_sales_articles_restrictions_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_articles_restrictions_amount RENAME CONSTRAINT sales_articles_restrictions_amount_pkey TO cudi_sales_articles_restrictions_amount_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_articles_restrictions_available RENAME CONSTRAINT sales_articles_restrictions_available_pkey TO cudi_sales_articles_restrictions_available_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_articles_restrictions_member RENAME CONSTRAINT sales_articles_restrictions_member_pkey TO cudi_sales_articles_restrictions_member_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_articles_restrictions_study RENAME CONSTRAINT sales_articles_restrictions_study_pkey TO cudi_sales_articles_restrictions_study_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_articles_restrictions_study_map RENAME CONSTRAINT sales_articles_restrictions_study_map_pkey TO cudi_sales_articles_restrictions_study_map_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_bookings RENAME CONSTRAINT sales_bookings_pkey TO cudi_sales_bookings_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_history RENAME CONSTRAINT sales_history_pkey TO cudi_sales_history_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_pay_desks RENAME CONSTRAINT sales_pay_desks_pkey TO cudi_sales_pay_desks_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_queue_items RENAME CONSTRAINT sales_queue_items_pkey TO cudi_sales_queue_items_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_return_items RENAME CONSTRAINT sales_return_items_pkey TO cudi_sales_return_items_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_sale_items RENAME CONSTRAINT sales_sale_items_pkey TO cudi_sales_sale_items_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_session_openinghours RENAME CONSTRAINT sales_session_openinghours_pkey TO cudi_sales_session_openinghours_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_session_openinghours_translations RENAME CONSTRAINT sales_session_openinghours_translations_pkey TO cudi_sales_session_openinghours_translations_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_session_restriction RENAME CONSTRAINT sales_session_restriction_pkey TO cudi_sales_session_restriction_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_session_restriction_name RENAME CONSTRAINT sales_session_restriction_name_pkey TO cudi_sales_session_restriction_name_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_session_restriction_study RENAME CONSTRAINT sales_session_restriction_study_pkey TO cudi_sales_session_restriction_study_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_session_restriction_year RENAME CONSTRAINT sales_session_restriction_year_pkey TO cudi_sales_session_restriction_year_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_session_restrictions_study_map RENAME CONSTRAINT sales_session_restrictions_study_map_pkey TO cudi_sales_session_restrictions_study_map_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_sales_sessions RENAME CONSTRAINT sales_sessions_pkey TO cudi_sales_sessions_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_stock_deliveries RENAME CONSTRAINT stock_deliveries_pkey TO cudi_stock_deliveries_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_stock_orders RENAME CONSTRAINT stock_orders_pkey TO cudi_stock_orders_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_stock_orders_items RENAME CONSTRAINT stock_orders_items_pkey TO cudi_stock_orders_items_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_stock_orders_virtual RENAME CONSTRAINT stock_orders_virtual_pkey TO cudi_stock_orders_virtual_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_stock_periods RENAME CONSTRAINT stock_periods_pkey TO cudi_stock_periods_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_stock_periods_values_deltas RENAME CONSTRAINT stock_periods_values_deltas_pkey TO cudi_stock_periods_values_deltas_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_stock_periods_values_starts RENAME CONSTRAINT stock_periods_values_starts_pkey TO cudi_stock_periods_values_starts_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_stock_retours RENAME CONSTRAINT stock_retours_pkey TO cudi_stock_retours_pkey');
        $this->addSql('ALTER TABLE cudi.cudi_suppliers RENAME CONSTRAINT suppliers_pkey TO cudi_suppliers_pkey');
        $this->addSql('ALTER TABLE forms.forms_entries RENAME CONSTRAINT entries_pkey TO forms_entries_pkey');
        $this->addSql('ALTER TABLE forms.forms_fields RENAME CONSTRAINT fields_pkey TO forms_fields_pkey');
        $this->addSql('ALTER TABLE forms.forms_fields_checkboxes RENAME CONSTRAINT fields_checkboxes_pkey TO forms_fields_checkboxes_pkey');
        $this->addSql('ALTER TABLE forms.forms_fields_dropdowns RENAME CONSTRAINT fields_dropdowns_pkey TO forms_fields_dropdowns_pkey');
        $this->addSql('ALTER TABLE forms.forms_fields_files RENAME CONSTRAINT fields_files_pkey TO forms_fields_files_pkey');
        $this->addSql('ALTER TABLE forms.forms_fields_options RENAME CONSTRAINT fields_options_pkey TO forms_fields_options_pkey');
        $this->addSql('ALTER TABLE forms.forms_fields_options_translations RENAME CONSTRAINT fields_options_translations_pkey TO forms_fields_options_translations_pkey');
        $this->addSql('ALTER TABLE forms.forms_fields_texts RENAME CONSTRAINT fields_texts_pkey TO forms_fields_texts_pkey');
        $this->addSql('ALTER TABLE forms.forms_fields_timeslot RENAME CONSTRAINT fields_timeslot_pkey TO forms_fields_timeslot_pkey');
        $this->addSql('ALTER TABLE forms.forms_fields_timeslots_translations RENAME CONSTRAINT fields_timeslots_translations_pkey TO forms_fields_timeslots_translations_pkey');
        $this->addSql('ALTER TABLE forms.forms_fields_translations RENAME CONSTRAINT fields_translations_pkey TO forms_fields_translations_pkey');
        $this->addSql('ALTER TABLE forms.forms_guests_info RENAME CONSTRAINT guests_info_pkey TO forms_guests_info_pkey');
        $this->addSql('ALTER TABLE forms.forms_mails RENAME CONSTRAINT mails_pkey TO forms_mails_pkey');
        $this->addSql('ALTER TABLE forms.forms_mails_translations RENAME CONSTRAINT mails_translations_pkey TO forms_mails_translations_pkey');
        $this->addSql('ALTER TABLE forms.forms_viewers RENAME CONSTRAINT viewers_pkey TO forms_viewers_pkey');
        $this->addSql('ALTER TABLE gallery.gallery_albums RENAME CONSTRAINT albums_pkey TO gallery_albums_pkey');
        $this->addSql('ALTER TABLE gallery.gallery_photos RENAME CONSTRAINT photos_pkey TO gallery_photos_pkey');
        $this->addSql('ALTER TABLE gallery.gallery_translations RENAME CONSTRAINT translations_pkey TO gallery_translations_pkey');
        $this->addSql('ALTER TABLE general.general_academic_years RENAME CONSTRAINT academic_years_pkey TO general_academic_years_pkey');
        $this->addSql('ALTER TABLE general.general_address_cities RENAME CONSTRAINT address_cities_pkey TO general_address_cities_pkey');
        $this->addSql('ALTER TABLE general.general_address_streets RENAME CONSTRAINT address_streets_pkey TO general_address_streets_pkey');
        $this->addSql('ALTER TABLE general.general_addresses RENAME CONSTRAINT addresses_pkey TO general_addresses_pkey');
        $this->addSql('ALTER TABLE general.general_bank_bank_devices RENAME CONSTRAINT bank_bank_devices_pkey TO general_bank_bank_devices_pkey');
        $this->addSql('ALTER TABLE general.general_bank_bank_devices_amounts RENAME CONSTRAINT bank_bank_devices_amounts_pkey TO general_bank_bank_devices_amounts_pkey');
        $this->addSql('ALTER TABLE general.general_bank_cash_registers RENAME CONSTRAINT bank_cash_registers_pkey TO general_bank_cash_registers_pkey');
        $this->addSql('ALTER TABLE general.general_bank_money_units RENAME CONSTRAINT bank_money_units_pkey TO general_bank_money_units_pkey');
        $this->addSql('ALTER TABLE general.general_bank_money_units_amounts RENAME CONSTRAINT bank_money_units_amounts_pkey TO general_bank_money_units_amounts_pkey');
        $this->addSql('ALTER TABLE general.general_config RENAME CONSTRAINT config_pkey TO general_config_pkey');
        $this->addSql('ALTER TABLE general.general_languages RENAME CONSTRAINT languages_pkey TO general_languages_pkey');
        $this->addSql('ALTER TABLE general.general_locations RENAME CONSTRAINT locations_pkey TO general_locations_pkey');
        $this->addSql('ALTER TABLE general.general_organizations RENAME CONSTRAINT organizations_pkey TO general_organizations_pkey');
        $this->addSql('ALTER TABLE general.general_organizations_units RENAME CONSTRAINT organizations_units_pkey TO general_organizations_units_pkey');
        $this->addSql('ALTER TABLE general.general_organizations_units_coordinator_roles_map RENAME CONSTRAINT organizations_units_coordinator_roles_map_pkey TO general_organizations_units_coordinator_roles_map_pkey');
        $this->addSql('ALTER TABLE general.general_organizations_units_roles_map RENAME CONSTRAINT organizations_units_roles_map_pkey TO general_organizations_units_roles_map_pkey');
        $this->addSql('ALTER TABLE general.general_promotions RENAME CONSTRAINT promotions_pkey TO general_promotions_pkey');
        $this->addSql('ALTER TABLE general.general_promotions_academic RENAME CONSTRAINT promotions_academic_pkey TO general_promotions_academic_pkey');
        $this->addSql('ALTER TABLE general.general_promotions_external RENAME CONSTRAINT promotions_external_pkey TO general_promotions_external_pkey');
        $this->addSql('ALTER TABLE general.general_visits RENAME CONSTRAINT visits_pkey TO general_visits_pkey');
        $this->addSql('ALTER TABLE logistics.logistics_drivers RENAME CONSTRAINT drivers_pkey TO logistics_drivers_pkey');
        $this->addSql('ALTER TABLE logistics.logistics_drivers_years RENAME CONSTRAINT drivers_years_pkey TO logistics_drivers_years_pkey');
        $this->addSql('ALTER TABLE logistics.logistics_lease_items RENAME CONSTRAINT lease_items_pkey TO logistics_lease_items_pkey');
        $this->addSql('ALTER TABLE logistics.logistics_lease_lease RENAME CONSTRAINT lease_lease_pkey TO logistics_lease_lease_pkey');
        $this->addSql('ALTER TABLE logistics.logistics_reservations RENAME CONSTRAINT reservations_pkey TO logistics_reservations_pkey');
        $this->addSql('ALTER TABLE logistics.logistics_reservations_piano RENAME CONSTRAINT reservations_piano_pkey TO logistics_reservations_piano_pkey');
        $this->addSql('ALTER TABLE logistics.logistics_reservations_van RENAME CONSTRAINT reservations_van_pkey TO logistics_reservations_van_pkey');
        $this->addSql('ALTER TABLE logistics.logistics_resources RENAME CONSTRAINT resources_pkey TO logistics_resources_pkey');
        $this->addSql('ALTER TABLE mail.mail_aliases RENAME CONSTRAINT aliases_pkey TO mail_aliases_pkey');
        $this->addSql('ALTER TABLE mail.mail_aliases_academic RENAME CONSTRAINT aliases_academic_pkey TO mail_aliases_academic_pkey');
        $this->addSql('ALTER TABLE mail.mail_aliases_external RENAME CONSTRAINT aliases_external_pkey TO mail_aliases_external_pkey');
        $this->addSql('ALTER TABLE mail.mail_lists RENAME CONSTRAINT lists_pkey TO mail_lists_pkey');
        $this->addSql('ALTER TABLE mail.mail_lists_admin_roles RENAME CONSTRAINT lists_admin_roles_pkey TO mail_lists_admin_roles_pkey');
        $this->addSql('ALTER TABLE mail.mail_lists_admins RENAME CONSTRAINT lists_admins_pkey TO mail_lists_admins_pkey');
        $this->addSql('ALTER TABLE mail.mail_lists_entries RENAME CONSTRAINT lists_entries_pkey TO mail_lists_entries_pkey');
        $this->addSql('ALTER TABLE mail.mail_lists_entries_lists RENAME CONSTRAINT lists_entries_lists_pkey TO mail_lists_entries_lists_pkey');
        $this->addSql('ALTER TABLE mail.mail_lists_entries_people_academic RENAME CONSTRAINT lists_entries_people_academic_pkey TO mail_lists_entries_people_academic_pkey');
        $this->addSql('ALTER TABLE mail.mail_lists_entries_people_external RENAME CONSTRAINT lists_entries_people_external_pkey TO mail_lists_entries_people_external_pkey');
        $this->addSql('ALTER TABLE mail.mail_lists_named RENAME CONSTRAINT lists_named_pkey TO mail_lists_named_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_banners RENAME CONSTRAINT banners_pkey TO nodes_banners_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_events RENAME CONSTRAINT events_pkey TO nodes_events_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_events_translations RENAME CONSTRAINT events_translations_pkey TO nodes_events_translations_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_form_groups RENAME CONSTRAINT form_groups_pkey TO nodes_form_groups_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_form_groups_mapping RENAME CONSTRAINT form_groups_mapping_pkey TO nodes_form_groups_mapping_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_form_groups_translations RENAME CONSTRAINT form_groups_translations_pkey TO nodes_form_groups_translations_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_forms RENAME CONSTRAINT forms_pkey TO nodes_forms_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_forms_doodles RENAME CONSTRAINT forms_doodles_pkey TO nodes_forms_doodles_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_forms_entries RENAME CONSTRAINT forms_entries_pkey TO nodes_forms_entries_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_forms_forms RENAME CONSTRAINT forms_forms_pkey TO nodes_forms_forms_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_forms_translations RENAME CONSTRAINT forms_translations_pkey TO nodes_forms_translations_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_news RENAME CONSTRAINT news_pkey TO nodes_news_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_news_translations RENAME CONSTRAINT news_translations_pkey TO nodes_news_translations_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_nodes RENAME CONSTRAINT nodes_pkey TO nodes_nodes_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_notification_translations RENAME CONSTRAINT notification_translations_pkey TO nodes_notification_translations_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_notifications RENAME CONSTRAINT notifications_pkey TO nodes_notifications_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_pages RENAME CONSTRAINT pages_pkey TO nodes_pages_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_pages_categories RENAME CONSTRAINT pages_categories_pkey TO nodes_pages_categories_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_pages_categories_translations RENAME CONSTRAINT pages_categories_translations_pkey TO nodes_pages_categories_translations_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_pages_links RENAME CONSTRAINT pages_links_pkey TO nodes_pages_links_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_pages_links_translations RENAME CONSTRAINT pages_links_translations_pkey TO nodes_pages_links_translations_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_pages_roles_map RENAME CONSTRAINT pages_roles_map_pkey TO nodes_pages_roles_map_pkey');
        $this->addSql('ALTER TABLE nodes.nodes_pages_translations RENAME CONSTRAINT pages_translations_pkey TO nodes_pages_translations_pkey');
        $this->addSql('ALTER TABLE prom.prom_bus RENAME CONSTRAINT bus_pkey TO prom_bus_pkey');
        $this->addSql('ALTER TABLE prom.prom_bus_code RENAME CONSTRAINT bus_code_pkey TO prom_bus_code_pkey');
        $this->addSql('ALTER TABLE prom.prom_bus_code_academic RENAME CONSTRAINT bus_code_academic_pkey TO prom_bus_code_academic_pkey');
        $this->addSql('ALTER TABLE prom.prom_bus_code_external RENAME CONSTRAINT bus_code_external_pkey TO prom_bus_code_external_pkey');
        $this->addSql('ALTER TABLE prom.prom_bus_passenger RENAME CONSTRAINT bus_passenger_pkey TO prom_bus_passenger_pkey');
        $this->addSql('ALTER TABLE public.general_migrations RENAME CONSTRAINT general_migrations_pkey TO public_general_migrations_pkey');
        $this->addSql('ALTER TABLE publications.publications_editions RENAME CONSTRAINT editions_pkey TO publications_editions_pkey');
        $this->addSql('ALTER TABLE publications.publications_editions_html RENAME CONSTRAINT editions_html_pkey TO publications_editions_html_pkey');
        $this->addSql('ALTER TABLE publications.publications_editions_pdf RENAME CONSTRAINT editions_pdf_pkey TO publications_editions_pdf_pkey');
        $this->addSql('ALTER TABLE publications.publications_publications RENAME CONSTRAINT publications_pkey TO publications_publications_pkey');
        $this->addSql('ALTER TABLE quiz.quiz_points RENAME CONSTRAINT points_pkey TO quiz_points_pkey');
        $this->addSql('ALTER TABLE quiz.quiz_quizes RENAME CONSTRAINT quizes_pkey TO quiz_quizes_pkey');
        $this->addSql('ALTER TABLE quiz.quiz_quizes_roles_map RENAME CONSTRAINT quizes_roles_map_pkey TO quiz_quizes_roles_map_pkey');
        $this->addSql('ALTER TABLE quiz.quiz_rounds RENAME CONSTRAINT rounds_pkey TO quiz_rounds_pkey');
        $this->addSql('ALTER TABLE quiz.quiz_teams RENAME CONSTRAINT teams_pkey TO quiz_teams_pkey');
        $this->addSql('ALTER TABLE shifts.shifts_responsibles RENAME CONSTRAINT responsibles_pkey TO shifts_responsibles_pkey');
        $this->addSql('ALTER TABLE shifts.shifts_shifts RENAME CONSTRAINT shifts_pkey TO shifts_shifts_pkey');
        $this->addSql('ALTER TABLE shifts.shifts_shifts_responsibles_map RENAME CONSTRAINT shifts_responsibles_map_pkey TO shifts_shifts_responsibles_map_pkey');
        $this->addSql('ALTER TABLE shifts.shifts_shifts_roles_map RENAME CONSTRAINT shifts_roles_map_pkey TO shifts_shifts_roles_map_pkey');
        $this->addSql('ALTER TABLE shifts.shifts_shifts_volunteers_map RENAME CONSTRAINT shifts_volunteers_map_pkey TO shifts_shifts_volunteers_map_pkey');
        $this->addSql('ALTER TABLE shifts.shifts_volunteers RENAME CONSTRAINT volunteers_pkey TO shifts_volunteers_pkey');
        $this->addSql('ALTER TABLE shop.shop_products RENAME CONSTRAINT products_pkey TO shop_products_pkey');
        $this->addSql('ALTER TABLE shop.shop_reservation_permissions RENAME CONSTRAINT reservation_permissions_pkey TO shop_reservation_permissions_pkey');
        $this->addSql('ALTER TABLE shop.shop_reservations RENAME CONSTRAINT reservations_pkey TO shop_reservations_pkey');
        $this->addSql('ALTER TABLE shop.shop_session_stock_entries RENAME CONSTRAINT session_stock_entries_pkey TO shop_session_stock_entries_pkey');
        $this->addSql('ALTER TABLE shop.shop_sessions RENAME CONSTRAINT sessions_pkey TO shop_sessions_pkey');
        $this->addSql('ALTER TABLE sport.sport_departments RENAME CONSTRAINT departments_pkey TO sport_departments_pkey');
        $this->addSql('ALTER TABLE sport.sport_groups RENAME CONSTRAINT groups_pkey TO sport_groups_pkey');
        $this->addSql('ALTER TABLE sport.sport_laps RENAME CONSTRAINT laps_pkey TO sport_laps_pkey');
        $this->addSql('ALTER TABLE sport.sport_runners RENAME CONSTRAINT runners_pkey TO sport_runners_pkey');
        $this->addSql('ALTER TABLE syllabus.syllabus_combination_module_group_map RENAME CONSTRAINT combination_module_group_map_pkey TO syllabus_combination_module_group_map_pkey');
        $this->addSql('ALTER TABLE syllabus.syllabus_groups RENAME CONSTRAINT groups_pkey TO syllabus_groups_pkey');
        $this->addSql('ALTER TABLE syllabus.syllabus_pocs RENAME CONSTRAINT pocs_pkey TO syllabus_pocs_pkey');
        $this->addSql('ALTER TABLE syllabus.syllabus_student_enrollment RENAME CONSTRAINT student_enrollment_pkey TO syllabus_student_enrollment_pkey');
        $this->addSql('ALTER TABLE syllabus.syllabus_studies RENAME CONSTRAINT studies_pkey TO syllabus_studies_pkey');
        $this->addSql('ALTER TABLE syllabus.syllabus_studies_group_map RENAME CONSTRAINT studies_group_map_pkey TO syllabus_studies_group_map_pkey');
        $this->addSql('ALTER TABLE syllabus.syllabus_studies_subjects_map RENAME CONSTRAINT studies_subjects_map_pkey TO syllabus_studies_subjects_map_pkey');
        $this->addSql('ALTER TABLE syllabus.syllabus_study_combinations RENAME CONSTRAINT study_combinations_pkey TO syllabus_study_combinations_pkey');
        $this->addSql('ALTER TABLE syllabus.syllabus_study_module_group RENAME CONSTRAINT study_module_group_pkey TO syllabus_study_module_group_pkey');
        $this->addSql('ALTER TABLE syllabus.syllabus_subjects RENAME CONSTRAINT subjects_pkey TO syllabus_subjects_pkey');
        $this->addSql('ALTER TABLE syllabus.syllabus_subjects_comments RENAME CONSTRAINT subjects_comments_pkey TO syllabus_subjects_comments_pkey');
        $this->addSql('ALTER TABLE syllabus.syllabus_subjects_profs_map RENAME CONSTRAINT subjects_profs_map_pkey TO syllabus_subjects_profs_map_pkey');
        $this->addSql('ALTER TABLE syllabus.syllabus_subjects_reply RENAME CONSTRAINT subjects_reply_pkey TO syllabus_subjects_reply_pkey');
        $this->addSql('ALTER TABLE tickets.tickets_events RENAME CONSTRAINT events_pkey TO tickets_events_pkey');
        $this->addSql('ALTER TABLE tickets.tickets_events_options RENAME CONSTRAINT events_options_pkey TO tickets_events_options_pkey');
        $this->addSql('ALTER TABLE tickets.tickets_guests_info RENAME CONSTRAINT guests_info_pkey TO tickets_guests_info_pkey');
        $this->addSql('ALTER TABLE tickets.tickets_tickets RENAME CONSTRAINT tickets_pkey TO tickets_tickets_pkey');
        $this->addSql('ALTER TABLE users.users_barcodes RENAME CONSTRAINT barcodes_pkey TO users_barcodes_pkey');
        $this->addSql('ALTER TABLE users.users_barcodes_ean12 RENAME CONSTRAINT barcodes_ean12_pkey TO users_barcodes_ean12_pkey');
        $this->addSql('ALTER TABLE users.users_barcodes_qr RENAME CONSTRAINT barcodes_qr_pkey TO users_barcodes_qr_pkey');
        $this->addSql('ALTER TABLE users.users_codes RENAME CONSTRAINT codes_pkey TO users_codes_pkey');
        $this->addSql('ALTER TABLE users.users_corporate_statuses RENAME CONSTRAINT corporate_statuses_pkey TO users_corporate_statuses_pkey');
        $this->addSql('ALTER TABLE users.users_credentials RENAME CONSTRAINT credentials_pkey TO users_credentials_pkey');
        $this->addSql('ALTER TABLE users.users_organization_metadata RENAME CONSTRAINT organization_metadata_pkey TO users_organization_metadata_pkey');
        $this->addSql('ALTER TABLE users.users_organization_statuses RENAME CONSTRAINT organization_statuses_pkey TO users_organization_statuses_pkey');
        $this->addSql('ALTER TABLE users.users_people RENAME CONSTRAINT people_pkey TO users_people_pkey');
        $this->addSql('ALTER TABLE users.users_people_academic RENAME CONSTRAINT people_academic_pkey TO users_people_academic_pkey');
        $this->addSql('ALTER TABLE users.users_people_corporate RENAME CONSTRAINT people_corporate_pkey TO users_people_corporate_pkey');
        $this->addSql('ALTER TABLE users.users_people_organizations_academic_year_map RENAME CONSTRAINT people_organizations_academic_year_map_pkey TO users_people_organizations_academic_year_map_pkey');
        $this->addSql('ALTER TABLE users.users_people_organizations_unit_map RENAME CONSTRAINT people_organizations_unit_map_pkey TO users_people_organizations_unit_map_pkey');
        $this->addSql('ALTER TABLE users.users_people_organizations_unit_map_academic RENAME CONSTRAINT people_organizations_unit_map_academic_pkey TO users_people_organizations_unit_map_academic_pkey');
        $this->addSql('ALTER TABLE users.users_people_organizations_unit_map_external RENAME CONSTRAINT people_organizations_unit_map_external_pkey TO users_people_organizations_unit_map_external_pkey');
        $this->addSql('ALTER TABLE users.users_people_roles_map RENAME CONSTRAINT people_roles_map_pkey TO users_people_roles_map_pkey');
        $this->addSql('ALTER TABLE users.users_people_sale_acco RENAME CONSTRAINT people_sale_acco_pkey TO users_people_sale_acco_pkey');
        $this->addSql('ALTER TABLE users.users_people_shift_insurance RENAME CONSTRAINT people_shift_insurance_pkey TO users_people_shift_insurance_pkey');
        $this->addSql('ALTER TABLE users.users_people_suppliers RENAME CONSTRAINT people_suppliers_pkey TO users_people_suppliers_pkey');
        $this->addSql('ALTER TABLE users.users_registrations RENAME CONSTRAINT registrations_pkey TO users_registrations_pkey');
        $this->addSql('ALTER TABLE users.users_sessions RENAME CONSTRAINT sessions_pkey TO users_sessions_pkey');
        $this->addSql('ALTER TABLE users.users_shibboleth_codes RENAME CONSTRAINT shibboleth_codes_pkey TO users_shibboleth_codes_pkey');
        $this->addSql('ALTER TABLE users.users_study_enrollment RENAME CONSTRAINT study_enrollment_pkey TO users_study_enrollment_pkey');
        $this->addSql('ALTER TABLE users.users_subject_enrollment RENAME CONSTRAINT subject_enrollment_pkey TO users_subject_enrollment_pkey');
        $this->addSql('ALTER TABLE users.users_university_statuses RENAME CONSTRAINT university_statuses_pkey TO users_university_statuses_pkey');
    }

    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function down(Schema $schema) : void
    {
        $this->throwIrreversibleMigrationException();
    }
}
