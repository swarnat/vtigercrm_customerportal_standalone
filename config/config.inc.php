<?php
/**
 * Set the Templete-Directory
 */
define("TEMPLATE_DIR", "customerportal");

/**
 * Absolute URL to Customer Portal
 *
 * examples:
 * http://customerportal.domain.tld/
 * http://domain.tld/customerportal/
 * https://customerportal.domain.tld/
 */
define("CUSTOMER_PORTAL_URL", "http://localhost/customerPortal/");

/**
 * Please change this and use all available chars (a-z, A-Z, 0-9, special chars ...)
 */
define("SECURITY_SALT", '3w09fH2N');

/**
 * Define for default Template, to show your company name
 */
define("COMPANY_NAME", "Customer Portal");

$templateSettings = array(
    "logoUrl" => "http://www.praktika.de/styles/images/logo.png",
    "headerText" => "CustomerPortal",
);

/**
 * Default Language
 */
define('DEFAULT_LOCALE', 'de_DE');

/**
 * To allow different CustomerPortals for one vtiger system with different fieldconfiguration
 */
define("CUSTOMERPORTAL_ID", "CUSTOMERPORTAL_ID");
/**
 * Languages to Select
 */
$supported_locales = array(
    "EN" => 'en_US',
    "DE" => 'de_DE'
);

define("REDIRECT_AFTER_LOGIN", "home.html");

define('HIGH_SESSION_SECURITY', true);

/**
 * Connection to vtiger Webservice URL
 *
 * url - URL to the vtiger System
 * username - Username, that shoud use to connecto to webservice
 * accesskey - "remove access key" for this user
 */
global $connectionSettings;
$connectionSettings = array(
    "url" => "http://localhost/vtiger/5.4.0/",
    "username" => "customerportal",
    "accesskey" => "1rvw2qSv6UTHzFjE"
);

/**
 * Main Navigation Fields
 */
$mainNavigation = array(
    "MENU_CONTACT_DATA" => "contact.html",
    "MENU_DOCUMENTS_DATA" => "documents.html",
    "MENU_ACCOUNT_DATA" => "organization.html",
    "MENU_INVOICE_DATA" => "invoice_list.html",
    "MENU_TICKETS_DATA" => "tickets.html"
);

/**
 * Different Configurations, you have to setup before all values are display correctly
 */
$moduleConfigurations = array(
    "Invoice" => array(
        "field_invoice_number" => "invoice_number",         # Field of Invoice Number
        "field_invoice_date" => "invoice_booking_date",     # Field of Invoice Booking Date
    )
);

/* Expert Only !!! */
define("VTIGER_SESSION_VARIABLE", "PHPSESSID");