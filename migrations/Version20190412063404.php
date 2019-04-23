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
 * Version 20190412063404
 */
class Version20190412063404 extends \Doctrine\Migrations\AbstractMigration
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

        $this->addSql('ALTER INDEX academics_organizations_map_unique RENAME TO users_people_academic_years_map_academic_academic_year');
        $this->addSql('ALTER INDEX contract_unique RENAME TO br_contracts_author_contract_nb');
        $this->addSql('ALTER INDEX insurance_unique RENAME TO shift_users_academic_years_map_person_academic_year');
        $this->addSql('ALTER INDEX sales_booking_time RENAME TO cudi_sale_bookings_book_date');
        $this->addSql('ALTER INDEX sales_return_item_time RENAME TO cudi_sale_return_items_timestamp');
        $this->addSql('ALTER INDEX sales_sale_item_time RENAME TO cudi_sale_sale_items_timestamp');
        $this->addSql('ALTER INDEX stock_deliveries_time RENAME TO cudi_stock_deliveries_timestamp');
        $this->addSql('ALTER INDEX stock_orders_time RENAME TO cudi_stock_orders_date_created');
        $this->addSql('ALTER INDEX stock_retours_time RENAME TO cudi_stock_retours_timestamp');
        $this->addSql('ALTER INDEX street_name RENAME TO general_addresses_streets_name');
        $this->addSql('ALTER INDEX subjects_name RENAME TO syllabus_subjects_name_code');
        $this->addSql('ALTER INDEX ticket_number_unique RENAME TO ticket_tickets_event_number');
        $this->addSql('ALTER INDEX username_unique RENAME TO users_people_username');
        $this->addSql('ALTER INDEX year_academic_unique RENAME TO br_cv_entries_year_academic');
        $this->addSql('ALTER INDEX year_person_unique RENAME TO cudi_isic_cards_person_academic_year');

        $this->addSql('ALTER INDEX acl_roles_inheritance_map_pkey RENAME TO acl_roles_parents_map_pkey');
        $this->addSql('ALTER INDEX br_collaborator_pkey RENAME TO br_collaborators_pkey');
        $this->addSql('ALTER INDEX br_companies_cvbooks_pkey RENAME TO br_companies_cv_book_years_map_pkey');
        $this->addSql('ALTER INDEX br_companies_request_internship_pkey RENAME TO br_companies_requests_internship_pkey');
        $this->addSql('ALTER INDEX br_companies_request_pkey RENAME TO br_companies_requests_pkey');
        $this->addSql('ALTER INDEX br_companies_request_vacancy_pkey RENAME TO br_companies_requests_vacancy_pkey');
        $this->addSql('ALTER INDEX br_contract_history_pkey RENAME TO br_contracts_history_pkey');
        $this->addSql('ALTER INDEX br_event_company_map_pkey RENAME TO br_events_companies_map_pkey');
        $this->addSql('ALTER INDEX br_invoice_history_pkey RENAME TO br_invoices_history_pkey');
        $this->addSql('ALTER INDEX br_invoices_contract_pkey RENAME TO br_invoices_contracts_pkey');
        $this->addSql('ALTER INDEX br_orders_entries_pkey RENAME TO br_products_orders_entries_pkey');
        $this->addSql('ALTER INDEX br_orders_pkey RENAME TO br_products_orders_pkey');
        $this->addSql('ALTER INDEX br_page_years_pkey RENAME TO br_companies_pages_years_map_pkey');
        $this->addSql('ALTER INDEX cudi_isic_card_pkey RENAME TO cudi_isic_cards_pkey');
        $this->addSql('ALTER INDEX cudi_log_articles_subject_map_add_pkey RENAME TO cudi_log_articles_subjects_map_added_pkey');
        $this->addSql('ALTER INDEX cudi_log_articles_subject_map_remove_pkey RENAME TO cudi_log_articles_subjects_map_removed_pkey');
        $this->addSql('ALTER INDEX cudi_log_sales_prof_version_pkey RENAME TO cudi_log_sales_prof_versions_pkey');
        $this->addSql('ALTER INDEX cudi_sales_articles_barcodes_pkey RENAME TO cudi_sale_articles_barcodes_pkey');
        $this->addSql('ALTER INDEX cudi_sales_articles_discounts_discounts_pkey RENAME TO cudi_sale_articles_discounts_discounts_pkey');
        $this->addSql('ALTER INDEX cudi_sales_articles_discounts_templates_pkey RENAME TO cudi_sale_articles_discounts_templates_pkey');
        $this->addSql('ALTER INDEX cudi_sales_articles_pkey RENAME TO cudi_sale_articles_pkey');
        $this->addSql('ALTER INDEX cudi_sales_articles_restrictions_amount_pkey RENAME TO cudi_sale_articles_restrictions_amount_pkey');
        $this->addSql('ALTER INDEX cudi_sales_articles_restrictions_available_pkey RENAME TO cudi_sale_articles_restrictions_available_pkey');
        $this->addSql('ALTER INDEX cudi_sales_articles_restrictions_member_pkey RENAME TO cudi_sale_articles_restrictions_member_pkey');
        $this->addSql('ALTER INDEX cudi_sales_articles_restrictions_pkey RENAME TO cudi_sale_articles_restrictions_pkey');
        $this->addSql('ALTER INDEX cudi_sales_articles_restrictions_study_map_pkey RENAME TO cudi_sale_articles_restrictions_studies_map_pkey');
        $this->addSql('ALTER INDEX cudi_sales_articles_restrictions_study_pkey RENAME TO cudi_sale_articles_restrictions_study_pkey');
        $this->addSql('ALTER INDEX cudi_sales_bookings_pkey RENAME TO cudi_sale_bookings_pkey');
        $this->addSql('ALTER INDEX cudi_sales_history_pkey RENAME TO cudi_sale_articles_history_pkey');
        $this->addSql('ALTER INDEX cudi_sales_pay_desks_pkey RENAME TO cudi_sale_pay_desks_pkey');
        $this->addSql('ALTER INDEX cudi_sales_queue_items_pkey RENAME TO cudi_sale_queue_items_pkey');
        $this->addSql('ALTER INDEX cudi_sales_return_items_pkey RENAME TO cudi_sale_return_items_pkey');
        $this->addSql('ALTER INDEX cudi_sales_sale_items_pkey RENAME TO cudi_sale_sale_items_pkey');
        $this->addSql('ALTER INDEX cudi_sales_session_openinghours_pkey RENAME TO cudi_sale_sessions_opening_hours_pkey');
        $this->addSql('ALTER INDEX cudi_sales_session_openinghours_translations_pkey RENAME TO cudi_sale_sessions_opening_hours_translations_pkey');
        $this->addSql('ALTER INDEX cudi_sales_session_restriction_name_pkey RENAME TO cudi_sale_sessions_restrictions_name_pkey');
        $this->addSql('ALTER INDEX cudi_sales_session_restriction_pkey RENAME TO cudi_sale_sessions_restrictions_pkey');
        $this->addSql('ALTER INDEX cudi_sales_session_restriction_study_pkey RENAME TO cudi_sale_sessions_restrictions_study_pkey');
        $this->addSql('ALTER INDEX cudi_sales_session_restriction_year_pkey RENAME TO cudi_sale_sessions_restrictions_year_pkey');
        $this->addSql('ALTER INDEX cudi_sales_session_restrictions_study_map_pkey RENAME TO cudi_sale_sessions_restrictions_studies_map_pkey');
        $this->addSql('ALTER INDEX cudi_sales_sessions_pkey RENAME TO cudi_sale_sessions_pkey');
        $this->addSql('ALTER INDEX forms_entries_pkey RENAME TO form_entries_pkey');
        $this->addSql('ALTER INDEX forms_fields_checkboxes_pkey RENAME TO form_fields_checkboxes_pkey');
        $this->addSql('ALTER INDEX forms_fields_dropdowns_pkey RENAME TO form_fields_dropdowns_pkey');
        $this->addSql('ALTER INDEX forms_fields_files_pkey RENAME TO form_fields_files_pkey');
        $this->addSql('ALTER INDEX forms_fields_options_pkey RENAME TO form_fields_options_pkey');
        $this->addSql('ALTER INDEX forms_fields_options_translations_pkey RENAME TO form_fields_translations_options_pkey');
        $this->addSql('ALTER INDEX forms_fields_pkey RENAME TO form_fields_pkey');
        $this->addSql('ALTER INDEX forms_fields_texts_pkey RENAME TO form_fields_texts_pkey');
        $this->addSql('ALTER INDEX forms_fields_timeslot_pkey RENAME TO form_fields_time_slots_pkey');
        $this->addSql('ALTER INDEX forms_fields_timeslots_translations_pkey RENAME TO form_fields_translations_time_slots_pkey');
        $this->addSql('ALTER INDEX forms_fields_translations_pkey RENAME TO form_fields_translations_pkey');
        $this->addSql('ALTER INDEX forms_guests_info_pkey RENAME TO nodes_forms_guests_info_pkey');
        $this->addSql('ALTER INDEX forms_mails_pkey RENAME TO form_mails_pkey');
        $this->addSql('ALTER INDEX forms_mails_translations_pkey RENAME TO form_mails_translations_pkey');
        $this->addSql('ALTER INDEX forms_viewers_pkey RENAME TO form_viewers_map_pkey');
        $this->addSql('ALTER INDEX gallery_photos_pkey RENAME TO gallery_albums_photos_pkey');
        $this->addSql('ALTER INDEX gallery_translations_pkey RENAME TO gallery_albums_translations_pkey');
        $this->addSql('ALTER INDEX general_address_cities_pkey RENAME TO general_addresses_cities_pkey');
        $this->addSql('ALTER INDEX general_address_streets_pkey RENAME TO general_addresses_streets_pkey');
        $this->addSql('ALTER INDEX general_promotions_academic_pkey RENAME TO secretary_promotions_academic_pkey');
        $this->addSql('ALTER INDEX general_promotions_external_pkey RENAME TO secretary_promotions_external_pkey');
        $this->addSql('ALTER INDEX general_promotions_pkey RENAME TO secretary_promotions_pkey');
        $this->addSql('ALTER INDEX logistics_drivers_years_pkey RENAME TO logistics_drivers_years_map_pkey');
        $this->addSql('ALTER INDEX logistics_lease_items_pkey RENAME TO logistics_leases_items_pkey');
        $this->addSql('ALTER INDEX logistics_lease_lease_pkey RENAME TO logistics_leases_pkey');
        $this->addSql('ALTER INDEX logistics_resources_pkey RENAME TO logistics_reservations_resources_pkey');
        $this->addSql('ALTER INDEX mail_lists_admin_roles_pkey RENAME TO mail_lists_admin_roles_map_pkey');
        $this->addSql('ALTER INDEX mail_lists_admins_pkey RENAME TO mail_lists_admins_map_pkey');
        $this->addSql('ALTER INDEX nodes_form_groups_mapping_pkey RENAME TO nodes_forms_groups_map_pkey');
        $this->addSql('ALTER INDEX nodes_form_groups_pkey RENAME TO nodes_forms_groups_pkey');
        $this->addSql('ALTER INDEX nodes_form_groups_translations_pkey RENAME TO nodes_forms_groups_translations_pkey');
        $this->addSql('ALTER INDEX nodes_forms_translations_pkey RENAME TO nodes_forms_forms_translations_pkey');
        $this->addSql('ALTER INDEX nodes_notification_translations_pkey RENAME TO nodes_notifications_translations_pkey');
        $this->addSql('ALTER INDEX prom_bus_code_academic_pkey RENAME TO prom_buses_reservation_codes_academic_pkey');
        $this->addSql('ALTER INDEX prom_bus_code_external_pkey RENAME TO prom_buses_reservation_codes_external_pkey');
        $this->addSql('ALTER INDEX prom_bus_code_pkey RENAME TO prom_buses_reservation_codes_pkey');
        $this->addSql('ALTER INDEX prom_bus_passenger_pkey RENAME TO prom_buses_passengers_pkey');
        $this->addSql('ALTER INDEX prom_bus_pkey RENAME TO prom_buses_pkey');
        $this->addSql('ALTER INDEX public_general_migrations_pkey RENAME TO general_migrations_pkey');
        $this->addSql('ALTER INDEX shifts_responsibles_pkey RENAME TO shift_shifts_responsibles_pkey');
        $this->addSql('ALTER INDEX shifts_shifts_pkey RENAME TO shift_shifts_pkey');
        $this->addSql('ALTER INDEX shifts_shifts_responsibles_map_pkey RENAME TO shift_shifts_responsibles_map_pkey');
        $this->addSql('ALTER INDEX shifts_shifts_roles_map_pkey RENAME TO shift_shifts_roles_map_pkey');
        $this->addSql('ALTER INDEX shifts_shifts_volunteers_map_pkey RENAME TO shift_shifts_volunteers_map_pkey');
        $this->addSql('ALTER INDEX shifts_volunteers_pkey RENAME TO shift_shifts_volunteers_pkey');
        $this->addSql('ALTER INDEX shop_reservation_permissions_pkey RENAME TO shop_reservations_permissions_pkey');
        $this->addSql('ALTER INDEX shop_session_stock_entries_pkey RENAME TO shop_sessions_stock_pkey');
        $this->addSql('ALTER INDEX syllabus_combination_module_group_map_pkey RENAME TO syllabus_combinations_module_groups_map_pkey');
        $this->addSql('ALTER INDEX syllabus_student_enrollment_pkey RENAME TO syllabus_subjects_student_enrollments_pkey');
        $this->addSql('ALTER INDEX syllabus_studies_group_map_pkey RENAME TO syllabus_studies_groups_map_pkey');
        $this->addSql('ALTER INDEX syllabus_study_combinations_pkey RENAME TO syllabus_studies_combinations_pkey');
        $this->addSql('ALTER INDEX syllabus_study_module_group_pkey RENAME TO syllabus_studies_module_groups_pkey');
        $this->addSql('ALTER INDEX syllabus_subjects_reply_pkey RENAME TO syllabus_subjects_replies_pkey');
        $this->addSql('ALTER INDEX tickets_events_options_pkey RENAME TO ticket_events_options_pkey');
        $this->addSql('ALTER INDEX tickets_events_pkey RENAME TO ticket_events_pkey');
        $this->addSql('ALTER INDEX tickets_guests_info_pkey RENAME TO ticket_guests_info_pkey');
        $this->addSql('ALTER INDEX tickets_tickets_pkey RENAME TO ticket_tickets_pkey');
        $this->addSql('ALTER INDEX users_corporate_statuses_pkey RENAME TO users_statuses_corporate_pkey');
        $this->addSql('ALTER INDEX users_organization_metadata_pkey RENAME TO users_organizations_metadata_pkey');
        $this->addSql('ALTER INDEX users_organization_statuses_pkey RENAME TO users_statuses_organization_pkey');
        $this->addSql('ALTER INDEX users_people_organizations_academic_year_map_pkey RENAME TO users_people_organizations_academic_years_map_pkey');
        $this->addSql('ALTER INDEX users_people_organizations_unit_map_academic_pkey RENAME TO users_people_organizations_units_map_academic_pkey');
        $this->addSql('ALTER INDEX users_people_organizations_unit_map_external_pkey RENAME TO users_people_organizations_units_map_external_pkey');
        $this->addSql('ALTER INDEX users_people_organizations_unit_map_pkey RENAME TO users_people_organizations_units_map_pkey');
        $this->addSql('ALTER INDEX users_people_shift_insurance_pkey RENAME TO shift_users_people_academic_years_map_pkey');
        $this->addSql('ALTER INDEX users_registrations_pkey RENAME TO secretary_registrations_pkey');
        $this->addSql('ALTER INDEX users_study_enrollment_pkey RENAME TO secretary_syllabus_enrollments_study_pkey');
        $this->addSql('ALTER INDEX users_subject_enrollment_pkey RENAME TO secretary_syllabus_enrollments_subject_pkey');
        $this->addSql('ALTER INDEX users_university_statuses_pkey RENAME TO users_statuses_university_pkey');

        $this->addSql('ALTER SEQUENCE br_collaborator_id_seq RENAME TO br_collaborators_id_seq');
        $this->addSql('ALTER SEQUENCE br_companies_request_id_seq RENAME TO br_companies_requests_id_seq');
        $this->addSql('ALTER SEQUENCE br_contract_history_id_seq RENAME TO br_contracts_history_id_seq');
        $this->addSql('ALTER SEQUENCE br_event_company_map_id_seq RENAME TO br_events_companies_map_id_seq');
        $this->addSql('ALTER SEQUENCE br_invoice_history_id_seq RENAME TO br_invoices_history_id_seq');
        $this->addSql('ALTER SEQUENCE br_orders_entries_id_seq RENAME TO br_products_orders_entries_id_seq');
        $this->addSql('ALTER SEQUENCE br_orders_id_seq RENAME TO br_products_orders_id_seq');
        $this->addSql('ALTER SEQUENCE cudi_isic_card_id_seq RENAME TO cudi_isic_cards_id_seq');
        $this->addSql('ALTER SEQUENCE cudi_sales_articles_barcodes_id_seq RENAME TO cudi_sale_articles_barcodes_id_seq');
        $this->addSql('ALTER SEQUENCE cudi_sales_articles_discounts_discounts_id_seq RENAME TO cudi_sale_articles_discounts_discounts_id_seq');
        $this->addSql('ALTER SEQUENCE cudi_sales_articles_discounts_templates_id_seq RENAME TO cudi_sale_articles_discounts_templates_id_seq');
        $this->addSql('ALTER SEQUENCE cudi_sales_articles_id_seq RENAME TO cudi_sale_articles_id_seq');
        $this->addSql('ALTER SEQUENCE cudi_sales_articles_restrictions_id_seq RENAME TO cudi_sale_articles_restrictions_id_seq');
        $this->addSql('ALTER SEQUENCE cudi_sales_bookings_id_seq RENAME TO cudi_sale_bookings_id_seq');
        $this->addSql('ALTER SEQUENCE cudi_sales_history_id_seq RENAME TO cudi_sale_articles_history_id_seq');
        $this->addSql('ALTER SEQUENCE cudi_sales_pay_desks_id_seq RENAME TO cudi_sale_pay_desks_id_seq');
        $this->addSql('ALTER SEQUENCE cudi_sales_queue_items_id_seq RENAME TO cudi_sale_queue_items_id_seq');
        $this->addSql('ALTER SEQUENCE cudi_sales_return_items_id_seq RENAME TO cudi_sale_return_items_id_seq');
        $this->addSql('ALTER SEQUENCE cudi_sales_sale_items_id_seq RENAME TO cudi_sale_sale_items_id_seq');
        $this->addSql('ALTER SEQUENCE cudi_sales_session_openinghours_id_seq RENAME TO cudi_sale_sessions_opening_hours_id_seq');
        $this->addSql('ALTER SEQUENCE cudi_sales_session_openinghours_translations_id_seq RENAME TO cudi_sale_sessions_opening_hours_translations_id_seq');
        $this->addSql('ALTER SEQUENCE cudi_sales_session_restriction_id_seq RENAME TO cudi_sale_sessions_restrictions_id_seq');
        $this->addSql('ALTER SEQUENCE cudi_sales_sessions_id_seq RENAME TO cudi_sale_sessions_id_seq');
        $this->addSql('ALTER SEQUENCE forms_fields_id_seq RENAME TO form_fields_id_seq');
        $this->addSql('ALTER SEQUENCE forms_fields_options_translations_id_seq RENAME TO form_fields_translations_options_id_seq');
        $this->addSql('ALTER SEQUENCE forms_fields_timeslots_translations_id_seq RENAME TO form_fields_translations_time_slots_id_seq');
        $this->addSql('ALTER SEQUENCE forms_fields_translations_id_seq RENAME TO form_fields_translations_id_seq');
        $this->addSql('ALTER SEQUENCE forms_guests_info_id_seq RENAME TO nodes_forms_guests_info_id_seq');
        $this->addSql('ALTER SEQUENCE forms_mails_id_seq RENAME TO form_mails_id_seq');
        $this->addSql('ALTER SEQUENCE forms_mails_translations_id_seq RENAME TO form_mails_translations_id_seq');
        $this->addSql('ALTER SEQUENCE forms_viewers_id_seq RENAME TO form_viewers_map_id_seq');
        $this->addSql('ALTER SEQUENCE gallery_photos_id_seq RENAME TO gallery_albums_photos_id_seq');
        $this->addSql('ALTER SEQUENCE gallery_translations_id_seq RENAME TO gallery_albums_translations_id_seq');
        $this->addSql('ALTER SEQUENCE general_address_cities_id_seq RENAME TO general_addresses_cities_id_seq');
        $this->addSql('ALTER SEQUENCE general_address_streets_id_seq RENAME TO general_addresses_streets_id_seq');
        $this->addSql('ALTER SEQUENCE general_promotions_id_seq RENAME TO secretary_promotions_id_seq');
        $this->addSql('ALTER SEQUENCE logistics_lease_items_id_seq RENAME TO logistics_leases_items_id_seq');
        $this->addSql('ALTER SEQUENCE logistics_lease_lease_id_seq RENAME TO logistics_leases_id_seq');
        $this->addSql('ALTER SEQUENCE mail_lists_admin_roles_id_seq RENAME TO mail_lists_admin_roles_map_id_seq');
        $this->addSql('ALTER SEQUENCE mail_lists_admins_id_seq RENAME TO mail_lists_admins_map_id_seq');
        $this->addSql('ALTER SEQUENCE nodes_form_groups_mapping_id_seq RENAME TO nodes_forms_groups_map_id_seq');
        $this->addSql('ALTER SEQUENCE nodes_form_groups_translations_id_seq RENAME TO nodes_forms_groups_translations_id_seq');
        $this->addSql('ALTER SEQUENCE nodes_forms_translations_id_seq RENAME TO nodes_forms_forms_translations_id_seq');
        $this->addSql('ALTER SEQUENCE nodes_notification_translations_id_seq RENAME TO nodes_notifications_translations_id_seq');
        $this->addSql('ALTER SEQUENCE prom_bus_code_id_seq RENAME TO prom_buses_reservation_codes_id_seq');
        $this->addSql('ALTER SEQUENCE prom_bus_id_seq RENAME TO prom_buses_id_seq');
        $this->addSql('ALTER SEQUENCE prom_bus_passenger_id_seq RENAME TO prom_buses_passengers_id_seq');
        $this->addSql('ALTER SEQUENCE shifts_responsibles_id_seq RENAME TO shift_shifts_responsibles_id_seq');
        $this->addSql('ALTER SEQUENCE shifts_shifts_id_seq RENAME TO shift_shifts_id_seq');
        $this->addSql('ALTER SEQUENCE shifts_volunteers_id_seq RENAME TO shift_shifts_volunteers_id_seq');
        $this->addSql('ALTER SEQUENCE syllabus_student_enrollment_id_seq RENAME TO syllabus_subjects_student_enrollments_id_seq');
        $this->addSql('ALTER SEQUENCE syllabus_studies_group_map_id_seq RENAME TO syllabus_studies_groups_map_id_seq');
        $this->addSql('ALTER SEQUENCE syllabus_study_combinations_id_seq RENAME TO syllabus_studies_combinations_id_seq');
        $this->addSql('ALTER SEQUENCE syllabus_study_module_group_id_seq RENAME TO syllabus_studies_module_groups_id_seq');
        $this->addSql('ALTER SEQUENCE syllabus_subjects_reply_id_seq RENAME TO syllabus_subjects_replies_id_seq');
        $this->addSql('ALTER SEQUENCE tickets_events_id_seq RENAME TO ticket_events_id_seq');
        $this->addSql('ALTER SEQUENCE tickets_events_options_id_seq RENAME TO ticket_events_options_id_seq');
        $this->addSql('ALTER SEQUENCE tickets_guests_info_id_seq RENAME TO ticket_guests_info_id_seq');
        $this->addSql('ALTER SEQUENCE tickets_tickets_id_seq RENAME TO ticket_tickets_id_seq');
        $this->addSql('ALTER SEQUENCE users_corporate_statuses_id_seq RENAME TO users_statuses_corporate_id_seq');
        $this->addSql('ALTER SEQUENCE users_organization_metadata_id_seq RENAME TO users_organizations_metadata_id_seq');
        $this->addSql('ALTER SEQUENCE users_organization_statuses_id_seq RENAME TO users_statuses_organization_id_seq');
        $this->addSql('ALTER SEQUENCE users_people_organizations_academic_year_map_id_seq RENAME TO users_people_organizations_academic_years_map_id_seq');
        $this->addSql('ALTER SEQUENCE users_people_organizations_unit_map_id_seq RENAME TO users_people_organizations_units_map_id_seq');
        $this->addSql('ALTER SEQUENCE users_people_shift_insurance_id_seq RENAME TO shift_users_people_academic_years_map_id_seq');
        $this->addSql('ALTER SEQUENCE users_registrations_id_seq RENAME TO secretary_registrations_id_seq');
        $this->addSql('ALTER SEQUENCE users_study_enrollment_id_seq RENAME TO secretary_syllabus_enrollments_study_id_seq');
        $this->addSql('ALTER SEQUENCE users_subject_enrollment_id_seq RENAME TO secretary_syllabus_enrollments_subject_id_seq');
        $this->addSql('ALTER SEQUENCE users_university_statuses_id_seq RENAME TO users_statuses_university_id_seq');

        $this->addSql('ALTER TABLE acl_roles_inheritance_map RENAME TO acl_roles_parents_map');
        $this->addSql('ALTER TABLE br_collaborator RENAME TO br_collaborators');
        $this->addSql('ALTER TABLE br_companies_cvbooks RENAME TO br_companies_cv_book_years_map');
        $this->addSql('ALTER TABLE br_companies_request RENAME TO br_companies_requests');
        $this->addSql('ALTER TABLE br_companies_request_internship RENAME TO br_companies_requests_internship');
        $this->addSql('ALTER TABLE br_companies_request_vacancy RENAME TO br_companies_requests_vacancy');
        $this->addSql('ALTER TABLE br_contract_history RENAME TO br_contracts_history');
        $this->addSql('ALTER TABLE br_event_company_map RENAME TO br_events_companies_map');
        $this->addSql('ALTER TABLE br_invoice_history RENAME TO br_invoices_history');
        $this->addSql('ALTER TABLE br_invoices_contract RENAME TO br_invoices_contracts');
        $this->addSql('ALTER TABLE br_orders RENAME TO br_products_orders');
        $this->addSql('ALTER TABLE br_orders_entries RENAME TO br_products_orders_entries');
        $this->addSql('ALTER TABLE br_page_years RENAME TO br_companies_pages_years_map');
        $this->addSql('ALTER TABLE cudi_isic_card RENAME TO cudi_isic_cards');
        $this->addSql('ALTER TABLE cudi_log_articles_subject_map_add RENAME TO cudi_log_articles_subjects_map_added');
        $this->addSql('ALTER TABLE cudi_log_articles_subject_map_remove RENAME TO cudi_log_articles_subjects_map_removed');
        $this->addSql('ALTER TABLE cudi_log_sales_prof_version RENAME TO cudi_log_sales_prof_versions');
        $this->addSql('ALTER TABLE cudi_sales_articles RENAME TO cudi_sale_articles');
        $this->addSql('ALTER TABLE cudi_sales_articles_barcodes RENAME TO cudi_sale_articles_barcodes');
        $this->addSql('ALTER TABLE cudi_sales_articles_discounts_discounts RENAME TO cudi_sale_articles_discounts_discounts');
        $this->addSql('ALTER TABLE cudi_sales_articles_discounts_templates RENAME TO cudi_sale_articles_discounts_templates');
        $this->addSql('ALTER TABLE cudi_sales_articles_restrictions RENAME TO cudi_sale_articles_restrictions');
        $this->addSql('ALTER TABLE cudi_sales_articles_restrictions_amount RENAME TO cudi_sale_articles_restrictions_amount');
        $this->addSql('ALTER TABLE cudi_sales_articles_restrictions_available RENAME TO cudi_sale_articles_restrictions_available');
        $this->addSql('ALTER TABLE cudi_sales_articles_restrictions_member RENAME TO cudi_sale_articles_restrictions_member');
        $this->addSql('ALTER TABLE cudi_sales_articles_restrictions_study RENAME TO cudi_sale_articles_restrictions_study');
        $this->addSql('ALTER TABLE cudi_sales_articles_restrictions_study_map RENAME TO cudi_sale_articles_restrictions_studies_map');
        $this->addSql('ALTER TABLE cudi_sales_bookings RENAME TO cudi_sale_bookings');
        $this->addSql('ALTER TABLE cudi_sales_history RENAME TO cudi_sale_articles_history');
        $this->addSql('ALTER TABLE cudi_sales_pay_desks RENAME TO cudi_sale_pay_desks');
        $this->addSql('ALTER TABLE cudi_sales_queue_items RENAME TO cudi_sale_queue_items');
        $this->addSql('ALTER TABLE cudi_sales_return_items RENAME TO cudi_sale_return_items');
        $this->addSql('ALTER TABLE cudi_sales_sale_items RENAME TO cudi_sale_sale_items');
        $this->addSql('ALTER TABLE cudi_sales_session_openinghours RENAME TO cudi_sale_sessions_opening_hours');
        $this->addSql('ALTER TABLE cudi_sales_session_openinghours_translations RENAME TO cudi_sale_sessions_opening_hours_translations');
        $this->addSql('ALTER TABLE cudi_sales_session_restriction RENAME TO cudi_sale_sessions_restrictions');
        $this->addSql('ALTER TABLE cudi_sales_session_restriction_name RENAME TO cudi_sale_sessions_restrictions_name');
        $this->addSql('ALTER TABLE cudi_sales_session_restriction_study RENAME TO cudi_sale_sessions_restrictions_study');
        $this->addSql('ALTER TABLE cudi_sales_session_restriction_year RENAME TO cudi_sale_sessions_restrictions_year');
        $this->addSql('ALTER TABLE cudi_sales_session_restrictions_study_map RENAME TO cudi_sale_sessions_restrictions_studies_map');
        $this->addSql('ALTER TABLE cudi_sales_sessions RENAME TO cudi_sale_sessions');
        $this->addSql('ALTER TABLE forms_entries RENAME TO form_entries');
        $this->addSql('ALTER TABLE forms_fields RENAME TO form_fields');
        $this->addSql('ALTER TABLE forms_fields_checkboxes RENAME TO form_fields_checkboxes');
        $this->addSql('ALTER TABLE forms_fields_dropdowns RENAME TO form_fields_dropdowns');
        $this->addSql('ALTER TABLE forms_fields_files RENAME TO form_fields_files');
        $this->addSql('ALTER TABLE forms_fields_options RENAME TO form_fields_options');
        $this->addSql('ALTER TABLE forms_fields_options_translations RENAME TO form_fields_translations_options');
        $this->addSql('ALTER TABLE forms_fields_texts RENAME TO form_fields_texts');
        $this->addSql('ALTER TABLE forms_fields_timeslot RENAME TO form_fields_time_slots');
        $this->addSql('ALTER TABLE forms_fields_timeslots_translations RENAME TO form_fields_translations_time_slots');
        $this->addSql('ALTER TABLE forms_fields_translations RENAME TO form_fields_translations');
        $this->addSql('ALTER TABLE forms_guests_info RENAME TO nodes_forms_guests_info');
        $this->addSql('ALTER TABLE forms_mails RENAME TO form_mails');
        $this->addSql('ALTER TABLE forms_mails_translations RENAME TO form_mails_translations');
        $this->addSql('ALTER TABLE forms_viewers RENAME TO form_viewers_map');
        $this->addSql('ALTER TABLE gallery_photos RENAME TO gallery_albums_photos');
        $this->addSql('ALTER TABLE gallery_translations RENAME TO gallery_albums_translations');
        $this->addSql('ALTER TABLE general_address_cities RENAME TO general_addresses_cities');
        $this->addSql('ALTER TABLE general_address_streets RENAME TO general_addresses_streets');
        $this->addSql('ALTER TABLE general_promotions RENAME TO secretary_promotions');
        $this->addSql('ALTER TABLE general_promotions_academic RENAME TO secretary_promotions_academic');
        $this->addSql('ALTER TABLE general_promotions_external RENAME TO secretary_promotions_external');
        $this->addSql('ALTER TABLE logistics_drivers_years RENAME TO logistics_drivers_years_map');
        $this->addSql('ALTER TABLE logistics_lease_items RENAME TO logistics_leases_items');
        $this->addSql('ALTER TABLE logistics_lease_lease RENAME TO logistics_leases');
        $this->addSql('ALTER TABLE logistics_resources RENAME TO logistics_reservations_resources');
        $this->addSql('ALTER TABLE mail_lists_admin_roles RENAME TO mail_lists_admin_roles_map');
        $this->addSql('ALTER TABLE mail_lists_admins RENAME TO mail_lists_admins_map');
        $this->addSql('ALTER TABLE nodes_form_groups RENAME TO nodes_forms_groups');
        $this->addSql('ALTER TABLE nodes_form_groups_mapping RENAME TO nodes_forms_groups_map');
        $this->addSql('ALTER TABLE nodes_form_groups_translations RENAME TO nodes_forms_groups_translations');
        $this->addSql('ALTER TABLE nodes_forms_translations RENAME TO nodes_forms_forms_translations');
        $this->addSql('ALTER TABLE nodes_notification_translations RENAME TO nodes_notifications_translations');
        $this->addSql('ALTER TABLE prom_bus RENAME TO prom_buses');
        $this->addSql('ALTER TABLE prom_bus_code RENAME TO prom_buses_reservation_codes');
        $this->addSql('ALTER TABLE prom_bus_code_academic RENAME TO prom_buses_reservation_codes_academic');
        $this->addSql('ALTER TABLE prom_bus_code_external RENAME TO prom_buses_reservation_codes_external');
        $this->addSql('ALTER TABLE prom_bus_passenger RENAME TO prom_buses_passengers');
        $this->addSql('ALTER TABLE shifts_responsibles RENAME TO shift_shifts_responsibles');
        $this->addSql('ALTER TABLE shifts_shifts RENAME TO shift_shifts');
        $this->addSql('ALTER TABLE shifts_shifts_responsibles_map RENAME TO shift_shifts_responsibles_map');
        $this->addSql('ALTER TABLE shifts_shifts_roles_map RENAME TO shift_shifts_roles_map');
        $this->addSql('ALTER TABLE shifts_shifts_volunteers_map RENAME TO shift_shifts_volunteers_map');
        $this->addSql('ALTER TABLE shifts_volunteers RENAME TO shift_shifts_volunteers');
        $this->addSql('ALTER TABLE shop_reservation_permissions RENAME TO shop_reservations_permissions');
        $this->addSql('ALTER TABLE shop_session_stock_entries RENAME TO shop_sessions_stock');
        $this->addSql('ALTER TABLE syllabus_combination_module_group_map RENAME TO syllabus_combinations_module_groups_map');
        $this->addSql('ALTER TABLE syllabus_student_enrollment RENAME TO syllabus_subjects_student_enrollments');
        $this->addSql('ALTER TABLE syllabus_studies_group_map RENAME TO syllabus_studies_groups_map');
        $this->addSql('ALTER TABLE syllabus_study_combinations RENAME TO syllabus_studies_combinations');
        $this->addSql('ALTER TABLE syllabus_study_module_group RENAME TO syllabus_studies_module_groups');
        $this->addSql('ALTER TABLE syllabus_subjects_reply RENAME TO syllabus_subjects_replies');
        $this->addSql('ALTER TABLE tickets_events RENAME TO ticket_events');
        $this->addSql('ALTER TABLE tickets_events_options RENAME TO ticket_events_options');
        $this->addSql('ALTER TABLE tickets_guests_info RENAME TO ticket_guests_info');
        $this->addSql('ALTER TABLE tickets_tickets RENAME TO ticket_tickets');
        $this->addSql('ALTER TABLE users_corporate_statuses RENAME TO users_statuses_corporate');
        $this->addSql('ALTER TABLE users_organization_metadata RENAME TO users_organizations_metadata');
        $this->addSql('ALTER TABLE users_organization_statuses RENAME TO users_statuses_organization');
        $this->addSql('ALTER TABLE users_people_organizations_academic_year_map RENAME TO users_people_organizations_academic_years_map');
        $this->addSql('ALTER TABLE users_people_organizations_unit_map RENAME TO users_people_organizations_units_map');
        $this->addSql('ALTER TABLE users_people_organizations_unit_map_academic RENAME TO users_people_organizations_units_map_academic');
        $this->addSql('ALTER TABLE users_people_organizations_unit_map_external RENAME TO users_people_organizations_units_map_external');
        $this->addSql('ALTER TABLE users_people_shift_insurance RENAME TO shift_users_people_academic_years_map');
        $this->addSql('ALTER TABLE users_registrations RENAME TO secretary_registrations');
        $this->addSql('ALTER TABLE users_study_enrollment RENAME TO secretary_syllabus_enrollments_study');
        $this->addSql('ALTER TABLE users_subject_enrollment RENAME TO secretary_syllabus_enrollments_subject');
        $this->addSql('ALTER TABLE users_university_statuses RENAME TO users_statuses_university');
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
