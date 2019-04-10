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
use Doctrine\Migrations\AbstractMigration;
use RuntimeException;

/**
 * Version 20190410204326
 */
class Version20190410204326 extends \Doctrine\Migrations\AbstractMigration
{
    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function up(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on PostgreSQL.'
        );

        $this->addSql('ALTER SEQUENCE acl.actions_id_seq RENAME TO acl_actions_id_seq');
        $this->addSql('ALTER SEQUENCE api.keys_id_seq RENAME TO api_keys_id_seq');
        $this->addSql('ALTER SEQUENCE br.collaborator_id_seq RENAME TO br_collaborator_id_seq');
        $this->addSql('ALTER SEQUENCE br.companies_events_id_seq RENAME TO br_companies_events_id_seq');
        $this->addSql('ALTER SEQUENCE br.companies_id_seq RENAME TO br_companies_id_seq');
        $this->addSql('ALTER SEQUENCE br.companies_jobs_id_seq RENAME TO br_companies_jobs_id_seq');
        $this->addSql('ALTER SEQUENCE br.companies_logos_id_seq RENAME TO br_companies_logos_id_seq');
        $this->addSql('ALTER SEQUENCE br.companies_pages_id_seq RENAME TO br_companies_pages_id_seq');
        $this->addSql('ALTER SEQUENCE br.companies_request_id_seq RENAME TO br_companies_request_id_seq');
        $this->addSql('ALTER SEQUENCE br.contract_history_id_seq RENAME TO br_contract_history_id_seq');
        $this->addSql('ALTER SEQUENCE br.contracts_entries_id_seq RENAME TO br_contracts_entries_id_seq');
        $this->addSql('ALTER SEQUENCE br.contracts_id_seq RENAME TO br_contracts_id_seq');
        $this->addSql('ALTER SEQUENCE br.cv_entries_id_seq RENAME TO br_cv_entries_id_seq');
        $this->addSql('ALTER SEQUENCE br.cv_experiences_id_seq RENAME TO br_cv_experiences_id_seq');
        $this->addSql('ALTER SEQUENCE br.cv_languages_id_seq RENAME TO br_cv_languages_id_seq');
        $this->addSql('ALTER SEQUENCE br.event_company_map_id_seq RENAME TO br_event_company_map_id_seq');
        $this->addSql('ALTER SEQUENCE br.events_id_seq RENAME TO br_events_id_seq');
        $this->addSql('ALTER SEQUENCE br.invoice_history_id_seq RENAME TO br_invoice_history_id_seq');
        $this->addSql('ALTER SEQUENCE br.invoices_entries_id_seq RENAME TO br_invoices_entries_id_seq');
        $this->addSql('ALTER SEQUENCE br.invoices_id_seq RENAME TO br_invoices_id_seq');
        $this->addSql('ALTER SEQUENCE br.orders_entries_id_seq RENAME TO br_orders_entries_id_seq');
        $this->addSql('ALTER SEQUENCE br.orders_id_seq RENAME TO br_orders_id_seq');
        $this->addSql('ALTER SEQUENCE br.products_id_seq RENAME TO br_products_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.articles_history_id_seq RENAME TO cudi_articles_history_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.articles_id_seq RENAME TO cudi_articles_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.articles_notifications_subscriptions_id_seq RENAME TO cudi_articles_notifications_subscriptions_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.articles_options_bindings_id_seq RENAME TO cudi_articles_options_bindings_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.articles_options_colors_id_seq RENAME TO cudi_articles_options_colors_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.articles_subjects_map_id_seq RENAME TO cudi_articles_subjects_map_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.comments_articles_map_id_seq RENAME TO cudi_comments_articles_map_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.comments_comments_id_seq RENAME TO cudi_comments_comments_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.files_articles_map_id_seq RENAME TO cudi_files_articles_map_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.files_files_id_seq RENAME TO cudi_files_files_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.isic_card_id_seq RENAME TO cudi_isic_card_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.log_id_seq RENAME TO cudi_log_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.prof_actions_id_seq RENAME TO cudi_prof_actions_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.sales_articles_barcodes_id_seq RENAME TO cudi_sales_articles_barcodes_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.sales_articles_discounts_discounts_id_seq RENAME TO cudi_sales_articles_discounts_discounts_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.sales_articles_discounts_templates_id_seq RENAME TO cudi_sales_articles_discounts_templates_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.sales_articles_id_seq RENAME TO cudi_sales_articles_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.sales_articles_restrictions_id_seq RENAME TO cudi_sales_articles_restrictions_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.sales_bookings_id_seq RENAME TO cudi_sales_bookings_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.sales_history_id_seq RENAME TO cudi_sales_history_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.sales_pay_desks_id_seq RENAME TO cudi_sales_pay_desks_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.sales_queue_items_id_seq RENAME TO cudi_sales_queue_items_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.sales_return_items_id_seq RENAME TO cudi_sales_return_items_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.sales_sale_items_id_seq RENAME TO cudi_sales_sale_items_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.sales_session_openinghours_id_seq RENAME TO cudi_sales_session_openinghours_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.sales_session_openinghours_translations_id_seq RENAME TO cudi_sales_session_openinghours_translations_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.sales_session_restriction_id_seq RENAME TO cudi_sales_session_restriction_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.sales_sessions_id_seq RENAME TO cudi_sales_sessions_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.stock_deliveries_id_seq RENAME TO cudi_stock_deliveries_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.stock_orders_id_seq RENAME TO cudi_stock_orders_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.stock_orders_items_id_seq RENAME TO cudi_stock_orders_items_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.stock_orders_virtual_id_seq RENAME TO cudi_stock_orders_virtual_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.stock_periods_id_seq RENAME TO cudi_stock_periods_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.stock_periods_values_deltas_id_seq RENAME TO cudi_stock_periods_values_deltas_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.stock_periods_values_starts_id_seq RENAME TO cudi_stock_periods_values_starts_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.stock_retours_id_seq RENAME TO cudi_stock_retours_id_seq');
        $this->addSql('ALTER SEQUENCE cudi.suppliers_id_seq RENAME TO cudi_suppliers_id_seq');
        $this->addSql('ALTER SEQUENCE forms.fields_id_seq RENAME TO forms_fields_id_seq');
        $this->addSql('ALTER SEQUENCE forms.fields_options_translations_id_seq RENAME TO forms_fields_options_translations_id_seq');
        $this->addSql('ALTER SEQUENCE forms.fields_timeslots_translations_id_seq RENAME TO forms_fields_timeslots_translations_id_seq');
        $this->addSql('ALTER SEQUENCE forms.fields_translations_id_seq RENAME TO forms_fields_translations_id_seq');
        $this->addSql('ALTER SEQUENCE forms.guests_info_id_seq RENAME TO forms_guests_info_id_seq');
        $this->addSql('ALTER SEQUENCE forms.mails_id_seq RENAME TO forms_mails_id_seq');
        $this->addSql('ALTER SEQUENCE forms.mails_translations_id_seq RENAME TO forms_mails_translations_id_seq');
        $this->addSql('ALTER SEQUENCE forms.viewers_id_seq RENAME TO forms_viewers_id_seq');
        $this->addSql('ALTER SEQUENCE gallery.albums_id_seq RENAME TO gallery_albums_id_seq');
        $this->addSql('ALTER SEQUENCE gallery.photos_id_seq RENAME TO gallery_photos_id_seq');
        $this->addSql('ALTER SEQUENCE gallery.translations_id_seq RENAME TO gallery_translations_id_seq');
        $this->addSql('ALTER SEQUENCE general.academic_years_id_seq RENAME TO general_academic_years_id_seq');
        $this->addSql('ALTER SEQUENCE general.address_cities_id_seq RENAME TO general_address_cities_id_seq');
        $this->addSql('ALTER SEQUENCE general.address_streets_id_seq RENAME TO general_address_streets_id_seq');
        $this->addSql('ALTER SEQUENCE general.addresses_id_seq RENAME TO general_addresses_id_seq');
        $this->addSql('ALTER SEQUENCE general.bank_bank_devices_amounts_id_seq RENAME TO general_bank_bank_devices_amounts_id_seq');
        $this->addSql('ALTER SEQUENCE general.bank_bank_devices_id_seq RENAME TO general_bank_bank_devices_id_seq');
        $this->addSql('ALTER SEQUENCE general.bank_cash_registers_id_seq RENAME TO general_bank_cash_registers_id_seq');
        $this->addSql('ALTER SEQUENCE general.bank_money_units_amounts_id_seq RENAME TO general_bank_money_units_amounts_id_seq');
        $this->addSql('ALTER SEQUENCE general.bank_money_units_id_seq RENAME TO general_bank_money_units_id_seq');
        $this->addSql('ALTER SEQUENCE general.languages_id_seq RENAME TO general_languages_id_seq');
        $this->addSql('ALTER SEQUENCE general.locations_id_seq RENAME TO general_locations_id_seq');
        $this->addSql('ALTER SEQUENCE general.organizations_id_seq RENAME TO general_organizations_id_seq');
        $this->addSql('ALTER SEQUENCE general.organizations_units_id_seq RENAME TO general_organizations_units_id_seq');
        $this->addSql('ALTER SEQUENCE general.promotions_id_seq RENAME TO general_promotions_id_seq');
        $this->addSql('ALTER SEQUENCE general.visits_id_seq RENAME TO general_visits_id_seq');
        $this->addSql('ALTER SEQUENCE logistics.lease_items_id_seq RENAME TO logistics_lease_items_id_seq');
        $this->addSql('ALTER SEQUENCE logistics.lease_lease_id_seq RENAME TO logistics_lease_lease_id_seq');
        $this->addSql('ALTER SEQUENCE logistics.reservations_id_seq RENAME TO logistics_reservations_id_seq');
        $this->addSql('ALTER SEQUENCE mail.aliases_id_seq RENAME TO mail_aliases_id_seq');
        $this->addSql('ALTER SEQUENCE mail.lists_admin_roles_id_seq RENAME TO mail_lists_admin_roles_id_seq');
        $this->addSql('ALTER SEQUENCE mail.lists_admins_id_seq RENAME TO mail_lists_admins_id_seq');
        $this->addSql('ALTER SEQUENCE mail.lists_entries_id_seq RENAME TO mail_lists_entries_id_seq');
        $this->addSql('ALTER SEQUENCE mail.lists_id_seq RENAME TO mail_lists_id_seq');
        $this->addSql('ALTER SEQUENCE nodes.events_translations_id_seq RENAME TO nodes_events_translations_id_seq');
        $this->addSql('ALTER SEQUENCE nodes.form_groups_mapping_id_seq RENAME TO nodes_form_groups_mapping_id_seq');
        $this->addSql('ALTER SEQUENCE nodes.form_groups_translations_id_seq RENAME TO nodes_form_groups_translations_id_seq');
        $this->addSql('ALTER SEQUENCE nodes.forms_entries_id_seq RENAME TO nodes_forms_entries_id_seq');
        $this->addSql('ALTER SEQUENCE nodes.forms_translations_id_seq RENAME TO nodes_forms_translations_id_seq');
        $this->addSql('ALTER SEQUENCE nodes.news_translations_id_seq RENAME TO nodes_news_translations_id_seq');
        $this->addSql('ALTER SEQUENCE nodes.nodes_id_seq RENAME TO nodes_nodes_id_seq');
        $this->addSql('ALTER SEQUENCE nodes.notification_translations_id_seq RENAME TO nodes_notification_translations_id_seq');
        $this->addSql('ALTER SEQUENCE nodes.pages_categories_id_seq RENAME TO nodes_pages_categories_id_seq');
        $this->addSql('ALTER SEQUENCE nodes.pages_categories_translations_id_seq RENAME TO nodes_pages_categories_translations_id_seq');
        $this->addSql('ALTER SEQUENCE nodes.pages_links_id_seq RENAME TO nodes_pages_links_id_seq');
        $this->addSql('ALTER SEQUENCE nodes.pages_links_translations_id_seq RENAME TO nodes_pages_links_translations_id_seq');
        $this->addSql('ALTER SEQUENCE nodes.pages_translations_id_seq RENAME TO nodes_pages_translations_id_seq');
        $this->addSql('ALTER SEQUENCE prom.bus_code_id_seq RENAME TO prom_bus_code_id_seq');
        $this->addSql('ALTER SEQUENCE prom.bus_id_seq RENAME TO prom_bus_id_seq');
        $this->addSql('ALTER SEQUENCE prom.bus_passenger_id_seq RENAME TO prom_bus_passenger_id_seq');
        $this->addSql('ALTER SEQUENCE publications.editions_id_seq RENAME TO publications_editions_id_seq');
        $this->addSql('ALTER SEQUENCE publications.publications_id_seq RENAME TO publications_publications_id_seq');
        $this->addSql('ALTER SEQUENCE quiz.points_id_seq RENAME TO quiz_points_id_seq');
        $this->addSql('ALTER SEQUENCE quiz.quizes_id_seq RENAME TO quiz_quizes_id_seq');
        $this->addSql('ALTER SEQUENCE quiz.rounds_id_seq RENAME TO quiz_rounds_id_seq');
        $this->addSql('ALTER SEQUENCE quiz.teams_id_seq RENAME TO quiz_teams_id_seq');
        $this->addSql('ALTER SEQUENCE shifts.responsibles_id_seq RENAME TO shifts_responsibles_id_seq');
        $this->addSql('ALTER SEQUENCE shifts.shifts_id_seq RENAME TO shifts_shifts_id_seq');
        $this->addSql('ALTER SEQUENCE shifts.volunteers_id_seq RENAME TO shifts_volunteers_id_seq');
        $this->addSql('ALTER SEQUENCE shop.products_id_seq RENAME TO shop_products_id_seq');
        $this->addSql('ALTER SEQUENCE shop.reservations_id_seq RENAME TO shop_reservations_id_seq');
        $this->addSql('ALTER SEQUENCE shop.sessions_id_seq RENAME TO shop_sessions_id_seq');
        $this->addSql('ALTER SEQUENCE sport.departments_id_seq RENAME TO sport_departments_id_seq');
        $this->addSql('ALTER SEQUENCE sport.groups_id_seq RENAME TO sport_groups_id_seq');
        $this->addSql('ALTER SEQUENCE sport.laps_id_seq RENAME TO sport_laps_id_seq');
        $this->addSql('ALTER SEQUENCE sport.runners_id_seq RENAME TO sport_runners_id_seq');
        $this->addSql('ALTER SEQUENCE syllabus.groups_id_seq RENAME TO syllabus_groups_id_seq');
        $this->addSql('ALTER SEQUENCE syllabus.pocs_id_seq RENAME TO syllabus_pocs_id_seq');
        $this->addSql('ALTER SEQUENCE syllabus.student_enrollment_id_seq RENAME TO syllabus_student_enrollment_id_seq');
        $this->addSql('ALTER SEQUENCE syllabus.studies_group_map_id_seq RENAME TO syllabus_studies_group_map_id_seq');
        $this->addSql('ALTER SEQUENCE syllabus.studies_id_seq RENAME TO syllabus_studies_id_seq');
        $this->addSql('ALTER SEQUENCE syllabus.studies_subjects_map_id_seq RENAME TO syllabus_studies_subjects_map_id_seq');
        $this->addSql('ALTER SEQUENCE syllabus.study_combinations_id_seq RENAME TO syllabus_study_combinations_id_seq');
        $this->addSql('ALTER SEQUENCE syllabus.study_module_group_id_seq RENAME TO syllabus_study_module_group_id_seq');
        $this->addSql('ALTER SEQUENCE syllabus.subjects_comments_id_seq RENAME TO syllabus_subjects_comments_id_seq');
        $this->addSql('ALTER SEQUENCE syllabus.subjects_id_seq RENAME TO syllabus_subjects_id_seq');
        $this->addSql('ALTER SEQUENCE syllabus.subjects_profs_map_id_seq RENAME TO syllabus_subjects_profs_map_id_seq');
        $this->addSql('ALTER SEQUENCE syllabus.subjects_reply_id_seq RENAME TO syllabus_subjects_reply_id_seq');
        $this->addSql('ALTER SEQUENCE tickets.events_id_seq RENAME TO tickets_events_id_seq');
        $this->addSql('ALTER SEQUENCE tickets.events_options_id_seq RENAME TO tickets_events_options_id_seq');
        $this->addSql('ALTER SEQUENCE tickets.guests_info_id_seq RENAME TO tickets_guests_info_id_seq');
        $this->addSql('ALTER SEQUENCE tickets.tickets_id_seq RENAME TO tickets_tickets_id_seq');
        $this->addSql('ALTER SEQUENCE users.barcodes_id_seq RENAME TO users_barcodes_id_seq');
        $this->addSql('ALTER SEQUENCE users.codes_id_seq RENAME TO users_codes_id_seq');
        $this->addSql('ALTER SEQUENCE users.corporate_statuses_id_seq RENAME TO users_corporate_statuses_id_seq');
        $this->addSql('ALTER SEQUENCE users.credentials_id_seq RENAME TO users_credentials_id_seq');
        $this->addSql('ALTER SEQUENCE users.organization_metadata_id_seq RENAME TO users_organization_metadata_id_seq');
        $this->addSql('ALTER SEQUENCE users.organization_statuses_id_seq RENAME TO users_organization_statuses_id_seq');
        $this->addSql('ALTER SEQUENCE users.people_id_seq RENAME TO users_people_id_seq');
        $this->addSql('ALTER SEQUENCE users.people_organizations_academic_year_map_id_seq RENAME TO users_people_organizations_academic_year_map_id_seq');
        $this->addSql('ALTER SEQUENCE users.people_organizations_unit_map_id_seq RENAME TO users_people_organizations_unit_map_id_seq');
        $this->addSql('ALTER SEQUENCE users.people_sale_acco_id_seq RENAME TO users_people_sale_acco_id_seq');
        $this->addSql('ALTER SEQUENCE users.people_shift_insurance_id_seq RENAME TO users_people_shift_insurance_id_seq');
        $this->addSql('ALTER SEQUENCE users.registrations_id_seq RENAME TO users_registrations_id_seq');
        $this->addSql('ALTER SEQUENCE users.study_enrollment_id_seq RENAME TO users_study_enrollment_id_seq');
        $this->addSql('ALTER SEQUENCE users.subject_enrollment_id_seq RENAME TO users_subject_enrollment_id_seq');
        $this->addSql('ALTER SEQUENCE users.university_statuses_id_seq RENAME TO users_university_statuses_id_seq');

