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


/*authorize.net setting start*/
define('AUTHORIZE_VERSION','3.1');
define('AUTHORIZE_DELIMITER','|');
define('AUTHORIZE_DELIMIT_DATA',"TRUE");
define('AUTHORIZE_URL_FLAG',"FALSE");

define('AUTHORIZE_TEST_REQUEST',"TRUE");//it must be TRUE for test auth account
define('AUTHORIZE_ARB_TEST_REQUEST',TRUE);//it must be TRUE for test auth account

//TEST AUTH ACCOUNT
define('AUTHORIZE_LOGIN_NAME',"8Q87hKq5");
define('AUTHORIZE_LOGIN_PASSWORD',"9C88H4MpP4AtgU8F");
define('AUTHORIZE_TRANSACTION_KEY',"9C88H4MpP4AtgU8F");
define('VALIDATION_MODE','testMode');//testMode,liveMode

// Live Account
/*define('AUTHORIZE_LOGIN_NAME',"82DL3cquv");
define('AUTHORIZE_LOGIN_PASSWORD',"2XgKz226Wbp527Ag");
define('AUTHORIZE_TRANSACTION_KEY',"2XgKz226Wbp527Ag");
define('VALIDATION_MODE','liveMode');//testMode,liveMode*/

define('CREDIT_CARD_US','16001');
define('WESTERN_UNION_US','16003');
define('MONEY_ORDER_US','16004');
define('PAYPAL_US','16005');
define('WIRE_US','16009');

define('CREDIT_CARD_INTERNATINAL','16002');
define('WESTERN_UNION_INTERNATINAL','16006');
define('MONEY_ORDER_INTERNATINAL','16007');
define('PAYPAL_INTERNATINAL','16008');
define('WIRE_INTERNATINAL','16010');
define('NON_CREDIT_CARD','nonCreditCard');
/*authorize.net setting end*/

define('CUSTOMER', 'customer');
define('SUBSCRIPTIONS','order');

// Dev Product Page URL
//define('PRODUCT_PAGE_URL','https://healthspan-2.myshopify.com/products/tru-niagen-product');

// Live Product Page URL
define('PRODUCT_PAGE_URL','https://prohealthspan.com/products/tru-niagen-2');

//Sandbox Details.
define('PAYPAL_USERNAME', 'subhadip.mondal-facilitator_api1.codaemonsoftwares.com');
define('PAYPAL_PASSWORD', '6NPQHNMRN5TTZDLC');
define('PAYPAL_SIGNATURE', 'AFcWxV21C7fd0v3bYYYRCpSSRl31A1nZNjUYI8iMxkHfLnXyhTRLdGVC');

// Live PayPal Details
/*define('PAYPAL_USERNAME', 'info_api1.prohealthspan.com');
define('PAYPAL_PASSWORD', 'MW3LUUYXCMXEFF74');
define('PAYPAL_SIGNATURE', 'AFcWxV21C7fd0v3bYYYRCpSSRl31AWwWYiqb7feVM6G7tmB2gvCQPnwr');*/

//Shopify API details
// Dev Server details.
define('SHOPIFY_DOMAIN','healthspan-2.myshopify.com');
define('SHOPIFY_API_KEY','289c8c53a952805cb65cbf580e93d134');
define('SHOPIFY_PASSWORD','3e49c812b1303b64f7f6b0c3dbc79da0');

// Live server details.
/*define('SHOPIFY_DOMAIN','nr-supplement.myshopify.com');
define('SHOPIFY_API_KEY','fc01d8fadbc97983660e1489015f7648');
define('SHOPIFY_PASSWORD','ab1fa57f5cc6bdbde9816a2305f3aff3');
//Shipping Price
*/
//Klaviyo api key
define('KLAVIYO_PUBLIC_KEY','k6iMnq');
define('KLAVIYO_API_KEY','pk_1869358f251493e7c0a1fee7c60cb02381');

//UPS shipping api account
define('UPS_ACCOUNT_API_KEY','');