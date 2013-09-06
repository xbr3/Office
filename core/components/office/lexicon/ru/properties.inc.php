<?php

$_lang['office_prop_action'] = 'Имя контроллера для запуска. Обязательный параметр.';

$_lang['office_prop_HybridAuth'] = 'Включить интеграцию с HybridAuth, если он установлен.';
$_lang['office_prop_providers'] = 'Список провайдеров авторизации HybridAuth, через запятую. Все доступные провайдеры находятся тут {core_path}components/hybridauth/model/hybridauth/lib/Providers/. Например, &providers=`Google,Twitter,Facebook`.';
$_lang['office_prop_groups'] = 'Список групп для регистрации пользователя, через запятую. Можно указывать роль юзера в группе через двоеточие. Например, &groups=`Users:1` добавит юзера в группу "Users" с ролью "member".';
$_lang['office_prop_rememberme'] = 'Запомниает пользователя на долгое время. По умолчанию - включено.';
$_lang['office_prop_loginContext'] = 'Основной контекст для авторизации. По умолчанию - текущий.';
$_lang['office_prop_addContexts'] = 'Дополнительные контексты, через запятую. Например, &addContexts=`web,ru,en`';

$_lang['office_prop_loginResourceId'] = 'Идентификатор ресурса, на который отправлять юзера после авторизации. По умолчанию, это 0 - обновляет текущую страницу.';
$_lang['office_prop_logoutResourceId'] = 'Идентификатор ресурса, на который отправлять юзера после завершения сессии. По умолчанию, это 0 - обновляет текущую страницу.';

$_lang['office_prop_tplLogin'] = 'Этот чанк будет показан анонимному пользователю, то есть любому гостю.';
$_lang['office_prop_tplLogout'] = 'Этот чанк будет показан авторизованному пользователю.';
$_lang['office_prop_tplActivate'] = 'Чанк для оформления письма активации.';
$_lang['office_prop_tplProfile'] = 'Чанк для вывода и редактирования профиля пользователя.';
$_lang['office_prop_providerTpl'] = 'Чанк для вывода ссылки на авторизацию или привязку сервиса HybridAuth к учетной записи.';
$_lang['office_prop_activeProviderTpl'] = 'Чанк для вывода иконки привязанного сервиса HybridAuth.';

$_lang['office_prop_linkTTL'] = 'Время жизни ссылки активации профиля.';

$_lang['office_prop_profileFields'] = 'Список разрешенных для редактирования полей юзера, через запятую. Также можно указать максимальну. длину значений, через двоеточие. Например, &profileFields=`username:25,fullname:50,email`.';
$_lang['office_prop_requiredFields'] = 'Список обязательных полей при редактировании. Эти поля должны быть заполнены для успешного обновления профиля. Например, &requiredFields=`username,fullname,email`.';
