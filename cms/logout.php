<?php
/**
 * InfinityBinary CMS - Logout
 */

define('IB_INIT', true);
require_once __DIR__ . '/../includes/config.php';

logout_user();
redirect('/cms/login.php');
