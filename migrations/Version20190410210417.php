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

        $this->addSql('ALTER INDEX acl.actions_pkey RENAME TO acl_actions_pkey');
        $this->addSql('ALTER INDEX acl.resources_pkey RENAME TO acl_resources_pkey');
        $this->addSql('ALTER INDEX acl.roles_actions_map_pkey RENAME TO acl_roles_actions_map_pkey');
        $this->addSql('ALTER INDEX acl.roles_inheritance_map_pkey RENAME TO acl_roles_inheritance_map_pkey');
        $this->addSql('ALTER INDEX acl.roles_pkey RENAME TO acl_roles_pkey');
        $this->addSql('ALTER INDEX api.keys_pkey RENAME TO api_keys_pkey');
        $this->addSql('ALTER INDEX api.keys_roles_map_pkey RENAME TO api_keys_roles_map_pkey');
        $this->addSql('ALTER INDEX br.collaborator_pkey RENAME TO br_collaborator_pkey');
        $this->addSql('ALTER INDEX br.companies_cvbooks_pkey RENAME TO br_companies_cvbooks_pkey');
        $this->addSql('ALTER INDEX br.companies_events_pkey RENAME TO br_companies_events_pkey');
        $this->addSql('ALTER INDEX br.companies_logos_pkey RENAME TO br_companies_logos_pkey');
        $this->addSql('ALTER INDEX br.companies_pkey RENAME TO br_companies_pkey');
        $this->addSql('ALTER INDEX br.companies_request_internship_pkey RENAME TO br_companies_request_internship_pkey');
        $this->addSql('ALTER INDEX br.companies_request_pkey RENAME TO br_companies_request_pkey');
        $this->addSql('ALTER INDEX br.companies_request_vacancy_pkey RENAME TO br_companies_request_vacancy_pkey');
        $this->addSql('ALTER INDEX br.contract_history_pkey RENAME TO br_contract_history_pkey');
        $this->addSql('ALTER INDEX br.contracts_entries_pkey RENAME TO br_contracts_entries_pkey');
        $this->addSql('ALTER INDEX br.contracts_pkey RENAME TO br_contracts_pkey');
        $this->addSql('ALTER INDEX br.cv_entries_pkey RENAME TO br_cv_entries_pkey');
        $this->addSql('ALTER INDEX br.cv_experiences_pkey RENAME TO br_cv_experiences_pkey');
        $this->addSql('ALTER INDEX br.event_company_map_pkey RENAME TO br_event_company_map_pkey');
        $this->addSql('ALTER INDEX br.events_pkey RENAME TO br_events_pkey');
        $this->addSql('ALTER INDEX br.invoice_history_pkey RENAME TO br_invoice_history_pkey');
        $this->addSql('ALTER INDEX br.invoices_contract_pkey RENAME TO br_invoices_contract_pkey');
        $this->addSql('ALTER INDEX br.invoices_entries_pkey RENAME TO br_invoices_entries_pkey');
        $this->addSql('ALTER INDEX br.invoices_manual_pkey RENAME TO br_invoices_manual_pkey');
        $this->addSql('ALTER INDEX br.invoices_pkey RENAME TO br_invoices_pkey');
        $this->addSql('ALTER INDEX br.orders_entries_pkey RENAME TO br_orders_entries_pkey');
        $this->addSql('ALTER INDEX br.orders_pkey RENAME TO br_orders_pkey');
        $this->addSql('ALTER INDEX br.page_years_pkey RENAME TO br_page_years_pkey');
        $this->addSql('ALTER INDEX br.products_pkey RENAME TO br_products_pkey');
        $this->addSql('ALTER INDEX cudi.articles_external_pkey RENAME TO cudi_articles_external_pkey');
        $this->addSql('ALTER INDEX cudi.articles_history_pkey RENAME TO cudi_articles_history_pkey');
        $this->addSql('ALTER INDEX cudi.articles_internal_pkey RENAME TO cudi_articles_internal_pkey');
        $this->addSql('ALTER INDEX cudi.articles_notifications_subscriptions_pkey RENAME TO cudi_articles_notifications_subscriptions_pkey');
        $this->addSql('ALTER INDEX cudi.articles_options_bindings_pkey RENAME TO cudi_articles_options_bindings_pkey');
        $this->addSql('ALTER INDEX cudi.articles_options_colors_pkey RENAME TO cudi_articles_options_colors_pkey');
        $this->addSql('ALTER INDEX cudi.articles_pkey RENAME TO cudi_articles_pkey');
        $this->addSql('ALTER INDEX cudi.articles_subjects_map_pkey RENAME TO cudi_articles_subjects_map_pkey');
        $this->addSql('ALTER INDEX cudi.comments_articles_map_pkey RENAME TO cudi_comments_articles_map_pkey');
        $this->addSql('ALTER INDEX cudi.comments_comments_pkey RENAME TO cudi_comments_comments_pkey');
        $this->addSql('ALTER INDEX cudi.files_articles_map_pkey RENAME TO cudi_files_articles_map_pkey');
        $this->addSql('ALTER INDEX cudi.files_files_pkey RENAME TO cudi_files_files_pkey');
        $this->addSql('ALTER INDEX cudi.isic_card_pkey RENAME TO cudi_isic_card_pkey');
        $this->addSql('ALTER INDEX cudi.log_articles_sales_bookable_pkey RENAME TO cudi_log_articles_sales_bookable_pkey');
        $this->addSql('ALTER INDEX cudi.log_articles_sales_unbookable_pkey RENAME TO cudi_log_articles_sales_unbookable_pkey');
        $this->addSql('ALTER INDEX cudi.log_articles_subject_map_add_pkey RENAME TO cudi_log_articles_subject_map_add_pkey');
        $this->addSql('ALTER INDEX cudi.log_articles_subject_map_remove_pkey RENAME TO cudi_log_articles_subject_map_remove_pkey');
        $this->addSql('ALTER INDEX cudi.log_pkey RENAME TO cudi_log_pkey');
        $this->addSql('ALTER INDEX cudi.log_sales_assignments_pkey RENAME TO cudi_log_sales_assignments_pkey');
        $this->addSql('ALTER INDEX cudi.log_sales_cancellations_pkey RENAME TO cudi_log_sales_cancellations_pkey');
        $this->addSql('ALTER INDEX cudi.log_sales_prof_version_pkey RENAME TO cudi_log_sales_prof_version_pkey');
        $this->addSql('ALTER INDEX cudi.prof_actions_pkey RENAME TO cudi_prof_actions_pkey');
        $this->addSql('ALTER INDEX cudi.sales_articles_barcodes_pkey RENAME TO cudi_sales_articles_barcodes_pkey');
        $this->addSql('ALTER INDEX cudi.sales_articles_pkey RENAME TO cudi_sales_articles_pkey');
        $this->addSql('ALTER INDEX cudi.sales_articles_restrictions_amount_pkey RENAME TO cudi_sales_articles_restrictions_amount_pkey');
        $this->addSql('ALTER INDEX cudi.sales_articles_restrictions_available_pkey RENAME TO cudi_sales_articles_restrictions_available_pkey');
        $this->addSql('ALTER INDEX cudi.sales_articles_restrictions_member_pkey RENAME TO cudi_sales_articles_restrictions_member_pkey');
        $this->addSql('ALTER INDEX cudi.sales_articles_restrictions_pkey RENAME TO cudi_sales_articles_restrictions_pkey');
        $this->addSql('ALTER INDEX cudi.sales_articles_restrictions_study_map_pkey RENAME TO cudi_sales_articles_restrictions_study_map_pkey');
        $this->addSql('ALTER INDEX cudi.sales_articles_restrictions_study_pkey RENAME TO cudi_sales_articles_restrictions_study_pkey');
        $this->addSql('ALTER INDEX cudi.sales_bookings_pkey RENAME TO cudi_sales_bookings_pkey');
        $this->addSql('ALTER INDEX cudi.sales_history_pkey RENAME TO cudi_sales_history_pkey');
        $this->addSql('ALTER INDEX cudi.sales_pay_desks_pkey RENAME TO cudi_sales_pay_desks_pkey');
        $this->addSql('ALTER INDEX cudi.sales_queue_items_pkey RENAME TO cudi_sales_queue_items_pkey');
        $this->addSql('ALTER INDEX cudi.sales_return_items_pkey RENAME TO cudi_sales_return_items_pkey');
        $this->addSql('ALTER INDEX cudi.sales_sale_items_pkey RENAME TO cudi_sales_sale_items_pkey');
        $this->addSql('ALTER INDEX cudi.sales_session_openinghours_pkey RENAME TO cudi_sales_session_openinghours_pkey');
        $this->addSql('ALTER INDEX cudi.sales_session_openinghours_translations_pkey RENAME TO cudi_sales_session_openinghours_translations_pkey');
        $this->addSql('ALTER INDEX cudi.sales_session_restriction_name_pkey RENAME TO cudi_sales_session_restriction_name_pkey');
        $this->addSql('ALTER INDEX cudi.sales_session_restriction_pkey RENAME TO cudi_sales_session_restriction_pkey');
        $this->addSql('ALTER INDEX cudi.sales_session_restriction_study_pkey RENAME TO cudi_sales_session_restriction_study_pkey');
        $this->addSql('ALTER INDEX cudi.sales_session_restriction_year_pkey RENAME TO cudi_sales_session_restriction_year_pkey');
        $this->addSql('ALTER INDEX cudi.sales_session_restrictions_study_map_pkey RENAME TO cudi_sales_session_restrictions_study_map_pkey');
        $this->addSql('ALTER INDEX cudi.sales_sessions_pkey RENAME TO cudi_sales_sessions_pkey');
        $this->addSql('ALTER INDEX cudi.stock_deliveries_pkey RENAME TO cudi_stock_deliveries_pkey');
        $this->addSql('ALTER INDEX cudi.stock_orders_items_pkey RENAME TO cudi_stock_orders_items_pkey');
        $this->addSql('ALTER INDEX cudi.stock_orders_pkey RENAME TO cudi_stock_orders_pkey');
        $this->addSql('ALTER INDEX cudi.stock_orders_virtual_pkey RENAME TO cudi_stock_orders_virtual_pkey');
        $this->addSql('ALTER INDEX cudi.stock_periods_pkey RENAME TO cudi_stock_periods_pkey');
        $this->addSql('ALTER INDEX cudi.stock_periods_values_deltas_pkey RENAME TO cudi_stock_periods_values_deltas_pkey');
        $this->addSql('ALTER INDEX cudi.stock_periods_values_starts_pkey RENAME TO cudi_stock_periods_values_starts_pkey');
        $this->addSql('ALTER INDEX cudi.suppliers_pkey RENAME TO cudi_suppliers_pkey');
        $this->addSql('ALTER INDEX forms.fields_files_pkey RENAME TO forms_fields_files_pkey');
        $this->addSql('ALTER INDEX forms.fields_pkey RENAME TO forms_fields_pkey');
        $this->addSql('ALTER INDEX forms.fields_timeslot_pkey RENAME TO forms_fields_timeslot_pkey');
        $this->addSql('ALTER INDEX forms.fields_timeslots_translations_pkey RENAME TO forms_fields_timeslots_translations_pkey');
        $this->addSql('ALTER INDEX forms.mails_pkey RENAME TO forms_mails_pkey');
        $this->addSql('ALTER INDEX forms.mails_translations_pkey RENAME TO forms_mails_translations_pkey');
        $this->addSql('ALTER INDEX forms.viewers_pkey RENAME TO forms_viewers_pkey');
        $this->addSql('ALTER INDEX general.academic_years_pkey RENAME TO general_academic_years_pkey');
        $this->addSql('ALTER INDEX general.address_cities_pkey RENAME TO general_address_cities_pkey');
        $this->addSql('ALTER INDEX general.address_streets_pkey RENAME TO general_address_streets_pkey');
        $this->addSql('ALTER INDEX general.addresses_pkey RENAME TO general_addresses_pkey');
        $this->addSql('ALTER INDEX general.bank_bank_devices_amounts_pkey RENAME TO general_bank_bank_devices_amounts_pkey');
        $this->addSql('ALTER INDEX general.bank_bank_devices_pkey RENAME TO general_bank_bank_devices_pkey');
        $this->addSql('ALTER INDEX general.bank_cash_registers_pkey RENAME TO general_bank_cash_registers_pkey');
        $this->addSql('ALTER INDEX general.bank_money_units_amounts_pkey RENAME TO general_bank_money_units_amounts_pkey');
        $this->addSql('ALTER INDEX general.bank_money_units_pkey RENAME TO general_bank_money_units_pkey');
        $this->addSql('ALTER INDEX general.config_pkey RENAME TO general_config_pkey');
        $this->addSql('ALTER INDEX general.languages_pkey RENAME TO general_languages_pkey');
        $this->addSql('ALTER INDEX general.locations_pkey RENAME TO general_locations_pkey');
        $this->addSql('ALTER INDEX general.organizations_units_coordinator_roles_map_pkey RENAME TO general_organizations_units_coordinator_roles_map_pkey');
        $this->addSql('ALTER INDEX general.organizations_units_pkey RENAME TO general_organizations_units_pkey');
        $this->addSql('ALTER INDEX general.organizations_units_roles_map_pkey RENAME TO general_organizations_units_roles_map_pkey');
        $this->addSql('ALTER INDEX general.promotions_academic_pkey RENAME TO general_promotions_academic_pkey');
        $this->addSql('ALTER INDEX general.promotions_external_pkey RENAME TO general_promotions_external_pkey');
        $this->addSql('ALTER INDEX general.promotions_pkey RENAME TO general_promotions_pkey');
        $this->addSql('ALTER INDEX general.visits_pkey RENAME TO general_visits_pkey');
        $this->addSql('ALTER INDEX logistics.lease_items_pkey RENAME TO logistics_lease_items_pkey');
        $this->addSql('ALTER INDEX logistics.lease_lease_pkey RENAME TO logistics_lease_lease_pkey');
        $this->addSql('ALTER INDEX logistics.reservations_piano_pkey RENAME TO logistics_reservations_piano_pkey');
        $this->addSql('ALTER INDEX logistics.resources_pkey RENAME TO logistics_resources_pkey');
        $this->addSql('ALTER INDEX mail.aliases_academic_pkey RENAME TO mail_aliases_academic_pkey');
        $this->addSql('ALTER INDEX mail.aliases_external_pkey RENAME TO mail_aliases_external_pkey');
        $this->addSql('ALTER INDEX mail.aliases_pkey RENAME TO mail_aliases_pkey');
        $this->addSql('ALTER INDEX mail.lists_admin_roles_pkey RENAME TO mail_lists_admin_roles_pkey');
        $this->addSql('ALTER INDEX mail.lists_entries_lists_pkey RENAME TO mail_lists_entries_lists_pkey');
        $this->addSql('ALTER INDEX mail.lists_named_pkey RENAME TO mail_lists_named_pkey');
        $this->addSql('ALTER INDEX mail.lists_pkey RENAME TO mail_lists_pkey');
        $this->addSql('ALTER INDEX nodes.banners_pkey RENAME TO nodes_banners_pkey');
        $this->addSql('ALTER INDEX nodes.events_pkey RENAME TO nodes_events_pkey');
        $this->addSql('ALTER INDEX nodes.events_translations_pkey RENAME TO nodes_events_translations_pkey');
        $this->addSql('ALTER INDEX nodes.form_groups_mapping_pkey RENAME TO nodes_form_groups_mapping_pkey');
        $this->addSql('ALTER INDEX nodes.form_groups_pkey RENAME TO nodes_form_groups_pkey');
        $this->addSql('ALTER INDEX nodes.form_groups_translations_pkey RENAME TO nodes_form_groups_translations_pkey');
        $this->addSql('ALTER INDEX nodes.forms_doodles_pkey RENAME TO nodes_forms_doodles_pkey');
        $this->addSql('ALTER INDEX nodes.forms_forms_pkey RENAME TO nodes_forms_forms_pkey');
        $this->addSql('ALTER INDEX nodes.forms_pkey RENAME TO nodes_forms_pkey');
        $this->addSql('ALTER INDEX nodes.news_pkey RENAME TO nodes_news_pkey');
        $this->addSql('ALTER INDEX nodes.news_translations_pkey RENAME TO nodes_news_translations_pkey');
        $this->addSql('ALTER INDEX nodes.nodes_pkey RENAME TO nodes_nodes_pkey');
        $this->addSql('ALTER INDEX nodes.notification_translations_pkey RENAME TO nodes_notification_translations_pkey');
        $this->addSql('ALTER INDEX nodes.notifications_pkey RENAME TO nodes_notifications_pkey');
        $this->addSql('ALTER INDEX nodes.pages_categories_pkey RENAME TO nodes_pages_categories_pkey');
        $this->addSql('ALTER INDEX nodes.pages_categories_translations_pkey RENAME TO nodes_pages_categories_translations_pkey');
        $this->addSql('ALTER INDEX nodes.pages_links_pkey RENAME TO nodes_pages_links_pkey');
        $this->addSql('ALTER INDEX nodes.pages_links_translations_pkey RENAME TO nodes_pages_links_translations_pkey');
        $this->addSql('ALTER INDEX nodes.pages_pkey RENAME TO nodes_pages_pkey');
        $this->addSql('ALTER INDEX nodes.pages_roles_map_pkey RENAME TO nodes_pages_roles_map_pkey');
        $this->addSql('ALTER INDEX nodes.pages_translations_pkey RENAME TO nodes_pages_translations_pkey');
        $this->addSql('ALTER INDEX prom.bus_code_academic_pkey RENAME TO prom_bus_code_academic_pkey');
        $this->addSql('ALTER INDEX prom.bus_code_external_pkey RENAME TO prom_bus_code_external_pkey');
        $this->addSql('ALTER INDEX prom.bus_code_pkey RENAME TO prom_bus_code_pkey');
        $this->addSql('ALTER INDEX prom.bus_passenger_pkey RENAME TO prom_bus_passenger_pkey');
        $this->addSql('ALTER INDEX prom.bus_pkey RENAME TO prom_bus_pkey');
        $this->addSql('ALTER INDEX public.general_migrations_pkey RENAME TO public_general_migrations_pkey');
        $this->addSql('ALTER INDEX publications.editions_html_pkey RENAME TO publications_editions_html_pkey');
        $this->addSql('ALTER INDEX publications.editions_pdf_pkey RENAME TO publications_editions_pdf_pkey');
        $this->addSql('ALTER INDEX publications.editions_pkey RENAME TO publications_editions_pkey');
        $this->addSql('ALTER INDEX publications.publications_pkey RENAME TO publications_publications_pkey');
        $this->addSql('ALTER INDEX quiz.points_pkey RENAME TO quiz_points_pkey');
        $this->addSql('ALTER INDEX quiz.quizes_pkey RENAME TO quiz_quizes_pkey');
        $this->addSql('ALTER INDEX quiz.quizes_roles_map_pkey RENAME TO quiz_quizes_roles_map_pkey');
        $this->addSql('ALTER INDEX quiz.rounds_pkey RENAME TO quiz_rounds_pkey');
        $this->addSql('ALTER INDEX quiz.teams_pkey RENAME TO quiz_teams_pkey');
        $this->addSql('ALTER INDEX shifts.responsibles_pkey RENAME TO shifts_responsibles_pkey');
        $this->addSql('ALTER INDEX shifts.shifts_pkey RENAME TO shifts_shifts_pkey');
        $this->addSql('ALTER INDEX shifts.shifts_responsibles_map_pkey RENAME TO shifts_shifts_responsibles_map_pkey');
        $this->addSql('ALTER INDEX shifts.shifts_roles_map_pkey RENAME TO shifts_shifts_roles_map_pkey');
        $this->addSql('ALTER INDEX shifts.shifts_volunteers_map_pkey RENAME TO shifts_shifts_volunteers_map_pkey');
        $this->addSql('ALTER INDEX shifts.volunteers_pkey RENAME TO shifts_volunteers_pkey');
        $this->addSql('ALTER INDEX shop.products_pkey RENAME TO shop_products_pkey');
        $this->addSql('ALTER INDEX shop.reservation_permissions_pkey RENAME TO shop_reservation_permissions_pkey');
        $this->addSql('ALTER INDEX shop.reservations_pkey RENAME TO shop_reservations_pkey');
        $this->addSql('ALTER INDEX shop.session_stock_entries_pkey RENAME TO shop_session_stock_entries_pkey');
        $this->addSql('ALTER INDEX shop.sessions_pkey RENAME TO shop_sessions_pkey');
        $this->addSql('ALTER INDEX sport.departments_pkey RENAME TO sport_departments_pkey');
        $this->addSql('ALTER INDEX sport.groups_pkey RENAME TO sport_groups_pkey');
        $this->addSql('ALTER INDEX sport.laps_pkey RENAME TO sport_laps_pkey');
        $this->addSql('ALTER INDEX sport.runners_pkey RENAME TO sport_runners_pkey');
        $this->addSql('ALTER INDEX syllabus.combination_module_group_map_pkey RENAME TO syllabus_combination_module_group_map_pkey');
        $this->addSql('ALTER INDEX syllabus.groups_pkey RENAME TO syllabus_groups_pkey');
        $this->addSql('ALTER INDEX syllabus.pocs_pkey RENAME TO syllabus_pocs_pkey');
        $this->addSql('ALTER INDEX syllabus.student_enrollment_pkey RENAME TO syllabus_student_enrollment_pkey');
        $this->addSql('ALTER INDEX syllabus.studies_group_map_pkey RENAME TO syllabus_studies_group_map_pkey');
        $this->addSql('ALTER INDEX syllabus.studies_pkey RENAME TO syllabus_studies_pkey');
        $this->addSql('ALTER INDEX syllabus.studies_subjects_map_pkey RENAME TO syllabus_studies_subjects_map_pkey');
        $this->addSql('ALTER INDEX syllabus.study_combinations_pkey RENAME TO syllabus_study_combinations_pkey');
        $this->addSql('ALTER INDEX syllabus.study_module_group_pkey RENAME TO syllabus_study_module_group_pkey');
        $this->addSql('ALTER INDEX syllabus.subjects_comments_pkey RENAME TO syllabus_subjects_comments_pkey');
        $this->addSql('ALTER INDEX syllabus.subjects_pkey RENAME TO syllabus_subjects_pkey');
        $this->addSql('ALTER INDEX syllabus.subjects_profs_map_pkey RENAME TO syllabus_subjects_profs_map_pkey');
        $this->addSql('ALTER INDEX syllabus.subjects_reply_pkey RENAME TO syllabus_subjects_reply_pkey');
        $this->addSql('ALTER INDEX tickets.events_options_pkey RENAME TO tickets_events_options_pkey');
        $this->addSql('ALTER INDEX tickets.events_pkey RENAME TO tickets_events_pkey');
        $this->addSql('ALTER INDEX tickets.guests_info_pkey RENAME TO tickets_guests_info_pkey');
        $this->addSql('ALTER INDEX tickets.tickets_pkey RENAME TO tickets_tickets_pkey');
        $this->addSql('ALTER INDEX users.barcodes_ean12_pkey RENAME TO users_barcodes_ean12_pkey');
        $this->addSql('ALTER INDEX users.barcodes_pkey RENAME TO users_barcodes_pkey');
        $this->addSql('ALTER INDEX users.barcodes_qr_pkey RENAME TO users_barcodes_qr_pkey');
        $this->addSql('ALTER INDEX users.codes_pkey RENAME TO users_codes_pkey');
        $this->addSql('ALTER INDEX users.corporate_statuses_pkey RENAME TO users_corporate_statuses_pkey');
        $this->addSql('ALTER INDEX users.credentials_pkey RENAME TO users_credentials_pkey');
        $this->addSql('ALTER INDEX users.organization_metadata_pkey RENAME TO users_organization_metadata_pkey');
        $this->addSql('ALTER INDEX users.organization_statuses_pkey RENAME TO users_organization_statuses_pkey');
        $this->addSql('ALTER INDEX users.people_academic_pkey RENAME TO users_people_academic_pkey');
        $this->addSql('ALTER INDEX users.people_corporate_pkey RENAME TO users_people_corporate_pkey');
        $this->addSql('ALTER INDEX users.people_organizations_academic_year_map_pkey RENAME TO users_people_organizations_academic_year_map_pkey');
        $this->addSql('ALTER INDEX users.people_organizations_unit_map_academic_pkey RENAME TO users_people_organizations_unit_map_academic_pkey');
        $this->addSql('ALTER INDEX users.people_organizations_unit_map_external_pkey RENAME TO users_people_organizations_unit_map_external_pkey');
        $this->addSql('ALTER INDEX users.people_organizations_unit_map_pkey RENAME TO users_people_organizations_unit_map_pkey');
        $this->addSql('ALTER INDEX users.people_pkey RENAME TO users_people_pkey');
        $this->addSql('ALTER INDEX users.people_roles_map_pkey RENAME TO users_people_roles_map_pkey');
        $this->addSql('ALTER INDEX users.people_sale_acco_pkey RENAME TO users_people_sale_acco_pkey');
        $this->addSql('ALTER INDEX users.people_shift_insurance_pkey RENAME TO users_people_shift_insurance_pkey');
        $this->addSql('ALTER INDEX users.people_suppliers_pkey RENAME TO users_people_suppliers_pkey');
        $this->addSql('ALTER INDEX users.registrations_pkey RENAME TO users_registrations_pkey');
        $this->addSql('ALTER INDEX users.sessions_pkey RENAME TO users_sessions_pkey');
        $this->addSql('ALTER INDEX users.shibboleth_codes_pkey RENAME TO users_shibboleth_codes_pkey');
        $this->addSql('ALTER INDEX users.study_enrollment_pkey RENAME TO users_study_enrollment_pkey');
        $this->addSql('ALTER INDEX users.subject_enrollment_pkey RENAME TO users_subject_enrollment_pkey');
        $this->addSql('ALTER INDEX users.university_statuses_pkey RENAME TO users_university_statuses_pkey');

        $this->addSql('ALTER INDEX IF EXISTS br.companies_job_pkey RENAME TO br_companies_jobs_pkey');
        $this->addSql('ALTER INDEX IF EXISTS br.companies_jobs_pkey RENAME TO br_companies_jobs_pkey');
        $this->addSql('ALTER INDEX IF EXISTS br.companies_page_pkey RENAME TO br_companies_pages_pkey');
        $this->addSql('ALTER INDEX IF EXISTS br.companies_pages_pkey RENAME TO br_companies_pages_pkey');
        $this->addSql('ALTER INDEX IF EXISTS br.cv_language_pkey RENAME TO br_cv_languages_pkey');
        $this->addSql('ALTER INDEX IF EXISTS br.cv_languages_pkey RENAME TO br_cv_languages_pkey');
        $this->addSql('ALTER INDEX IF EXISTS cudi.sales_articles_discounts_discounts_pkey RENAME TO cudi_sales_articles_discounts_discounts_pkey');
        $this->addSql('ALTER INDEX IF EXISTS cudi.sales_articles_discounts_templates_pkey RENAME TO cudi_sales_articles_discounts_templates_pkey');
        $this->addSql('ALTER INDEX IF EXISTS cudi.sales_discounts_discounts_pkey RENAME TO cudi_sales_articles_discounts_discounts_pkey');
        $this->addSql('ALTER INDEX IF EXISTS cudi.sales_discounts_templates_pkey RENAME TO cudi_sales_articles_discounts_templates_pkey');
        $this->addSql('ALTER INDEX IF EXISTS cudi.stock_deliveries_retours_pkey RENAME TO cudi_stock_retours_pkey');
        $this->addSql('ALTER INDEX IF EXISTS cudi.stock_retours_pkey RENAME TO cudi_stock_retours_pkey');
        $this->addSql('ALTER INDEX IF EXISTS forms.entries_pkey RENAME TO forms_entries_pkey');
        $this->addSql('ALTER INDEX IF EXISTS forms.field_checkbox_pkey RENAME TO forms_fields_checkboxes_pkey');
        $this->addSql('ALTER INDEX IF EXISTS forms.field_dropdown_pkey RENAME TO forms_fields_dropdowns_pkey');
        $this->addSql('ALTER INDEX IF EXISTS forms.field_options_pkey RENAME TO forms_fields_options_pkey');
        $this->addSql('ALTER INDEX IF EXISTS forms.field_string_pkey RENAME TO forms_fields_texts_pkey');
        $this->addSql('ALTER INDEX IF EXISTS forms.field_translation_pkey RENAME TO forms_fields_translations_pkey');
        $this->addSql('ALTER INDEX IF EXISTS forms.fieldentries_pkey RENAME TO forms_entries_pkey');
        $this->addSql('ALTER INDEX IF EXISTS forms.fields_checkboxes_pkey RENAME TO forms_fields_checkboxes_pkey');
        $this->addSql('ALTER INDEX IF EXISTS forms.fields_dropdowns_pkey RENAME TO forms_fields_dropdowns_pkey');
        $this->addSql('ALTER INDEX IF EXISTS forms.fields_options_pkey RENAME TO forms_fields_options_pkey');
        $this->addSql('ALTER INDEX IF EXISTS forms.fields_options_translations_pkey RENAME TO forms_fields_options_translations_pkey');
        $this->addSql('ALTER INDEX IF EXISTS forms.fields_strings_pkey RENAME TO forms_fields_texts_pkey');
        $this->addSql('ALTER INDEX IF EXISTS forms.fields_texts_pkey RENAME TO forms_fields_texts_pkey');
        $this->addSql('ALTER INDEX IF EXISTS forms.fields_translations_pkey RENAME TO forms_fields_translations_pkey');
        $this->addSql('ALTER INDEX IF EXISTS forms.guest_info_pkey RENAME TO forms_guests_info_pkey');
        $this->addSql('ALTER INDEX IF EXISTS forms.guests_info_pkey RENAME TO forms_guests_info_pkey');
        $this->addSql('ALTER INDEX IF EXISTS forms.option_translation_pkey RENAME TO forms_fields_options_translations_pkey');
        $this->addSql('ALTER INDEX IF EXISTS gallery.album_pkey RENAME TO gallery_albums_pkey');
        $this->addSql('ALTER INDEX IF EXISTS gallery.albums_pkey RENAME TO gallery_albums_pkey');
        $this->addSql('ALTER INDEX IF EXISTS gallery.photo_pkey RENAME TO gallery_photos_pkey');
        $this->addSql('ALTER INDEX IF EXISTS gallery.photos_pkey RENAME TO gallery_photos_pkey');
        $this->addSql('ALTER INDEX IF EXISTS gallery.translation_pkey RENAME TO gallery_translations_pkey');
        $this->addSql('ALTER INDEX IF EXISTS gallery.translations_pkey RENAME TO gallery_translations_pkey');
        $this->addSql('ALTER INDEX IF EXISTS general.organisation_pkey RENAME TO general_organizations_pkey');
        $this->addSql('ALTER INDEX IF EXISTS general.organizations_pkey RENAME TO general_organizations_pkey');
        $this->addSql('ALTER INDEX IF EXISTS logistics.driver_pkey RENAME TO logistics_drivers_pkey');
        $this->addSql('ALTER INDEX IF EXISTS logistics.driver_years_pkey RENAME TO logistics_drivers_years_pkey');
        $this->addSql('ALTER INDEX IF EXISTS logistics.drivers_pkey RENAME TO logistics_drivers_pkey');
        $this->addSql('ALTER INDEX IF EXISTS logistics.drivers_years_pkey RENAME TO logistics_drivers_years_pkey');
        $this->addSql('ALTER INDEX IF EXISTS logistics.reservation_pkey RENAME TO logistics_reservations_pkey');
        $this->addSql('ALTER INDEX IF EXISTS logistics.reservation_van_pkey RENAME TO logistics_reservations_van_pkey');
        $this->addSql('ALTER INDEX IF EXISTS logistics.reservations_pkey RENAME TO logistics_reservations_pkey');
        $this->addSql('ALTER INDEX IF EXISTS logistics.reservations_van_pkey RENAME TO logistics_reservations_van_pkey');
        $this->addSql('ALTER INDEX IF EXISTS mail.list_admins_pkey RENAME TO mail_lists_admins_pkey');
        $this->addSql('ALTER INDEX IF EXISTS mail.list_entries_pkey RENAME TO mail_lists_entries_pkey');
        $this->addSql('ALTER INDEX IF EXISTS mail.lists_admins_pkey RENAME TO mail_lists_admins_pkey');
        $this->addSql('ALTER INDEX IF EXISTS mail.lists_entries_academic_pkey RENAME TO mail_lists_entries_people_academic_pkey');
        $this->addSql('ALTER INDEX IF EXISTS mail.lists_entries_external_pkey RENAME TO mail_lists_entries_people_external_pkey');
        $this->addSql('ALTER INDEX IF EXISTS mail.lists_entries_people_academic_pkey RENAME TO mail_lists_entries_people_academic_pkey');
        $this->addSql('ALTER INDEX IF EXISTS mail.lists_entries_people_external_pkey RENAME TO mail_lists_entries_people_external_pkey');
        $this->addSql('ALTER INDEX IF EXISTS mail.lists_entries_pkey RENAME TO mail_lists_entries_pkey');
        $this->addSql('ALTER INDEX IF EXISTS nodes.form_entries_pkey RENAME TO nodes_forms_entries_pkey');
        $this->addSql('ALTER INDEX IF EXISTS nodes.form_translation_pkey RENAME TO nodes_forms_translations_pkey');
        $this->addSql('ALTER INDEX IF EXISTS nodes.forms_entries_pkey RENAME TO nodes_forms_entries_pkey');
        $this->addSql('ALTER INDEX IF EXISTS nodes.forms_translations_pkey RENAME TO nodes_forms_translations_pkey');
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
