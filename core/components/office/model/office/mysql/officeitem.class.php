<?php
/**
 * @package office
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/officeitem.class.php');
class OfficeItem_mysql extends OfficeItem {}