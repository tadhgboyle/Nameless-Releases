<?php
if (!$user->handlePanelPageLoad('admincp.releases')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'releases');
define('PANEL_PAGE', 'releases');
$page_title = 'Releases';
require_once(ROOT_PATH . '/core/templates/backend_init.php');

if (!isset($_GET['action'])) {
    $smarty->assign(array(
        'NONE' => $language->get('general', 'none'),
        'ALL_RELEASES' => ReleasesHelper::getInstance()->getFormattedReleases()
    ));

    $template_file = 'releases/list.tpl';
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, array($navigation, $cc_nav, $mod_nav), $widgets);

$smarty->assign(array(
    'PAGE' => PANEL_PAGE,
    'PARENT_PAGE' => PARENT_PAGE,
    'DASHBOARD' => $language->get('admin', 'dashboard'),
    'TOKEN' => Token::get(),
));

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate($template_file, $smarty);