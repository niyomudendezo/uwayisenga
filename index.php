<?php
// Fix SCRIPT_NAME so URI stripping works correctly when called from root
$_SERVER['SCRIPT_NAME'] = '/agrukrwanda/public/index.php';
require_once __DIR__ . '/public/index.php';
