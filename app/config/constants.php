<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

define("PHPASS_HASH_STRENGTH", 8);
define('PHPASS_HASH_PORTABLE', FALSE);
if (defined('TIMEZONE')) {
    date_default_timezone_set(TIMEZONE);
}
define("TEMP_PATH", APPPATH . "../assets/tmp");
define('DEMO_VERSION', FALSE);
define("SCRIP_APP_ID", 23595718);
define('CHILD_PANEL', FALSE);
define("NOW", date("Y-m-d H:i:s"));

define("FILE_MANAGER", "general_file_manager");

define("CATEGORIES", "categories");
define("API_PROVIDERS", "api_providers");
define("SERVICES", "services");
define("ORDER", "orders");
define("ORDERS_REFILL", "orders_refill");
define("SUBSCRIPTIONS", "subscriptions");
define("FAQS", "faqs");
define("TICKETS", "tickets");
define("TICKET_MESSAGES", "ticket_messages");

define("NEWS", "general_news");
define("COUPONS", "general_coupons");
define("OPTIONS", "general_options");
define("TRANSACTION_LOGS", "general_transaction_logs");
define("PURCHASE", "general_purchase");
define("LANGUAGE", "general_lang");
define("LANGUAGE_LIST", "general_lang_list");

define("USERS", "general_users");
define("USERS_PRICE", "general_users_price");
define("USER_LOGS", "general_user_logs");
define("USER_BLOCK_IP", "general_user_block_ip");
define("USER_MAIL_LOGS", "general_user_mail_logs");
define("SUBSCRIBERS", "general_subscribers");

define("STAFFS", "general_staffs");
define("STAFFS_LOGS", "general_staffs_logs");
define("ROLE_PERMISSIONS", "general_role_permission");

define("PAYMENTS_METHOD", "payments");
define("PAYMENTS_BONUSES", "payments_bonus");

// affiliate
define("AFFILIATE", "affiliate");
define("AFFILIATE_PAYOUT", "affiliate_payout");
define("AFFILIATE_REFERRAL", "affiliate_referral");

// Blacklist
define("BLACKLIST_IP", "general_blacklist_ip");
define("BLACKLIST_EMAIL", "general_blacklist_email");
define("BLACKLIST_LINK", "general_blacklist_link");


// BLogs
define("BLOG_CATEGORIES", "blog_categories");
define("BLOG_POSTS", "blog_posts");
define("BLOG_POSTS_LANG", "blog_posts_lang");

if (!defined('CUSTOM_PAGE')) {
    define("CUSTOM_PAGE", "general_custom_page");
}