<?php

if (defined('BACK_END') && !Config::get('github_token')) {
    // ok to error out since we can assume they're nmc staff
    throw new RuntimeException('GitHub token is not set');
}

require_once(ROOT_PATH . '/modules/Releases/module.php');
require_once(ROOT_PATH . '/modules/Releases/classes/Release.php');
require_once(ROOT_PATH . '/modules/Releases/classes/GithubHelper.php');
require_once(ROOT_PATH . '/modules/Releases/classes/ReleasesHelper.php');
require_once(ROOT_PATH . '/modules/Releases/classes/StatisticsHandler.php');

$module = new Releases_Module($pages, $language, $endpoints);
