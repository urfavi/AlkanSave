<?php
require_once '../includes/config.php';
session_start();
// ari ka kuya em hueueueu ikaw lang sa admin kami ni jhane sa user
// Temporary "Under Maintenance" message
header("Location: ../under_maintenance.html");
exit;
?>