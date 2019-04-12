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
 * Version 20190410211245
 */
class Version20190410211245 extends \Doctrine\Migrations\AbstractMigration
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

        $this->addSql('ALTER SEQUENCE acl.acl_actions_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE api.api_keys_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_collaborator_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_companies_events_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_companies_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_companies_jobs_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_companies_logos_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_companies_pages_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_companies_request_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_contract_history_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_contracts_entries_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_contracts_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_cv_entries_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_cv_experiences_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_cv_languages_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_event_company_map_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_events_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_invoice_history_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_invoices_entries_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_invoices_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_orders_entries_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_orders_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE br.br_products_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_articles_history_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_articles_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_articles_notifications_subscriptions_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_articles_options_bindings_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_articles_options_colors_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_articles_subjects_map_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_comments_articles_map_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_comments_comments_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_files_articles_map_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_files_files_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_isic_card_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_log_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_prof_actions_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_sales_articles_barcodes_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_sales_articles_discounts_discounts_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_sales_articles_discounts_templates_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_sales_articles_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_sales_articles_restrictions_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_sales_bookings_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_sales_history_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_sales_pay_desks_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_sales_queue_items_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_sales_return_items_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_sales_sale_items_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_sales_session_openinghours_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_sales_session_openinghours_translations_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_sales_session_restriction_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_sales_sessions_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_stock_deliveries_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_stock_orders_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_stock_orders_items_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_stock_orders_virtual_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_stock_periods_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_stock_periods_values_deltas_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_stock_periods_values_starts_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_stock_retours_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE cudi.cudi_suppliers_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE forms.forms_fields_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE forms.forms_fields_options_translations_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE forms.forms_fields_timeslots_translations_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE forms.forms_fields_translations_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE forms.forms_guests_info_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE forms.forms_mails_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE forms.forms_mails_translations_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE forms.forms_viewers_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE gallery.gallery_albums_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE gallery.gallery_photos_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE gallery.gallery_translations_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE general.general_academic_years_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE general.general_address_cities_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE general.general_address_streets_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE general.general_addresses_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE general.general_bank_bank_devices_amounts_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE general.general_bank_bank_devices_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE general.general_bank_cash_registers_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE general.general_bank_money_units_amounts_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE general.general_bank_money_units_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE general.general_languages_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE general.general_locations_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE general.general_organizations_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE general.general_organizations_units_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE general.general_promotions_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE general.general_visits_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE logistics.logistics_lease_items_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE logistics.logistics_lease_lease_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE logistics.logistics_reservations_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE mail.mail_aliases_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE mail.mail_lists_admin_roles_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE mail.mail_lists_admins_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE mail.mail_lists_entries_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE mail.mail_lists_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE nodes.nodes_events_translations_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE nodes.nodes_form_groups_mapping_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE nodes.nodes_form_groups_translations_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE nodes.nodes_forms_entries_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE nodes.nodes_forms_translations_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE nodes.nodes_news_translations_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE nodes.nodes_nodes_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE nodes.nodes_notification_translations_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE nodes.nodes_pages_categories_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE nodes.nodes_pages_categories_translations_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE nodes.nodes_pages_links_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE nodes.nodes_pages_links_translations_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE nodes.nodes_pages_translations_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE prom.prom_bus_code_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE prom.prom_bus_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE prom.prom_bus_passenger_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE publications.publications_editions_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE publications.publications_publications_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE quiz.quiz_points_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE quiz.quiz_quizes_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE quiz.quiz_rounds_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE quiz.quiz_teams_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE shifts.shifts_responsibles_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE shifts.shifts_shifts_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE shifts.shifts_volunteers_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE shop.shop_products_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE shop.shop_reservations_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE shop.shop_sessions_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE sport.sport_departments_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE sport.sport_groups_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE sport.sport_laps_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE sport.sport_runners_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE syllabus.syllabus_groups_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE syllabus.syllabus_pocs_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE syllabus.syllabus_student_enrollment_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE syllabus.syllabus_studies_group_map_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE syllabus.syllabus_studies_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE syllabus.syllabus_studies_subjects_map_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE syllabus.syllabus_study_combinations_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE syllabus.syllabus_study_module_group_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE syllabus.syllabus_subjects_comments_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE syllabus.syllabus_subjects_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE syllabus.syllabus_subjects_profs_map_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE syllabus.syllabus_subjects_reply_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE tickets.tickets_events_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE tickets.tickets_events_options_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE tickets.tickets_guests_info_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE tickets.tickets_tickets_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE users.users_barcodes_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE users.users_codes_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE users.users_corporate_statuses_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE users.users_credentials_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE users.users_organization_metadata_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE users.users_organization_statuses_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE users.users_people_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE users.users_people_organizations_academic_year_map_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE users.users_people_organizations_unit_map_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE users.users_people_sale_acco_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE users.users_people_shift_insurance_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE users.users_registrations_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE users.users_study_enrollment_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE users.users_subject_enrollment_id_seq SET SCHEMA public');
        $this->addSql('ALTER SEQUENCE users.users_university_statuses_id_seq SET SCHEMA public');

        $this->addSql('ALTER TABLE acl.acl_actions SET SCHEMA public');
        $this->addSql('ALTER TABLE acl.acl_resources SET SCHEMA public');
        $this->addSql('ALTER TABLE acl.acl_roles SET SCHEMA public');
        $this->addSql('ALTER TABLE acl.acl_roles_actions_map SET SCHEMA public');
        $this->addSql('ALTER TABLE acl.acl_roles_inheritance_map SET SCHEMA public');
        $this->addSql('ALTER TABLE api.api_keys SET SCHEMA public');
        $this->addSql('ALTER TABLE api.api_keys_roles_map SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_collaborator SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_companies SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_companies_cvbooks SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_companies_events SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_companies_jobs SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_companies_logos SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_companies_pages SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_companies_request SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_companies_request_internship SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_companies_request_vacancy SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_contract_history SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_contracts SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_contracts_entries SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_cv_entries SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_cv_experiences SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_cv_languages SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_event_company_map SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_events SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_invoice_history SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_invoices SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_invoices_contract SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_invoices_entries SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_invoices_manual SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_orders SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_orders_entries SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_page_years SET SCHEMA public');
        $this->addSql('ALTER TABLE br.br_products SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_articles SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_articles_external SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_articles_history SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_articles_internal SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_articles_notifications_subscriptions SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_articles_options_bindings SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_articles_options_colors SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_articles_subjects_map SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_comments_articles_map SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_comments_comments SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_files_articles_map SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_files_files SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_isic_card SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_log SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_log_articles_sales_bookable SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_log_articles_sales_unbookable SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_log_articles_subject_map_add SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_log_articles_subject_map_remove SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_log_sales_assignments SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_log_sales_cancellations SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_log_sales_prof_version SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_prof_actions SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_articles SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_articles_barcodes SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_articles_discounts_discounts SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_articles_discounts_templates SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_articles_restrictions SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_articles_restrictions_amount SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_articles_restrictions_available SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_articles_restrictions_member SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_articles_restrictions_study SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_articles_restrictions_study_map SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_bookings SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_history SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_pay_desks SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_queue_items SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_return_items SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_sale_items SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_session_openinghours SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_session_openinghours_translations SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_session_restriction SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_session_restriction_name SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_session_restriction_study SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_session_restriction_year SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_session_restrictions_study_map SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_sales_sessions SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_stock_deliveries SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_stock_orders SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_stock_orders_items SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_stock_orders_virtual SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_stock_periods SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_stock_periods_values_deltas SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_stock_periods_values_starts SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_stock_retours SET SCHEMA public');
        $this->addSql('ALTER TABLE cudi.cudi_suppliers SET SCHEMA public');
        $this->addSql('ALTER TABLE forms.forms_entries SET SCHEMA public');
        $this->addSql('ALTER TABLE forms.forms_fields SET SCHEMA public');
        $this->addSql('ALTER TABLE forms.forms_fields_checkboxes SET SCHEMA public');
        $this->addSql('ALTER TABLE forms.forms_fields_dropdowns SET SCHEMA public');
        $this->addSql('ALTER TABLE forms.forms_fields_files SET SCHEMA public');
        $this->addSql('ALTER TABLE forms.forms_fields_options SET SCHEMA public');
        $this->addSql('ALTER TABLE forms.forms_fields_options_translations SET SCHEMA public');
        $this->addSql('ALTER TABLE forms.forms_fields_texts SET SCHEMA public');
        $this->addSql('ALTER TABLE forms.forms_fields_timeslot SET SCHEMA public');
        $this->addSql('ALTER TABLE forms.forms_fields_timeslots_translations SET SCHEMA public');
        $this->addSql('ALTER TABLE forms.forms_fields_translations SET SCHEMA public');
        $this->addSql('ALTER TABLE forms.forms_guests_info SET SCHEMA public');
        $this->addSql('ALTER TABLE forms.forms_mails SET SCHEMA public');
        $this->addSql('ALTER TABLE forms.forms_mails_translations SET SCHEMA public');
        $this->addSql('ALTER TABLE forms.forms_viewers SET SCHEMA public');
        $this->addSql('ALTER TABLE gallery.gallery_albums SET SCHEMA public');
        $this->addSql('ALTER TABLE gallery.gallery_photos SET SCHEMA public');
        $this->addSql('ALTER TABLE gallery.gallery_translations SET SCHEMA public');
        $this->addSql('ALTER TABLE general.general_academic_years SET SCHEMA public');
        $this->addSql('ALTER TABLE general.general_address_cities SET SCHEMA public');
        $this->addSql('ALTER TABLE general.general_address_streets SET SCHEMA public');
        $this->addSql('ALTER TABLE general.general_addresses SET SCHEMA public');
        $this->addSql('ALTER TABLE general.general_bank_bank_devices SET SCHEMA public');
        $this->addSql('ALTER TABLE general.general_bank_bank_devices_amounts SET SCHEMA public');
        $this->addSql('ALTER TABLE general.general_bank_cash_registers SET SCHEMA public');
        $this->addSql('ALTER TABLE general.general_bank_money_units SET SCHEMA public');
        $this->addSql('ALTER TABLE general.general_bank_money_units_amounts SET SCHEMA public');
        $this->addSql('ALTER TABLE general.general_config SET SCHEMA public');
        $this->addSql('ALTER TABLE general.general_languages SET SCHEMA public');
        $this->addSql('ALTER TABLE general.general_locations SET SCHEMA public');
        $this->addSql('ALTER TABLE general.general_organizations SET SCHEMA public');
        $this->addSql('ALTER TABLE general.general_organizations_units SET SCHEMA public');
        $this->addSql('ALTER TABLE general.general_organizations_units_coordinator_roles_map SET SCHEMA public');
        $this->addSql('ALTER TABLE general.general_organizations_units_roles_map SET SCHEMA public');
        $this->addSql('ALTER TABLE general.general_promotions SET SCHEMA public');
        $this->addSql('ALTER TABLE general.general_promotions_academic SET SCHEMA public');
        $this->addSql('ALTER TABLE general.general_promotions_external SET SCHEMA public');
        $this->addSql('ALTER TABLE general.general_visits SET SCHEMA public');
        $this->addSql('ALTER TABLE logistics.logistics_drivers SET SCHEMA public');
        $this->addSql('ALTER TABLE logistics.logistics_drivers_years SET SCHEMA public');
        $this->addSql('ALTER TABLE logistics.logistics_lease_items SET SCHEMA public');
        $this->addSql('ALTER TABLE logistics.logistics_lease_lease SET SCHEMA public');
        $this->addSql('ALTER TABLE logistics.logistics_reservations SET SCHEMA public');
        $this->addSql('ALTER TABLE logistics.logistics_reservations_piano SET SCHEMA public');
        $this->addSql('ALTER TABLE logistics.logistics_reservations_van SET SCHEMA public');
        $this->addSql('ALTER TABLE logistics.logistics_resources SET SCHEMA public');
        $this->addSql('ALTER TABLE mail.mail_aliases SET SCHEMA public');
        $this->addSql('ALTER TABLE mail.mail_aliases_academic SET SCHEMA public');
        $this->addSql('ALTER TABLE mail.mail_aliases_external SET SCHEMA public');
        $this->addSql('ALTER TABLE mail.mail_lists SET SCHEMA public');
        $this->addSql('ALTER TABLE mail.mail_lists_admin_roles SET SCHEMA public');
        $this->addSql('ALTER TABLE mail.mail_lists_admins SET SCHEMA public');
        $this->addSql('ALTER TABLE mail.mail_lists_entries SET SCHEMA public');
        $this->addSql('ALTER TABLE mail.mail_lists_entries_lists SET SCHEMA public');
        $this->addSql('ALTER TABLE mail.mail_lists_entries_people_academic SET SCHEMA public');
        $this->addSql('ALTER TABLE mail.mail_lists_entries_people_external SET SCHEMA public');
        $this->addSql('ALTER TABLE mail.mail_lists_named SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_banners SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_events SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_events_translations SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_form_groups SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_form_groups_mapping SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_form_groups_translations SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_forms SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_forms_doodles SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_forms_entries SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_forms_forms SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_forms_translations SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_news SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_news_translations SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_nodes SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_notification_translations SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_notifications SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_pages SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_pages_categories SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_pages_categories_translations SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_pages_links SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_pages_links_translations SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_pages_roles_map SET SCHEMA public');
        $this->addSql('ALTER TABLE nodes.nodes_pages_translations SET SCHEMA public');
        $this->addSql('ALTER TABLE prom.prom_bus SET SCHEMA public');
        $this->addSql('ALTER TABLE prom.prom_bus_code SET SCHEMA public');
        $this->addSql('ALTER TABLE prom.prom_bus_code_academic SET SCHEMA public');
        $this->addSql('ALTER TABLE prom.prom_bus_code_external SET SCHEMA public');
        $this->addSql('ALTER TABLE prom.prom_bus_passenger SET SCHEMA public');
        $this->addSql('ALTER TABLE publications.publications_editions SET SCHEMA public');
        $this->addSql('ALTER TABLE publications.publications_editions_html SET SCHEMA public');
        $this->addSql('ALTER TABLE publications.publications_editions_pdf SET SCHEMA public');
        $this->addSql('ALTER TABLE publications.publications_publications SET SCHEMA public');
        $this->addSql('ALTER TABLE quiz.quiz_points SET SCHEMA public');
        $this->addSql('ALTER TABLE quiz.quiz_quizes SET SCHEMA public');
        $this->addSql('ALTER TABLE quiz.quiz_quizes_roles_map SET SCHEMA public');
        $this->addSql('ALTER TABLE quiz.quiz_rounds SET SCHEMA public');
        $this->addSql('ALTER TABLE quiz.quiz_teams SET SCHEMA public');
        $this->addSql('ALTER TABLE shifts.shifts_responsibles SET SCHEMA public');
        $this->addSql('ALTER TABLE shifts.shifts_shifts SET SCHEMA public');
        $this->addSql('ALTER TABLE shifts.shifts_shifts_responsibles_map SET SCHEMA public');
        $this->addSql('ALTER TABLE shifts.shifts_shifts_roles_map SET SCHEMA public');
        $this->addSql('ALTER TABLE shifts.shifts_shifts_volunteers_map SET SCHEMA public');
        $this->addSql('ALTER TABLE shifts.shifts_volunteers SET SCHEMA public');
        $this->addSql('ALTER TABLE shop.shop_products SET SCHEMA public');
        $this->addSql('ALTER TABLE shop.shop_reservation_permissions SET SCHEMA public');
        $this->addSql('ALTER TABLE shop.shop_reservations SET SCHEMA public');
        $this->addSql('ALTER TABLE shop.shop_session_stock_entries SET SCHEMA public');
        $this->addSql('ALTER TABLE shop.shop_sessions SET SCHEMA public');
        $this->addSql('ALTER TABLE sport.sport_departments SET SCHEMA public');
        $this->addSql('ALTER TABLE sport.sport_groups SET SCHEMA public');
        $this->addSql('ALTER TABLE sport.sport_laps SET SCHEMA public');
        $this->addSql('ALTER TABLE sport.sport_runners SET SCHEMA public');
        $this->addSql('ALTER TABLE syllabus.syllabus_combination_module_group_map SET SCHEMA public');
        $this->addSql('ALTER TABLE syllabus.syllabus_groups SET SCHEMA public');
        $this->addSql('ALTER TABLE syllabus.syllabus_pocs SET SCHEMA public');
        $this->addSql('ALTER TABLE syllabus.syllabus_student_enrollment SET SCHEMA public');
        $this->addSql('ALTER TABLE syllabus.syllabus_studies SET SCHEMA public');
        $this->addSql('ALTER TABLE syllabus.syllabus_studies_group_map SET SCHEMA public');
        $this->addSql('ALTER TABLE syllabus.syllabus_studies_subjects_map SET SCHEMA public');
        $this->addSql('ALTER TABLE syllabus.syllabus_study_combinations SET SCHEMA public');
        $this->addSql('ALTER TABLE syllabus.syllabus_study_module_group SET SCHEMA public');
        $this->addSql('ALTER TABLE syllabus.syllabus_subjects SET SCHEMA public');
        $this->addSql('ALTER TABLE syllabus.syllabus_subjects_comments SET SCHEMA public');
        $this->addSql('ALTER TABLE syllabus.syllabus_subjects_profs_map SET SCHEMA public');
        $this->addSql('ALTER TABLE syllabus.syllabus_subjects_reply SET SCHEMA public');
        $this->addSql('ALTER TABLE tickets.tickets_events SET SCHEMA public');
        $this->addSql('ALTER TABLE tickets.tickets_events_options SET SCHEMA public');
        $this->addSql('ALTER TABLE tickets.tickets_guests_info SET SCHEMA public');
        $this->addSql('ALTER TABLE tickets.tickets_tickets SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_barcodes SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_barcodes_ean12 SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_barcodes_qr SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_codes SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_corporate_statuses SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_credentials SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_organization_metadata SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_organization_statuses SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_people SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_people_academic SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_people_corporate SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_people_organizations_academic_year_map SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_people_organizations_unit_map SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_people_organizations_unit_map_academic SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_people_organizations_unit_map_external SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_people_roles_map SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_people_sale_acco SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_people_shift_insurance SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_people_suppliers SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_registrations SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_sessions SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_shibboleth_codes SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_study_enrollment SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_subject_enrollment SET SCHEMA public');
        $this->addSql('ALTER TABLE users.users_university_statuses SET SCHEMA public');

        $this->addSql('DROP SCHEMA acl');
        $this->addSql('DROP SCHEMA api');
        $this->addSql('DROP SCHEMA br');
        $this->addSql('DROP SCHEMA cudi');
        $this->addSql('DROP SCHEMA forms');
        $this->addSql('DROP SCHEMA gallery');
        $this->addSql('DROP SCHEMA general');
        $this->addSql('DROP SCHEMA logistics');
        $this->addSql('DROP SCHEMA mail');
        $this->addSql('DROP SCHEMA nodes');
        $this->addSql('DROP SCHEMA prom');
        $this->addSql('DROP SCHEMA publications');
        $this->addSql('DROP SCHEMA quiz');
        $this->addSql('DROP SCHEMA shifts');
        $this->addSql('DROP SCHEMA shop');
        $this->addSql('DROP SCHEMA sport');
        $this->addSql('DROP SCHEMA syllabus');
        $this->addSql('DROP SCHEMA tickets');
        $this->addSql('DROP SCHEMA users');
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
