<?php

header('Content-Type: application/json');

$data = [];

foreach (ReleasesHelper::getInstance()->getReleases() as $release) {
    $data[] = [
        'name' => $release['name'],
        'version_tag' => $release['version_tag'],
        'required_version' => $release['required_version'],
        'github_link' => $release['github_link'],
        'urgent' => (bool) $release['urgent'],
        'install_instructions' => $release['install_instructions'],
    ];
}

die(json_encode($data));
