<?php
require_once '../config/config.php';

session_destroy();
setcookie('remember_token', '', time() - 3600, '/');

redirect('../index.php');
?>