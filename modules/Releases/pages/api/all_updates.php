<?php

header('Content-Type: application/json');

$data = [];

foreach (ReleasesHelper::getInstance()->getReleases() as $release) {
    if (!$release->isApproved()) {
        continue;
    }

    $data[] = $release->toArray();
}

die(json_encode($data));
