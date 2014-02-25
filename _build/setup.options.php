<?php
/**
 * Build the setup options form.
 */
$exists = false;
$output = null;
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
	case xPDOTransport::ACTION_INSTALL:

	case xPDOTransport::ACTION_UPGRADE:
		$exists = $modx->getObject('transport.modTransportPackage', array('package_name' => 'HybridAuth'));

		if (!empty($options['attributes']['chunks'])) {
			$chunks = '<ul id="formCheckboxes" style="height:200px;overflow:auto;">';
			foreach ($options['attributes']['chunks'] as $k => $v) {
				$chunks .= '
					<li>
						<label>
							<input type="checkbox" name="update_chunks[]" value="'.$k.'"> '.$k.'
						</label>
					</li>';
			}
			$chunks .= '</ul>';
		}
		break;

	case xPDOTransport::ACTION_UNINSTALL: break;
}

if (!$exists) {
	switch ($modx->getOption('manager_language')) {
		case 'ru':
			$output = 'Этот компонент может работать c <b>HybridAuth</b> для социальной авторизации.<br/><br/>Могу я автоматически скачать и установить его?';
			break;
		default:
			$output = 'This component can work with <b>HybridAuth</b> for social authorization.<br/><br/>Can i automaticly download and install it?';
	}
}
if ($chunks) {
	if (!$exists) {
		$output .= '<br/><br/>';
	}

	switch ($modx->getOption('manager_language')) {
		case 'ru':
			$output .= 'Выберите чанки, которые нужно <b>перезаписать</b>:<br/>
				<small>
					<a href="#" onclick="Ext.get(\'formCheckboxes\').select(\'input\').each(function(v) {v.dom.checked = true;});">отметить все</a> |
					<a href="#" onclick="Ext.get(\'formCheckboxes\').select(\'input\').each(function(v) {v.dom.checked = false;});">cнять отметки</a>
				</small>
			';
			break;
		default:
			$output .= 'Select chunks, which need to <b>overwrite</b>:<br/>
				<small>
					<a href="#" onclick="Ext.get(\'formCheckboxes\').select(\'input\').each(function(v) {v.dom.checked = true;});">select all</a> |
					<a href="#" onclick="Ext.get(\'formCheckboxes\').select(\'input\').each(function(v) {v.dom.checked = false;});">deselect all</a>
				</small>
			';
	}

	$output .= $chunks;
}


return $output;