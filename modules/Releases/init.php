<?php

require_once(ROOT_PATH . '/modules/Releases/module.php');
require_once(ROOT_PATH . '/modules/Releases/classes/GithubHelper.php');
require_once(ROOT_PATH . '/modules/Releases/classes/ReleasesHelper.php');
require_once(ROOT_PATH . '/modules/Releases/classes/StatisticsHandler.php');

$module = new Releases_Module($pages, $language, $endpoints);
