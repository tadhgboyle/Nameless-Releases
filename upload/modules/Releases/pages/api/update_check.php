<?php

header('Content-Type: application/json');

if (!isset($_GET['uid']) || !isset($_GET['version'])) {
    die(json_encode([
        'error' => true,
        'update_available' => false,
        'message' => 'uid or version not set.'
    ]));
}

$data = $_GET;

if (!isValidSiteId($data['uid'])) {
    die(json_encode([
        'error' => true,
        'update_available' => false,
        'message' => 'Invalid site unique id'
    ]));
}

StatisticsHandler::handleRequest($data);

$new_update = ReleasesHelper::getInstance()->getUpdateForVersion($data['version']);

if ($new_update == null) {
    die(json_encode([
        'error' => false,
        'update_available' => false,
        'message' => 'No update available'
    ]));
}

die(json_encode([
    'update_available' => true,
    'name' => $new_update['name'],
    'version_tag' => $new_update['version_tag'],
    'download_link' => $new_update['download_link'],
    'urgent' => (bool) $new_update['urgent'],
    'install_instructions' => $new_update['install_instructions'],
]));

function isValidSiteId(string $id) {
    return true;
}