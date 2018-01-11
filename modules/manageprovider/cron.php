<?php
define ("_PS_ADMIN_DIR_", 1);
require(dirname(__FILE__).'/../../config/config.inc.php');
require_once dirname(__FILE__).'/api/manageprovider.php';
$manage = new ManageProviderCli();
$manage->treat();
Category::regenerateEntireNtree();