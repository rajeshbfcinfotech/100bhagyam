<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "welcome";

$route['404_override'] = '';

$route['website-details'] = "welcome/websiteDetails";

$route['splash/(:any)/(:any)/(:any)'] = "welcome/splash/$1/$2/$3";

$route['product-list/(:any)/(:any)/(:any)/(:any)/(:any)'] = "welcome/productList/$1/$2/$3/$4/$5";

$route['brand-product-list/(:any)/(:any)/(:any)/(:any)'] = "welcome/brandProductList/$1/$2/$3/$4";

$route['item/(:any)'] = "welcome/getsingleProd/$1";

$route['category-item/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)'] = "welcome/categoryItem/$1/$2/$3/$4/$5/$6/$7";

$route['category-list/(:any)/(:any)'] = "welcome/categoryList/$1/$2";

$route['discounted-product/(:any)/(:any)/(:any)'] = "welcome/discountedProductList/$1/$2/$3";

$route['most-popular-product/(:any)/(:any)/(:any)'] = "welcome/mostPopularProductList/$1/$2/$3";

$route['registerUser'] = "welcome/registerUser";

$route['login'] = "welcome/login";

$route['registerVendor'] = "welcome/registerVendor";

$route['referred-by-user'] = "welcome/referredByUser";

$route['search/(:any)'] = "welcome/searchData/$1";

$route['getinfo/(:any)'] = "welcome/getInfo/$1";

$route['updateprofile'] = "welcome/updateProfile";

$route['addtocart'] = "welcome/addtoCart";

$route['addtocartOffer'] = "welcome/addtoCartOffer";

$route['get-cart/(:any)/(:any)'] = "welcome/getCart/$1/$2";

$route['empty-cart/(:any)/(:any)'] = "welcome/emptyCart/$1/$2";

$route['delete-cart/(:any)'] = "welcome/deleteCart/$1";

$route['update-cart'] = "welcome/updateQty";

$route['edit-cart'] = "welcome/updateCart";

$route['get-time-slot'] = "welcome/getTimeSlot";

$route['checkout'] = "welcome/checkout";

$route['apply-promo-code/(:any)/(:any)'] = "welcome/Applypromocode/$1/$2";

$route['cash-on-delivery'] = "welcome/cashonDelivery";

$route['online-payment'] = "welcome/payonlineSuccess";

$route['my-order/(:any)'] = "welcome/orderHistory/$1";

$route['order-detail/(:any)'] = "welcome/getorderDetail/$1";

$route['thank-you/(:any)'] = "welcome/thankyou/$1";

$route['track-order/(:any)'] = "welcome/trackOrder/$1";

$route['cancel-order'] = "welcome/cancelOrder";

$route['forgot-password'] = "welcome/forgotPass";

$route['filter'] = "welcome/getpacksizeother";

$route['get-address/(:any)/(:any)'] = "welcome/getAddress/$1/$2";

$route['get-category'] = "welcome/getAllCat";

$route['get-sub-category/(:any)'] = "welcome/getAllSubCat/$1";

$route['get-sub-sub-category/(:any)'] = "welcome/getAllSubSubCat/$1";

$route['get-promo-codes/(:any)'] = "welcome/getPromoCode/$1";

$route['order-review/(:any)'] = "welcome/orderReview/$1";

$route['redirecting-page/(:any)'] = "welcome/staticPage/$1";

$route['term'] = "welcome/termsConditions";

$route['offer-product/(:any)/(:any)/(:any)/(:any)'] = "welcome/offerProducts/$1/$2/$3/$4";

$route['offer-detail/(:any)/(:any)/(:any)'] = "welcome/getsingleOfferProduct/$1/$2/$3";

/* End of file routes.php */
/* Location: ./application/config/routes.php */