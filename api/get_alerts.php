<?php
require_once '../classes/Alert.php';
require_once '../classes/Database.php';

header('Content-Type: application/json');

$alerts = Alert::getHistory(10); // Get last 10 alerts
echo json_encode($alerts);