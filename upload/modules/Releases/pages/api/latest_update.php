<?php

header('Content-Type: application/json');

$latest_update = ReleasesHelper::getInstance()->getReleases()[0];

die(json_encode([
    'name' => $latest_update['name'],
    'version_tag' => $latest_update['version_tag'],
    'required_version' => $latest_update['required_version'],
    'github_link' => $latest_update['github_link'],
    'urgent' => (bool) $latest_update['urgent'],
    'install_instructions' => $latest_update['install_instructions'],
]));