<?php
/**
 * Set the Templete-Directory
 */
define("TEMPLATE_DIR", "customerportal");

/**
 * Activate extended error messages
 */
define("DEBUG", 1);

/**
 * URL to Customer Portal
 *
 * examples:
 * http://customerportal.domain.tld/
 * http://domain.tld/customerportal/
 * https://customerportal.domain.tld/
 * /customerportal/
 */
define("CUSTOMER_PORTAL_URL", "/customerPortal/");

/**
 * With this SALT the LOGIN to the vtigerCRM sytem will be secured
 * It has to be equal to the SECURITY_SALT of config.inc.php inside CustomerPortal2 Extension
 */
define("SECURITY_SALT", '3w09fH2N');

/**
 * Define for default Template, to show your company name
 */
define("COMPANY_NAME", "Customer Portal");

$templateSettings = array(
    "logoUrl" => "https://www.vtiger.com/crm/images/frontpagelogo/vtiger%20logo2.png",
    "headerText" => "Customerportal of ".COMPANY_NAME,
);


/**
 * To allow different CustomerPortals for one vtiger system with different fieldconfiguration
 *
 * Not used at this moment, because the administration is missing this feature
 * MUST SET TO: CUSTOMERPORTAL_ID
 */
define("CUSTOMERPORTAL_ID", "CUSTOMERPORTAL_ID");


/**
 * Default Language
 */
define('DEFAULT_LOCALE', 'en_US');

/**
 * Languages to Select
 */
$supported_locales = array(
    "EN" => 'en_US',
    "DE" => 'de_DE'
);

/**
 * Main Navigation Fields
 */
$mainNavigation = array(
    "MENU_CONTACT_DATA" => "contact.html",
    "MENU_DOCUMENTS_DATA" => "documents.html",
    "MENU_ACCOUNT_DATA" => "organization.html",
    #"MENU_INVOICE_DATA" => "invoice_list.html",
    "MENU_TICKETS_DATA" => "tickets.html"
);

/**
 * Url to redirect after Login
 */
define("REDIRECT_AFTER_LOGIN", "home.html");

/**
 * Connection to vtiger Webservice URL
 *
 * url - URL to the vtiger System
 * username - Username, that shoud use to connecto to webservice
 * accesskey - "remove access key" for this user
 */
global $connectionSettings;
$connectionSettings = array(
    "url" => "<urlToVtiger>",
    "username" => "<username>",
    "accesskey" => "<accesskey>"
);

/**
 * Use this Auth-Method to get access to your vtigerCRM (none|basic|digest|certificate)
 */
$http_auth = "none";

/*
	Uncomment this for basic http auth:

$http_auth_credentials = array(
	"username" => "swarnat",
	"password" => "stefan34"
);
*/

/*
	Uncomment this for digest http auth

$http_auth_credentials = array(
	"username" => "swarnat",
	"password" => "stefan"
);
*/

/*
	Uncomment this for digest http auth

    You have to create two files with private key and public key in pem format

$http_auth_credentials = array(
	"sslcertfile" => dirname(__FILE__)."/publicKey.pem",
	"sslkeyfile" => dirname(__FILE__)."/privateKey.pem",
	"passphrase" => "asdf",
);
*/

/*
 * Uncomment this for usage of http proxy

$http_proxy = array(
    "url" => "",
    "port" => 8080,
    "proxy_user" => "",
    "proxy_password" => ""
);
*/

/**
 * Enable fileupload from customerportal
 */
define("DOCUMENTS_UPLOAD", true);

/**
 * Admin eMail to receive system errors
 */
define("ADMIN_MAIL", "admin@example.com");

/**
 * Sender eMail for system mails
 */
define("ADMIN_MAIL_SENDER", "admin@example.com");

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
/**
 * Have to set to the PHP.ini Setting of your Session Cookie
 */
define("VTIGER_SESSION_VARIABLE", "PHPSESSID");

/**
 * Increase the Session Security Check
 * Should be set to true, to prevent Session Hijacking
 */
define('HIGH_SESSION_SECURITY', true);