        $this->addSql('ALTER TABLE acl.actions RENAME TO acl_actions');
        $this->addSql('ALTER TABLE acl.resources RENAME TO acl_resources');
        $this->addSql('ALTER TABLE acl.roles RENAME TO acl_roles');
        $this->addSql('ALTER TABLE acl.roles_actions_map RENAME TO acl_roles_actions_map');
        $this->addSql('ALTER TABLE acl.roles_inheritance_map RENAME TO acl_roles_inheritance_map');
        $this->addSql('ALTER TABLE api.keys RENAME TO api_keys');
        $this->addSql('ALTER TABLE api.keys_roles_map RENAME TO api_keys_roles_map');
        $this->addSql('ALTER TABLE br.collaborator RENAME TO br_collaborator');
        $this->addSql('ALTER TABLE br.companies RENAME TO br_companies');
        $this->addSql('ALTER TABLE br.companies_cvbooks RENAME TO br_companies_cvbooks');
        $this->addSql('ALTER TABLE br.companies_events RENAME TO br_companies_events');
        $this->addSql('ALTER TABLE br.companies_jobs RENAME TO br_companies_jobs');
        $this->addSql('ALTER TABLE br.companies_logos RENAME TO br_companies_logos');
        $this->addSql('ALTER TABLE br.companies_pages RENAME TO br_companies_pages');
        $this->addSql('ALTER TABLE br.companies_request RENAME TO br_companies_request');
        $this->addSql('ALTER TABLE br.companies_request_internship RENAME TO br_companies_request_internship');
        $this->addSql('ALTER TABLE br.companies_request_vacancy RENAME TO br_companies_request_vacancy');
        $this->addSql('ALTER TABLE br.contract_history RENAME TO br_contract_history');
        $this->addSql('ALTER TABLE br.contracts RENAME TO br_contracts');
        $this->addSql('ALTER TABLE br.contracts_entries RENAME TO br_contracts_entries');
        $this->addSql('ALTER TABLE br.cv_entries RENAME TO br_cv_entries');
        $this->addSql('ALTER TABLE br.cv_experiences RENAME TO br_cv_experiences');
        $this->addSql('ALTER TABLE br.cv_languages RENAME TO br_cv_languages');
        $this->addSql('ALTER TABLE br.event_company_map RENAME TO br_event_company_map');
        $this->addSql('ALTER TABLE br.events RENAME TO br_events');
        $this->addSql('ALTER TABLE br.invoice_history RENAME TO br_invoice_history');
        $this->addSql('ALTER TABLE br.invoices RENAME TO br_invoices');
        $this->addSql('ALTER TABLE br.invoices_contract RENAME TO br_invoices_contract');
        $this->addSql('ALTER TABLE br.invoices_entries RENAME TO br_invoices_entries');
        $this->addSql('ALTER TABLE br.invoices_manual RENAME TO br_invoices_manual');
        $this->addSql('ALTER TABLE br.orders RENAME TO br_orders');
        $this->addSql('ALTER TABLE br.orders_entries RENAME TO br_orders_entries');
        $this->addSql('ALTER TABLE br.page_years RENAME TO br_page_years');
        $this->addSql('ALTER TABLE br.products RENAME TO br_products');
        $this->addSql('ALTER TABLE cudi.articles RENAME TO cudi_articles');
        $this->addSql('ALTER TABLE cudi.articles_external RENAME TO cudi_articles_external');
        $this->addSql('ALTER TABLE cudi.articles_history RENAME TO cudi_articles_history');
        $this->addSql('ALTER TABLE cudi.articles_internal RENAME TO cudi_articles_internal');
        $this->addSql('ALTER TABLE cudi.articles_notifications_subscriptions RENAME TO cudi_articles_notifications_subscriptions');
        $this->addSql('ALTER TABLE cudi.articles_options_bindings RENAME TO cudi_articles_options_bindings');
        $this->addSql('ALTER TABLE cudi.articles_options_colors RENAME TO cudi_articles_options_colors');
        $this->addSql('ALTER TABLE cudi.articles_subjects_map RENAME TO cudi_articles_subjects_map');
        $this->addSql('ALTER TABLE cudi.comments_articles_map RENAME TO cudi_comments_articles_map');
        $this->addSql('ALTER TABLE cudi.comments_comments RENAME TO cudi_comments_comments');
        $this->addSql('ALTER TABLE cudi.files_articles_map RENAME TO cudi_files_articles_map');
        $this->addSql('ALTER TABLE cudi.files_files RENAME TO cudi_files_files');
        $this->addSql('ALTER TABLE cudi.isic_card RENAME TO cudi_isic_card');
        $this->addSql('ALTER TABLE cudi.log RENAME TO cudi_log');
        $this->addSql('ALTER TABLE cudi.log_articles_sales_bookable RENAME TO cudi_log_articles_sales_bookable');
        $this->addSql('ALTER TABLE cudi.log_articles_sales_unbookable RENAME TO cudi_log_articles_sales_unbookable');
        $this->addSql('ALTER TABLE cudi.log_articles_subject_map_add RENAME TO cudi_log_articles_subject_map_add');
        $this->addSql('ALTER TABLE cudi.log_articles_subject_map_remove RENAME TO cudi_log_articles_subject_map_remove');
        $this->addSql('ALTER TABLE cudi.log_sales_assignments RENAME TO cudi_log_sales_assignments');
        $this->addSql('ALTER TABLE cudi.log_sales_cancellations RENAME TO cudi_log_sales_cancellations');
        $this->addSql('ALTER TABLE cudi.log_sales_prof_version RENAME TO cudi_log_sales_prof_version');
        $this->addSql('ALTER TABLE cudi.prof_actions RENAME TO cudi_prof_actions');
        $this->addSql('ALTER TABLE cudi.sales_articles RENAME TO cudi_sales_articles');
        $this->addSql('ALTER TABLE cudi.sales_articles_barcodes RENAME TO cudi_sales_articles_barcodes');
        $this->addSql('ALTER TABLE cudi.sales_articles_discounts_discounts RENAME TO cudi_sales_articles_discounts_discounts');
        $this->addSql('ALTER TABLE cudi.sales_articles_discounts_templates RENAME TO cudi_sales_articles_discounts_templates');
        $this->addSql('ALTER TABLE cudi.sales_articles_restrictions RENAME TO cudi_sales_articles_restrictions');
        $this->addSql('ALTER TABLE cudi.sales_articles_restrictions_amount RENAME TO cudi_sales_articles_restrictions_amount');
        $this->addSql('ALTER TABLE cudi.sales_articles_restrictions_available RENAME TO cudi_sales_articles_restrictions_available');
        $this->addSql('ALTER TABLE cudi.sales_articles_restrictions_member RENAME TO cudi_sales_articles_restrictions_member');
        $this->addSql('ALTER TABLE cudi.sales_articles_restrictions_study RENAME TO cudi_sales_articles_restrictions_study');
        $this->addSql('ALTER TABLE cudi.sales_articles_restrictions_study_map RENAME TO cudi_sales_articles_restrictions_study_map');
        $this->addSql('ALTER TABLE cudi.sales_bookings RENAME TO cudi_sales_bookings');
        $this->addSql('ALTER TABLE cudi.sales_history RENAME TO cudi_sales_history');
        $this->addSql('ALTER TABLE cudi.sales_pay_desks RENAME TO cudi_sales_pay_desks');
        $this->addSql('ALTER TABLE cudi.sales_queue_items RENAME TO cudi_sales_queue_items');
        $this->addSql('ALTER TABLE cudi.sales_return_items RENAME TO cudi_sales_return_items');
        $this->addSql('ALTER TABLE cudi.sales_sale_items RENAME TO cudi_sales_sale_items');
        $this->addSql('ALTER TABLE cudi.sales_session_openinghours RENAME TO cudi_sales_session_openinghours');
        $this->addSql('ALTER TABLE cudi.sales_session_openinghours_translations RENAME TO cudi_sales_session_openinghours_translations');
        $this->addSql('ALTER TABLE cudi.sales_session_restriction RENAME TO cudi_sales_session_restriction');
        $this->addSql('ALTER TABLE cudi.sales_session_restriction_name RENAME TO cudi_sales_session_restriction_name');
        $this->addSql('ALTER TABLE cudi.sales_session_restriction_study RENAME TO cudi_sales_session_restriction_study');
        $this->addSql('ALTER TABLE cudi.sales_session_restriction_year RENAME TO cudi_sales_session_restriction_year');
        $this->addSql('ALTER TABLE cudi.sales_session_restrictions_study_map RENAME TO cudi_sales_session_restrictions_study_map');
        $this->addSql('ALTER TABLE cudi.sales_sessions RENAME TO cudi_sales_sessions');
        $this->addSql('ALTER TABLE cudi.stock_deliveries RENAME TO cudi_stock_deliveries');
        $this->addSql('ALTER TABLE cudi.stock_orders RENAME TO cudi_stock_orders');
        $this->addSql('ALTER TABLE cudi.stock_orders_items RENAME TO cudi_stock_orders_items');
        $this->addSql('ALTER TABLE cudi.stock_orders_virtual RENAME TO cudi_stock_orders_virtual');
        $this->addSql('ALTER TABLE cudi.stock_periods RENAME TO cudi_stock_periods');
        $this->addSql('ALTER TABLE cudi.stock_periods_values_deltas RENAME TO cudi_stock_periods_values_deltas');
        $this->addSql('ALTER TABLE cudi.stock_periods_values_starts RENAME TO cudi_stock_periods_values_starts');
        $this->addSql('ALTER TABLE cudi.stock_retours RENAME TO cudi_stock_retours');
        $this->addSql('ALTER TABLE cudi.suppliers RENAME TO cudi_suppliers');
        $this->addSql('ALTER TABLE forms.entries RENAME TO forms_entries');
        $this->addSql('ALTER TABLE forms.fields RENAME TO forms_fields');
        $this->addSql('ALTER TABLE forms.fields_checkboxes RENAME TO forms_fields_checkboxes');
        $this->addSql('ALTER TABLE forms.fields_dropdowns RENAME TO forms_fields_dropdowns');
        $this->addSql('ALTER TABLE forms.fields_files RENAME TO forms_fields_files');
        $this->addSql('ALTER TABLE forms.fields_options RENAME TO forms_fields_options');
        $this->addSql('ALTER TABLE forms.fields_options_translations RENAME TO forms_fields_options_translations');
        $this->addSql('ALTER TABLE forms.fields_texts RENAME TO forms_fields_texts');
        $this->addSql('ALTER TABLE forms.fields_timeslot RENAME TO forms_fields_timeslot');
        $this->addSql('ALTER TABLE forms.fields_timeslots_translations RENAME TO forms_fields_timeslots_translations');
        $this->addSql('ALTER TABLE forms.fields_translations RENAME TO forms_fields_translations');
        $this->addSql('ALTER TABLE forms.guests_info RENAME TO forms_guests_info');
        $this->addSql('ALTER TABLE forms.mails RENAME TO forms_mails');
        $this->addSql('ALTER TABLE forms.mails_translations RENAME TO forms_mails_translations');
        $this->addSql('ALTER TABLE forms.viewers RENAME TO forms_viewers');
        $this->addSql('ALTER TABLE gallery.albums RENAME TO gallery_albums');
        $this->addSql('ALTER TABLE gallery.photos RENAME TO gallery_photos');
        $this->addSql('ALTER TABLE gallery.translations RENAME TO gallery_translations');
        $this->addSql('ALTER TABLE general.academic_years RENAME TO general_academic_years');
        $this->addSql('ALTER TABLE general.address_cities RENAME TO general_address_cities');
        $this->addSql('ALTER TABLE general.address_streets RENAME TO general_address_streets');
        $this->addSql('ALTER TABLE general.addresses RENAME TO general_addresses');
        $this->addSql('ALTER TABLE general.bank_bank_devices RENAME TO general_bank_bank_devices');
        $this->addSql('ALTER TABLE general.bank_bank_devices_amounts RENAME TO general_bank_bank_devices_amounts');
        $this->addSql('ALTER TABLE general.bank_cash_registers RENAME TO general_bank_cash_registers');
        $this->addSql('ALTER TABLE general.bank_money_units RENAME TO general_bank_money_units');
        $this->addSql('ALTER TABLE general.bank_money_units_amounts RENAME TO general_bank_money_units_amounts');
        $this->addSql('ALTER TABLE general.config RENAME TO general_config');
        $this->addSql('ALTER TABLE general.languages RENAME TO general_languages');
        $this->addSql('ALTER TABLE general.locations RENAME TO general_locations');
        $this->addSql('ALTER TABLE general.organizations RENAME TO general_organizations');
        $this->addSql('ALTER TABLE general.organizations_units RENAME TO general_organizations_units');
        $this->addSql('ALTER TABLE general.organizations_units_coordinator_roles_map RENAME TO general_organizations_units_coordinator_roles_map');
        $this->addSql('ALTER TABLE general.organizations_units_roles_map RENAME TO general_organizations_units_roles_map');
        $this->addSql('ALTER TABLE general.promotions RENAME TO general_promotions');
        $this->addSql('ALTER TABLE general.promotions_academic RENAME TO general_promotions_academic');
        $this->addSql('ALTER TABLE general.promotions_external RENAME TO general_promotions_external');
        $this->addSql('ALTER TABLE general.visits RENAME TO general_visits');
        $this->addSql('ALTER TABLE logistics.drivers RENAME TO logistics_drivers');
        $this->addSql('ALTER TABLE logistics.drivers_years RENAME TO logistics_drivers_years');
        $this->addSql('ALTER TABLE logistics.lease_items RENAME TO logistics_lease_items');
        $this->addSql('ALTER TABLE logistics.lease_lease RENAME TO logistics_lease_lease');
        $this->addSql('ALTER TABLE logistics.reservations RENAME TO logistics_reservations');
        $this->addSql('ALTER TABLE logistics.reservations_piano RENAME TO logistics_reservations_piano');
        $this->addSql('ALTER TABLE logistics.reservations_van RENAME TO logistics_reservations_van');
        $this->addSql('ALTER TABLE logistics.resources RENAME TO logistics_resources');
        $this->addSql('ALTER TABLE mail.aliases RENAME TO mail_aliases');
        $this->addSql('ALTER TABLE mail.aliases_academic RENAME TO mail_aliases_academic');
        $this->addSql('ALTER TABLE mail.aliases_external RENAME TO mail_aliases_external');
        $this->addSql('ALTER TABLE mail.lists RENAME TO mail_lists');
        $this->addSql('ALTER TABLE mail.lists_admin_roles RENAME TO mail_lists_admin_roles');
        $this->addSql('ALTER TABLE mail.lists_admins RENAME TO mail_lists_admins');
        $this->addSql('ALTER TABLE mail.lists_entries RENAME TO mail_lists_entries');
        $this->addSql('ALTER TABLE mail.lists_entries_lists RENAME TO mail_lists_entries_lists');
        $this->addSql('ALTER TABLE mail.lists_entries_people_academic RENAME TO mail_lists_entries_people_academic');
        $this->addSql('ALTER TABLE mail.lists_entries_people_external RENAME TO mail_lists_entries_people_external');
        $this->addSql('ALTER TABLE mail.lists_named RENAME TO mail_lists_named');
        $this->addSql('ALTER TABLE nodes.banners RENAME TO nodes_banners');
        $this->addSql('ALTER TABLE nodes.events RENAME TO nodes_events');
        $this->addSql('ALTER TABLE nodes.events_translations RENAME TO nodes_events_translations');
        $this->addSql('ALTER TABLE nodes.form_groups RENAME TO nodes_form_groups');
        $this->addSql('ALTER TABLE nodes.form_groups_mapping RENAME TO nodes_form_groups_mapping');
        $this->addSql('ALTER TABLE nodes.form_groups_translations RENAME TO nodes_form_groups_translations');
        $this->addSql('ALTER TABLE nodes.forms RENAME TO nodes_forms');
        $this->addSql('ALTER TABLE nodes.forms_doodles RENAME TO nodes_forms_doodles');
        $this->addSql('ALTER TABLE nodes.forms_entries RENAME TO nodes_forms_entries');
        $this->addSql('ALTER TABLE nodes.forms_forms RENAME TO nodes_forms_forms');
        $this->addSql('ALTER TABLE nodes.forms_translations RENAME TO nodes_forms_translations');
        $this->addSql('ALTER TABLE nodes.news RENAME TO nodes_news');
        $this->addSql('ALTER TABLE nodes.news_translations RENAME TO nodes_news_translations');
        $this->addSql('ALTER TABLE nodes.nodes RENAME TO nodes_nodes');
        $this->addSql('ALTER TABLE nodes.notification_translations RENAME TO nodes_notification_translations');
        $this->addSql('ALTER TABLE nodes.notifications RENAME TO nodes_notifications');
        $this->addSql('ALTER TABLE nodes.pages RENAME TO nodes_pages');
        $this->addSql('ALTER TABLE nodes.pages_categories RENAME TO nodes_pages_categories');
        $this->addSql('ALTER TABLE nodes.pages_categories_translations RENAME TO nodes_pages_categories_translations');
        $this->addSql('ALTER TABLE nodes.pages_links RENAME TO nodes_pages_links');
        $this->addSql('ALTER TABLE nodes.pages_links_translations RENAME TO nodes_pages_links_translations');
        $this->addSql('ALTER TABLE nodes.pages_roles_map RENAME TO nodes_pages_roles_map');
        $this->addSql('ALTER TABLE nodes.pages_translations RENAME TO nodes_pages_translations');
        $this->addSql('ALTER TABLE prom.bus RENAME TO prom_bus');
        $this->addSql('ALTER TABLE prom.bus_code RENAME TO prom_bus_code');
        $this->addSql('ALTER TABLE prom.bus_code_academic RENAME TO prom_bus_code_academic');
        $this->addSql('ALTER TABLE prom.bus_code_external RENAME TO prom_bus_code_external');
        $this->addSql('ALTER TABLE prom.bus_passenger RENAME TO prom_bus_passenger');
        $this->addSql('ALTER TABLE publications.editions RENAME TO publications_editions');
        $this->addSql('ALTER TABLE publications.editions_html RENAME TO publications_editions_html');
        $this->addSql('ALTER TABLE publications.editions_pdf RENAME TO publications_editions_pdf');
        $this->addSql('ALTER TABLE publications.publications RENAME TO publications_publications');
        $this->addSql('ALTER TABLE quiz.points RENAME TO quiz_points');
        $this->addSql('ALTER TABLE quiz.quizes RENAME TO quiz_quizes');
        $this->addSql('ALTER TABLE quiz.quizes_roles_map RENAME TO quiz_quizes_roles_map');
        $this->addSql('ALTER TABLE quiz.rounds RENAME TO quiz_rounds');
        $this->addSql('ALTER TABLE quiz.teams RENAME TO quiz_teams');
        $this->addSql('ALTER TABLE shifts.responsibles RENAME TO shifts_responsibles');
        $this->addSql('ALTER TABLE shifts.shifts RENAME TO shifts_shifts');
        $this->addSql('ALTER TABLE shifts.shifts_responsibles_map RENAME TO shifts_shifts_responsibles_map');
        $this->addSql('ALTER TABLE shifts.shifts_roles_map RENAME TO shifts_shifts_roles_map');
        $this->addSql('ALTER TABLE shifts.shifts_volunteers_map RENAME TO shifts_shifts_volunteers_map');
        $this->addSql('ALTER TABLE shifts.volunteers RENAME TO shifts_volunteers');
        $this->addSql('ALTER TABLE shop.products RENAME TO shop_products');
        $this->addSql('ALTER TABLE shop.reservation_permissions RENAME TO shop_reservation_permissions');
        $this->addSql('ALTER TABLE shop.reservations RENAME TO shop_reservations');
        $this->addSql('ALTER TABLE shop.session_stock_entries RENAME TO shop_session_stock_entries');
        $this->addSql('ALTER TABLE shop.sessions RENAME TO shop_sessions');
        $this->addSql('ALTER TABLE sport.departments RENAME TO sport_departments');
        $this->addSql('ALTER TABLE sport.groups RENAME TO sport_groups');
        $this->addSql('ALTER TABLE sport.laps RENAME TO sport_laps');
        $this->addSql('ALTER TABLE sport.runners RENAME TO sport_runners');
        $this->addSql('ALTER TABLE syllabus.combination_module_group_map RENAME TO syllabus_combination_module_group_map');
        $this->addSql('ALTER TABLE syllabus.groups RENAME TO syllabus_groups');
        $this->addSql('ALTER TABLE syllabus.pocs RENAME TO syllabus_pocs');
        $this->addSql('ALTER TABLE syllabus.student_enrollment RENAME TO syllabus_student_enrollment');
        $this->addSql('ALTER TABLE syllabus.studies RENAME TO syllabus_studies');
        $this->addSql('ALTER TABLE syllabus.studies_group_map RENAME TO syllabus_studies_group_map');
        $this->addSql('ALTER TABLE syllabus.studies_subjects_map RENAME TO syllabus_studies_subjects_map');
        $this->addSql('ALTER TABLE syllabus.study_combinations RENAME TO syllabus_study_combinations');
        $this->addSql('ALTER TABLE syllabus.study_module_group RENAME TO syllabus_study_module_group');
        $this->addSql('ALTER TABLE syllabus.subjects RENAME TO syllabus_subjects');
        $this->addSql('ALTER TABLE syllabus.subjects_comments RENAME TO syllabus_subjects_comments');
        $this->addSql('ALTER TABLE syllabus.subjects_profs_map RENAME TO syllabus_subjects_profs_map');
        $this->addSql('ALTER TABLE syllabus.subjects_reply RENAME TO syllabus_subjects_reply');
        $this->addSql('ALTER TABLE tickets.events RENAME TO tickets_events');
        $this->addSql('ALTER TABLE tickets.events_options RENAME TO tickets_events_options');
        $this->addSql('ALTER TABLE tickets.guests_info RENAME TO tickets_guests_info');
        $this->addSql('ALTER TABLE tickets.tickets RENAME TO tickets_tickets');
        $this->addSql('ALTER TABLE users.barcodes RENAME TO users_barcodes');
        $this->addSql('ALTER TABLE users.barcodes_ean12 RENAME TO users_barcodes_ean12');
        $this->addSql('ALTER TABLE users.barcodes_qr RENAME TO users_barcodes_qr');
        $this->addSql('ALTER TABLE users.codes RENAME TO users_codes');
        $this->addSql('ALTER TABLE users.corporate_statuses RENAME TO users_corporate_statuses');
        $this->addSql('ALTER TABLE users.credentials RENAME TO users_credentials');
        $this->addSql('ALTER TABLE users.organization_metadata RENAME TO users_organization_metadata');
        $this->addSql('ALTER TABLE users.organization_statuses RENAME TO users_organization_statuses');
        $this->addSql('ALTER TABLE users.people RENAME TO users_people');
        $this->addSql('ALTER TABLE users.people_academic RENAME TO users_people_academic');
        $this->addSql('ALTER TABLE users.people_corporate RENAME TO users_people_corporate');
        $this->addSql('ALTER TABLE users.people_organizations_academic_year_map RENAME TO users_people_organizations_academic_year_map');
        $this->addSql('ALTER TABLE users.people_organizations_unit_map RENAME TO users_people_organizations_unit_map');
        $this->addSql('ALTER TABLE users.people_organizations_unit_map_academic RENAME TO users_people_organizations_unit_map_academic');
        $this->addSql('ALTER TABLE users.people_organizations_unit_map_external RENAME TO users_people_organizations_unit_map_external');
        $this->addSql('ALTER TABLE users.people_roles_map RENAME TO users_people_roles_map');
        $this->addSql('ALTER TABLE users.people_sale_acco RENAME TO users_people_sale_acco');
        $this->addSql('ALTER TABLE users.people_shift_insurance RENAME TO users_people_shift_insurance');
        $this->addSql('ALTER TABLE users.people_suppliers RENAME TO users_people_suppliers');
        $this->addSql('ALTER TABLE users.registrations RENAME TO users_registrations');
        $this->addSql('ALTER TABLE users.sessions RENAME TO users_sessions');
        $this->addSql('ALTER TABLE users.shibboleth_codes RENAME TO users_shibboleth_codes');
        $this->addSql('ALTER TABLE users.study_enrollment RENAME TO users_study_enrollment');
        $this->addSql('ALTER TABLE users.subject_enrollment RENAME TO users_subject_enrollment');
        $this->addSql('ALTER TABLE users.university_statuses RENAME TO users_university_statuses');
    }

    /**
     * @param  \Doctrine\DBAL\Schema\Schema $schema
     * @return void
     */
    public function down(Schema $schema): void
    {
        $this->abortIf(true, 'Migration cannot be executed down.');
    }
}
