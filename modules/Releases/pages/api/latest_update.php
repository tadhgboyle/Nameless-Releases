<?php

header('Content-Type: application/json');

$latest_update = ReleasesHelper::getInstance()->getReleases()[0];

die(json_encode(
    $latest_update->toArray()
));
