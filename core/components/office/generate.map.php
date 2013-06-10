<?php
define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/index.php';
/*******************************************************/

$package = 'office'; // Class name for generation

// Folders for schema and model
$Model = dirname(__FILE__).'/model/';
$Schema = dirname(__FILE__).'/model/schema/';
$xml = $Schema.$package.'.mysql.schema.xml';

// Remove old files
rrmdir($Model.$package .'/mysql');

/*******************************************************/

$modx->getService('error','error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
$modx->error->message = null;
$modx->loadClass('transport.modPackageBuilder', '', false, true);
$manager = $modx->getManager();

$generator = $manager->getGenerator();
$generator->parseSchema($xml, $Model);

$modx->addPackage($package, $Model);

//$manager->removeObjectContainer('zpUser');

//$manager->createObjectContainer('zpUser');


print "\nDone\n";


/********************************************************/
function rrmdir($dir) {
	if (is_dir($dir)) {
		$objects = scandir($dir);

		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
			}
		}

		reset($objects);
		rmdir($dir);
	}
}