<?php

$_lang['area_office_main'] = 'Основные';

$_lang['setting_office_frontend_css'] = 'Стили фронтенда';
$_lang['setting_office_frontend_css_desc'] = 'Путь к файлу со стилями магазина. Если вы хотите использовать собственные стили - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';
$_lang['setting_office_frontend_js'] = 'Скрипты фронтенда';
$_lang['setting_office_frontend_js_desc'] = 'Путь к файлу со скриптами магазина. Если вы хотите использовать собственные скрипты - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';
$_lang['setting_office_page_id'] = 'Id страницы кабинета';
$_lang['setting_office_page_id_desc'] = 'Укажите, на какой странице сайта вызывается сниппето личного кабинета. Этот параметр необходим для генерации ссылок.';

$_lang['area_office_zpayment'] = 'Z-Payment';

$_lang['setting_office_zp_interface'] = 'Идентификатор интерфейса';
$_lang['setting_office_zp_interface_desc'] = 'Целое число с номером платежного интерфейса, который вам выдали при регистрации в Z-Payment.';
$_lang['setting_office_zp_api_url'] = 'Адрес для отправки запросов';
$_lang['setting_office_zp_api_url_desc'] = 'Адрес публичного API биллинга Z-Payment в интернет.';
$_lang['setting_office_zp_account'] = 'ZP кошелек владельца интерфейса';
$_lang['setting_office_zp_account_desc'] = 'Полный номер кошелька, который владеет этим интерфейсом в формате ZPxxxxxxxx.';
$_lang['setting_office_zp_password'] = 'Пароль доступа к интерфейсу';
$_lang['setting_office_zp_password_desc'] = 'Этот пароль вам тоже должны выдать при регистрации в Z-Payment.';
$_lang['setting_office_zp_money_format'] = 'Формат денег';
$_lang['setting_office_zp_money_format_desc'] = 'Укажите, как нужно форматировать деньги функцией number_format(). Используется JSON строка с массивом для передачи 3х параметров: количество десятичных, разделитель десятичных и разделитель тысяч. По умолчанию формат [2,"."," "], что превращает "15336.6" в "15 336.60".';
