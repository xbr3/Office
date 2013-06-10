<?php

$_lang['area_office_main'] = 'Main';

$_lang['setting_office_frontend_css'] = 'Frontend styles';
$_lang['setting_office_frontend_css_desc'] = 'Path to file with styles of the shop. If you want to use your own styles - specify them here, or clean this parameter and load them in site template.';
$_lang['setting_office_frontend_js'] = 'Frontend scripts';
$_lang['setting_office_frontend_js_desc'] = 'Path to file with scripts of the shop. If you want to use your own sscripts - specify them here, or clean this parameter and load them in site template.';
$_lang['setting_office_page_id'] = 'Office page id';
$_lang['setting_office_page_id_desc'] = 'Specify on which page of the site the snippet Office is called. This option is required to generate links.';

$_lang['area_office_zpayment'] = 'Z-Payment';

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