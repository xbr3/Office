<?php

$_lang['area_office_main'] = 'Main';
$_lang['area_office_auth'] = 'Authorization';
$_lang['area_office_profile'] = 'Profile';
$_lang['area_office_extras'] = 'Extras';
$_lang['area_office_zpayment'] = 'Z-Payment';
$_lang['area_office_ms2'] = 'miniShop2';

$_lang['setting_office_frontend_css'] = 'Office main styles';
$_lang['setting_office_frontend_css_desc'] = 'Path to file with the main styles of the office. If you want to use your own styles - specify them here, or clean this parameter and load them in site template.';
$_lang['setting_office_extjs_css'] = 'ExtJS custom css';
$_lang['setting_office_extjs_css_desc'] = 'You can specify path to custom css for styling office when ExtJS used in the controller.';
$_lang['setting_office_frontend_js'] = 'Office main script';
$_lang['setting_office_frontend_js_desc'] = 'Path to file with the main javascript of the office. If you want to use your own sscripts - specify them here, or clean this parameter and load them in site template.';
$_lang['setting_office_auth_frontend_css'] = 'Styles of controller Auth';
$_lang['setting_office_auth_frontend_css_desc'] = 'Path to file with Auth styles. If you want to use your own styles - specify them here, or clean this parameter and load them in site template.';
$_lang['setting_office_auth_frontend_js'] = 'Script of controller Auth';
$_lang['setting_office_auth_frontend_js_desc'] = 'Path to file with the Auth javascript. If you want to use your own sscripts - specify them here, or clean this parameter and load them in site template.';
$_lang['setting_office_profile_frontend_css'] = 'Styles of controller Profile';
$_lang['setting_office_profile_frontend_css_desc'] = 'Path to file with Profile styles. If you want to use your own styles - specify them here, or clean this parameter and load them in site template.';
$_lang['setting_office_profile_frontend_js'] = 'Script of controller Profile';
$_lang['setting_office_profile_frontend_js_desc'] = 'Path to file with the Profile javascript. If you want to use your own sscripts - specify them here, or clean this parameter and load them in site template.';
$_lang['setting_office_ms2_frontend_css'] = 'Styles of controller miniShop2';
$_lang['setting_office_ms2_frontend_css_desc'] = 'Path to file with miniShop2 styles. If you want to use your own styles - specify them here, or clean this parameter and load them in site template.';
$_lang['setting_office_ms2_frontend_js'] = 'Script of controller miniShop2';
$_lang['setting_office_ms2_frontend_js_desc'] = 'Path to file with the miniShop2 javascript. If you want to use your own sscripts - specify them here, or clean this parameter and load them in site template.';

$_lang['setting_office_auth_page_id'] = 'Auth page id';
$_lang['setting_office_auth_page_id_desc'] = 'Id of the site page, where controller Auth is called. This setting is automatically filled in when you call the controller first time.';
$_lang['setting_office_auth_page_id'] = 'Profile page id';
$_lang['setting_office_auth_page_id_desc'] = 'Id of the site page, where controller Profile is called. This setting is automatically filled in when you call the controller first time.';

$_lang['setting_office_zp_interface'] = 'Interface identity';
$_lang['setting_office_zp_interface_desc'] = 'Integer representing the payment interface, which you gave when registering in the Z-Payment.';
$_lang['setting_office_zp_api_url'] = 'Address for sending queries';
$_lang['setting_office_zp_api_url_desc'] = 'Address of the public API billing Z-Payment in the Internet.';
$_lang['setting_office_zp_account'] = 'ZP purse of interface owner';
$_lang['setting_office_zp_account_desc'] = 'Полный номер кошелька, который владеет этим интерфейсом в формате ZPxxxxxxxx.';
$_lang['setting_office_zp_password'] = 'Password to the interface';
$_lang['setting_office_zp_password_desc'] = 'This password will also have to issue upon registration in the Z-Payment.';
$_lang['setting_office_zp_money_format'] = 'Money format';
$_lang['setting_office_zp_money_format_desc'] = 'You can specify, how to format money by function number_format(). For this used JSON string with array of 3 values: number of decimals, decimals separator and thousands separator. By default format is [2,"."," "], that transforms "15336.6" into "15 336.60"';
$_lang['setting_office_zp_activation_type'] = 'The type of purses activation';
$_lang['setting_office_zp_activation_type_desc'] = 'Depending on the settings of your billing, you must specify one of two possible types of purses activation: PHONE_CODE or MAIL_CODE.';

$_lang['setting_office_ms2_date_format'] = 'date Format';
$_lang['setting_office_ms2_date_format_desc'] = 'Specify the format of date, using the syntax of php function strftime(). For example, "%d.%m.%y %H:%M".';
$_lang['setting_office_ms2_order_grid_fields'] = 'Fields of the orders table';
$_lang['setting_office_ms2_order_grid_fields_desc'] = 'Comma separated list of fields in the table of orders. Available: "createdon,updatedon,num,cost,cart_cost,delivery_cost,weight,status,delivery,payment,customer,receiver".';
$_lang['setting_office_ms2_order_form_fields'] = 'Main fields of order form';
$_lang['setting_office_ms2_order_form_fields_desc'] = 'Comma separated list of the main fields in the order, which will be shown at the first tab. Available: "weight,createdon,updatedon,cart_cost,delivery_cost,status,delivery,payment".';
$_lang['setting_office_ms2_order_address_fields'] = 'Fields of order address';
$_lang['setting_office_ms2_order_address_fields_desc'] = 'Comma separated list of address of order, which will be shown on the third tab. Available: "receiver,phone,index,country,region,metro,building,city,street,room". If empty, this tab will be hidden.';
$_lang['setting_office_ms2_order_product_fields'] = 'Field of the purchased products';
$_lang['setting_office_ms2_order_product_fields_desc'] = 'which will be shown list of ordered products. Available: "count,price,weight,cost,options". Product fields specified with the prefix "product_", for example "product_pagetitle,product_article". Additionaly, you can specify a values from the options field with the prefix "option_", for example: "option_color,option_size".';