<?php

$_lang['area_office_main'] = 'Основные';
$_lang['area_office_auth'] = 'Авторизация';
$_lang['area_office_profile'] = 'Профиль';
$_lang['area_office_ms2'] = 'miniShop2';

$_lang['setting_office_frontend_css'] = 'Основные стили кабинета';
$_lang['setting_office_frontend_css_desc'] = 'Путь к файлу с основными стилями кабинета. Если вы хотите использовать собственные стили - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';
$_lang['setting_office_extjs_css'] = 'Кастомные стили ExtJS';
$_lang['setting_office_extjs_css_desc'] = 'Вы можете указать путь к собственным стилям для оформления личного кабинета при использовании ExtJS в контроллере.';
$_lang['setting_office_frontend_js'] = 'Основной скрипт кабинета';
$_lang['setting_office_frontend_js_desc'] = 'Путь к файлу с основными скриптами кабинета. Если вы хотите использовать собственные скрипты - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';
$_lang['setting_office_auth_frontend_css'] = 'Стили контроллера Auth';
$_lang['setting_office_auth_frontend_css_desc'] = 'Путь к файлу со стилями контроллера Auth. Если вы хотите использовать собственные стили - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';
$_lang['setting_office_auth_frontend_js'] = 'Скрипт контроллера Auth';
$_lang['setting_office_auth_frontend_js_desc'] = 'Путь к файлу со скриптами контроллера Auth. Если вы хотите использовать собственные скрипты - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';
$_lang['setting_office_profile_frontend_css'] = 'Стили контроллера Profile';
$_lang['setting_office_profile_frontend_css_desc'] = 'Путь к файлу со стилями контроллера Profile. Если вы хотите использовать собственные стили - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';
$_lang['setting_office_profile_frontend_js'] = 'Скрипт контроллера Profile';
$_lang['setting_office_profile_frontend_js_desc'] = 'Путь к файлу со скриптами контроллера Profile. Если вы хотите использовать собственные скрипты - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';
$_lang['setting_office_ms2_frontend_css'] = 'Стили контроллера miniShop2';
$_lang['setting_office_ms2_frontend_css_desc'] = 'Путь к файлу со стилями контроллера miniShop2. Если вы хотите использовать собственные стили - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';
$_lang['setting_office_ms2_frontend_js'] = 'Скрипт контроллера miniShop2';
$_lang['setting_office_ms2_frontend_js_desc'] = 'Путь к файлу со скриптами контроллера miniShop2. Если вы хотите использовать собственные скрипты - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';

$_lang['setting_office_auth_page_id'] = 'Id страницы авторизации';
$_lang['setting_office_auth_page_id_desc'] = 'Id страницы сайта, на которой вызывается контроллер Auth. Эта настройка заполняется автоматически, при первом вызове контроллера.';
$_lang['setting_office_profile_page_id'] = 'Id страницы профиля';
$_lang['setting_office_profile_page_id_desc'] = 'Id страницы сайта, на которой вызывается контроллер Profile. Эта настройка заполняется автоматически, при первом вызове контроллера.';
$_lang['setting_office_profile_required_fields'] = 'Обязательные поля профиля';
$_lang['setting_office_profile_required_fields_desc'] = 'Укажите обязательные поля профиля пользователя. Пользователь будет постоянно отправляться на редактирование профиля, пока не заполнит эти поля.';

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
$_lang['setting_office_zp_activation_type'] = 'Тип активации кошельков';
$_lang['setting_office_zp_activation_type_desc'] = 'В зависимости от настроек вашего биллинга, вы должны указать один из двух возможных типов активации кошельков: PHONE_CODE или MAIL_CODE.';

$_lang['setting_office_ms2_date_format'] = 'Формат даты';
$_lang['setting_office_ms2_date_format_desc'] = 'Укажите формат дат, используя синтаксис php функции strftime(). Например, "%d.%m.%y %H:%M".';
$_lang['setting_office_ms2_order_grid_fields'] = 'Поля таблицы заказов';
$_lang['setting_office_ms2_order_grid_fields_desc'] = 'Список полей, которые будут показаны в таблице заказов. Доступны: "createdon,updatedon,num,cost,cart_cost,delivery_cost,weight,status,delivery,payment,customer,receiver".';
$_lang['setting_office_ms2_order_form_fields'] = 'Основные поля заказа';
$_lang['setting_office_ms2_order_form_fields_desc'] = 'Список полей заказа, которые будут показаны на первой вкладке карточки заказа. Доступны: "weight,createdon,updatedon,cart_cost,delivery_cost,status,delivery,payment".';
$_lang['setting_office_ms2_order_address_fields'] = 'Поля адреса доставки';
$_lang['setting_office_ms2_order_address_fields_desc'] = 'Список полей доставки, которые будут показаны на третьей вкладке карточки заказа. Доступны: "receiver,phone,index,country,region,metro,building,city,street,room". Если параметр пуст, вкладка будет скрыта.';
$_lang['setting_office_ms2_order_product_fields'] = 'Поля таблицы покупок';
$_lang['setting_office_ms2_order_product_fields_desc'] = 'Список полей таблицы заказанных товаров. Доступны: "count,price,weight,cost,options". Поля товара указываются с префиксом "product_", например "product_pagetitle,product_article". Дополнительно можно указывать значения из поля options с префиксом "option_", например: "option_color,option_size".';