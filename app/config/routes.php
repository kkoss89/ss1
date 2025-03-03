<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller']                    = 'home';
$route['404_override']                          = 'custom_page/page_404';
$route['translate_uri_dashes']                  = FALSE;
$route['set_language']                          = 'blocks/set_language';

// Settings page
$route['new_order']                             = 'order/new_order';
$route['tickets/(:num)'] = 'tickets/view/$1';

// client area
$route['login']                   = 'auth/login';
$route['faq']                     = 'client/faq';
$route['terms']                   = 'client/terms';
$route['impressum']               = 'client/impressum';
$route['cookie-policy']           = 'client/cookie_policy';
$route['news-annoucement']        = 'client/news_annoucement';
$route['set-language']            = 'client/set_language';
$route['back-to-admin']           = 'client/back_to_admin';
$route['ref/(:any)']              = 'client/referral/$1';

// Client Blog
$route['blog']                                  = 'blog/index';
$route['blog/(:any)']                           = 'blog/detail/$1';
$route['blog/category/(:any)']                  = 'blog/category/$1';

// Payment IPN
$route['unitpay_ipn'] 	    = 'add_funds/unitpay/unitpay_ipn/';
$route['cashmaal_ipn'] 		= 'add_funds/cashmaal/cashmaal_ipn/';
$route['ehot_ipn'] 			= 'add_funds/ehot/ipn/';
$route['gbprimepay_ipn']    = 'add_funds/gbprimepay/gbprimepay_ipn/';
$route['nowpayments_ipn']   = 'add_funds/nowpayments/nowpayments_ipn/';
$route['mercadopago_ipn']   = 'add_funds/mercadopago/ipn';
$route['payhere_ipn']       = 'add_funds/payhere/ipn/';
$route['mercadopago_ipn']   = 'add_funds/mercadopago/ipn/';
$route['payzah_ipn']        = 'add_funds/payzah/ipn/';
$route['payku_ipn']         = 'add_funds/payku/ipn/';
$route['coinpayments_ipn']  = 'add_funds/coinpayments/ipn/';
$route['coinbase_ipn']      = 'add_funds/coinbase/ipn/';
$route['cardlink_ipn']      = 'add_funds/cardlink/ipn/';
$route['flutterwave_ipn']   = 'add_funds/flutterwave/ipn/';
$route['razorpay_ipn']      = 'add_funds/razorpay/ipn/';
$route['epayco_ipn']        = 'add_funds/epayco/ipn/';
$route['webmoney_ipn']      = 'add_funds/webmoney/ipn/';
$route['mpesa_ipn']         = 'add_funds/mpesa_/ipn/';
$route['paytr_ipn']         = 'add_funds/paytr/ipn/';
$route['ecpay_ipn']         = 'add_funds/ecpay/ipn/';
$route['xunhupay_ipn']      = 'add_funds/xunhupay/ipn/';
$route['paysky_ipn']        = 'add_funds/paysky/ipn/';
$route['youcanpay_ipn']     = 'add_funds/youcanpay/ipn/';
$route['(:any)_ipn']        = 'add_funds/$1/ipn/';


//$route['cron/pix']     					= 'add_funds/pix/list_pix';


// payment cron
$route['coinpayments/cron']             = 'add_funds/coinpayments/cron';
$route['coinbase/cron']                 = 'add_funds/coinbase/cron';
$route['payop/cron']                    = 'add_funds/payop/cron';
$route['midtrans/cron']                 = 'add_funds/midtrans/cron';
$route['paymongo/cron']                 = 'add_funds/paymongo/cron';
$route['payku/cron']                    = 'add_funds/payku/cron';
$route['mercadopago/cron']              = 'add_funds/mercadopago/cron';


// API provider cron
$route['api_provider/cron/order']                    = 'cron/order';
$route['api_provider/cron/status']                   = 'cron/multiple_status';
$route['api_provider/cron/status_subscriptions']     = 'cron/status_subscriptions';
$route['cron/refill_status']            = 'refill/cron';
$route['cron/sync_services']            = 'cron/provider/sync_services';
$route['cron/update_balance']           = 'cron/provider/update_balance';

// 

$route['cron/pix']                                   = 'add_funds/pix/list_pix';
$route['cron/pagseguro']                             = 'add_funds/pagseguro/retPag';
$route['cron/picpay']                                = 'add_funds/picpay/complete';

/**##################################
 * Add a 'prefix' controller to Admin|Staff
 * default: admin
 */##################################
$GLOBALS['ADMIN_URL_PREFIX'] = 'admin';

$route['upload_files']                                      = 'admin/file_manager/upload_files';
$route[$GLOBALS['ADMIN_URL_PREFIX']]                        = 'admin/login';
$route[$GLOBALS['ADMIN_URL_PREFIX'] . '/settings/store']    = 'admin/settings/store';
$route[$GLOBALS['ADMIN_URL_PREFIX'] . '/settings/(:any)']   = 'admin/settings/index/$1';
$route[$GLOBALS['ADMIN_URL_PREFIX'] . '/services/comments/bulk_action/delete'] = 'admin/services/bulk_action_delete';

$route[$GLOBALS['ADMIN_URL_PREFIX'] . '/(:any)']                            = 'admin/$1';
$route[$GLOBALS['ADMIN_URL_PREFIX'] . '/(:any)/(:any)']                     = 'admin/$1/$2';
$route[$GLOBALS['ADMIN_URL_PREFIX'] . '/(:any)/(:any)/(:any)']              = 'admin/$1/$2/$3';
$route[$GLOBALS['ADMIN_URL_PREFIX'] . '/(:any)/(:any)/(:any)/(:any)']       = 'admin/$1/$2/$3/$4';
