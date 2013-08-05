<?php

$settings = array();

$tmp = array(
	'main' => array(
		'frontend_css' => array(
			'xtype' => 'textfield'
			,'value' => '[[+cssUrl]]main/default.css'
		)
		,'extjs_css' => array(
			'xtype' => 'textfield'
			,'value' => ''
		)
		,'frontend_js' => array(
			'xtype' => 'textfield'
			,'value' => '[[+jsUrl]]main/default.js'
		)
	)
	,'auth' => array(
		'auth_page_id' => array(
			'xtype' => 'numberfield'
			,'value' => 0
			,'area' => 'office_auth'
		)
		,'auth_frontend_css' => array(
			'xtype' => 'textfield'
			,'value' => ''
			,'area' => 'office_auth'
		)
		,'auth_frontend_js' => array(
			'xtype' => 'textfield'
			,'value' => '[[+jsUrl]]auth/default.js'
			,'area' => 'office_auth'
		)
	)
	,'profile' => array(
		'profile_page_id' => array(
			'xtype' => 'numberfield'
			,'value' => 0
			,'area' => 'office_profile'
		)
		,'profile_required_fields' => array(
			'xtype' => 'textfield'
			,'value' => 'fullname'
			,'area' => 'office_profile'
		)
		,'profile_frontend_css' => array(
			'xtype' => 'textfield'
			,'value' => '[[+cssUrl]]profile/default.css'
			,'area' => 'office_profile'
		)
		,'profile_frontend_js' => array(
			'xtype' => 'textfield'
			,'value' => '[[+jsUrl]]profile/default.js'
			,'area' => 'office_profile'
		)
	)
	,'zpayment' => array(
		'zp_interface' => array(
			'xtype' => 'numberfield'
			,'value' => 0
			,'area' => 'office_zbillng'
		)
		,'zp_api_url' => array(
			'xtype' => 'textfield'
			,'value' => 'https://z-payment.com/api/billing/'
			,'area' => 'office_zpayment'
		)
		,'zp_account' => array(
			'xtype' => 'textfield'
			,'value' => 'ZP00000000'
			,'area' => 'office_zpayment'
		)
		,'zp_password' => array(
			'xtype' => 'text-password'
			,'value' => 'yourpassword'
			,'area' => 'office_zpayment'
		)
		,'zp_money_format' => array(
			'xtype' => 'textfield'
			,'value' => '[2, ".", " "]'
			,'area' => 'office_zpayment'
		)
		,'zp_activation_type' => array(
			'xtype' => 'textfield'
			,'value' => 'MAIL_CODE'
			,'area' => 'office_zpayment'
		)
	)
	,'minishop2' => array(
		'ms2_frontend_css' => array(
			'xtype' => 'textfield'
			,'value' => '[[+cssUrl]]minishop2/default.css'
			,'area' => 'office_ms2'
		)
		,'ms2_frontend_js' => array(
			'xtype' => 'textfield'
			,'value' => '[[+jsUrl]]minishop2/default.js'
			,'area' => 'office_ms2'
		)
		,'ms2_date_format' => array(
			'xtype' => 'textfield'
			,'value' => '%d.%m.%y <small>%H:%M</small>'
			,'area' => 'office_ms2'
		)
		,'ms2_order_grid_fields' => array(
			'xtype' => 'textarea'
			,'value' => 'num,status,cost,weight,delivery,payment,createdon,updatedon'
			,'area' => 'office_ms2'
		)
		,'ms2_order_form_fields' => array(
			'xtype' => 'textarea'
			,'value' => 'num,cart_cost,delivery_cost,weight,payment,delivery'
			,'area' => 'office_ms2'
		)
		,'ms2_order_address_fields' => array(
			'xtype' => 'textarea'
			,'value' => 'receiver,phone,index,country,region,city,metro,street,building,room,comment'
			,'area' => 'office_ms2'
		)
		,'ms2_order_product_fields' => array(
			'xtype' => 'textarea'
			,'value' => 'product_pagetitle,product_article,weight,price,count,cost'
			,'area' => 'office_ms2'
		)
	)
);

foreach ($tmp as $controller => $values) {
	if (in_array($controller, $BUILD_CONTROLLERS)) {
		foreach ($values as $k => $v) {
			/* @var modSystemSetting $setting */
			$setting = $modx->newObject('modSystemSetting');
			$setting->fromArray(array_merge(
				array(
					'key' => 'office_'.$k
					,'namespace' => 'office'
					,'area' => 'office_main'
					,'value' => ''
				), $v
			),'',true,true);

			$settings[] = $setting;
		}
	}
}

unset($tmp);
return $settings;