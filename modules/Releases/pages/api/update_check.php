<?php

header('Content-Type: application/json');

if (!isset($_GET['uid'], $_GET['version'])) {
    die(json_encode([
        'error' => true,
        'update_available' => false,
        'message' => 'uid or version not set.'
    ]));
}

$data = $_GET;

StatisticsHandler::handleRequest($data);

$new_update = ReleasesHelper::getInstance()->getUpdateForVersion($data['version']);

if ($new_update === null) {
    die(json_encode([
        'error' => false,
        'update_available' => false,
        'message' => 'No update available'
    ]));
}

$return = [
    'error' => false,
    'update_available' => true,
];

die(json_encode(array_merge(
    $return, $new_update->toArray()
)));
