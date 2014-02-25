<?php

$_lang['office_prop_action'] = 'Name of controller for lanch. Required.';

$_lang['office_prop_HybridAuth'] = 'Enable integration with HybridAuth, if installed.';
$_lang['office_prop_providers'] = 'Comma separated list of a HybridAuth providers for authentification. All available providers are here {core_path}components/hybridauth/model/hybridauth/lib/Providers/. For example, &providers=`Google,Twitter,Facebook`.';
$_lang['office_prop_groups'] = 'Comma separated list of existing user groups for joining by user at the first login. For example, &groups=`Users:1` will add new user to group "Users" with role "member"';
$_lang['office_prop_rememberme'] = 'If true, user will be remembered for a long time.';
$_lang['office_prop_loginContext'] = 'Main context for authentication. By default - it is current context.';
$_lang['office_prop_addContexts'] = 'Comma separated list of additional contexts for authentication. For example &addContexts=`web,ru,en`';

$_lang['office_prop_loginResourceId'] = 'Resource id to redirect to on successful login. By default, it is 0 - redirect to self.';
$_lang['office_prop_logoutResourceId'] = 'Resource id to redirect to on successful logout. By default, it is 0 - redirect to self.';

$_lang['office_prop_tplLogin'] = 'This chunk will see any anonymous user.';
$_lang['office_prop_tplLogout'] = 'This chunk will see any authenticated user.';
$_lang['office_prop_tplActivate'] = 'Chunk for activation email.';
$_lang['office_prop_tplProfile'] = 'Chunk for display and edit user profile.';
$_lang['office_prop_providerTpl'] = 'Chunk to display a link for HybridAuth authorization or linking a service to your account.';
$_lang['office_prop_activeProviderTpl'] = 'Chunk for output icon of linked HybridAuth service.';

$_lang['office_prop_linkTTL'] = 'Time to live of profile activation link.';

$_lang['office_prop_profileFields'] = 'Comma separated list of allowed user fields for update with maximum length of sended values. For example, &profileFields=`username:25,fullname:50,email`.';
$_lang['office_prop_requiredFields'] = 'Comma separated list of required user fields when update. This fields must be filled for successful update of profile. For example, &requiredFields=`username,fullname,email`.';

$_lang['office_prop_avatarPath'] = 'Directory for save users avatars in MODX_ASSETS_PATH. By default is "images/users/".';
$_lang['office_prop_avatarParams'] = 'JSON string with parameters for avatar convertation via phpThumb. By default is "{"w":200,"h":200,"zc":0,"bg":"ffffff","f":"jpg"}".';