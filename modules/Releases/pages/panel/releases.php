<?php

// TODO: release deleting + approval

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
        'EDIT_LINK' => URL::build('/panel/releases', 'action=edit&id='),
        'ALL_RELEASES' => ReleasesHelper::getInstance()->getReleases(),
    ));

    $template_file = 'releases/list.tpl';
} else {

    if ($_GET['action'] == 'edit') {

        $editing_release = ReleasesHelper::getInstance()->getRelease($_GET['id']);

        if ($editing_release === null) {
            Redirect::to(URL::build('/panel/releases'));
        }

        if (Input::exists()) {

            DB::getInstance()->update('releases', $editing_release->getId(), [
                'name' => Output::getClean(Input::get('name')),
                'version_tag' => Output::getClean(Input::get('version_tag')),
                'github_release_id' => Output::getClean(Input::get('github_release_id')),
                'required_version' => Output::getClean(Input::get('required_version')),
                'install_instructions' => Output::getClean(Input::get('install_instructions')),
                'urgent' => isset($_POST['urgent']) ? 1 : 0,
            ]);

            $cache_key = 'github_release_link-' . $editing_release->getid();

            $cache->setCache('releases');
            if ($cache->isCached($cache_key)) {
                $cache->erase($cache_key);
            }

            GithubHelper::getInstance()->resetCache();

            Session::flash('releases_success', 'Updated a Release: ' . Input::get('name'));

            Redirect::to(URL::build('/panel/releases'));

        } else {

            $smarty->assign(array(
                'EDITING_RELEASE' => $editing_release,
            ));
        }

    } else if ($_GET['action'] == 'new') {

        if (Input::exists()) {

            $validator = (new Validate())->check($_POST, [
                'name' => [
                    Validate::REQUIRED,
                    Validate::UNIQUE => 'releases'
                ],
                'version_tag' => [
                    Validate::REQUIRED,
                    Validate::UNIQUE => 'releases'
                ],
                'github_release_id' => [
                    Validate::REQUIRED,
                    Validate::UNIQUE => 'releases'
                ],
                'required_version' => [
                    Validate::REQUIRED,
                    Validate::UNIQUE => 'releases'
                ],
                'install_instructions' => Validate::REQUIRED
            ]);

            if (!$validator->passed()) {
                Session::flash('releases_errors', $validator->errors());

                Redirect::to(URL::build('/panel/releases', 'action=new'));

            } else {

                DB::getInstance()->insert('releases', [
                    'name' => Output::getClean(Input::get('name')),
                    'version_tag' => Output::getClean(Input::get('version_tag')),
                    'required_version' => Output::getClean(Input::get('required_version')),
                    'github_release_id' => Output::getClean(Input::get('github_release_id')),
                    'urgent' => isset($_POST['urgent']) ? 1 : 0,
                    'install_instructions' => Output::getClean(Input::get('install_instructions')),
                    'created_at' => time(),
                ]);

                GithubHelper::getInstance()->resetCache();

                Session::flash('releases_success', 'Created new Release: ' . Input::get('name'));

                Redirect::to(URL::build('/panel/releases'));
            }
        }
    }

    $smarty->assign(array(
        'BACK_LINK' => URL::build('/panel/releases'),
        'GITHUB_RELEASES' => GithubHelper::getInstance()->getGithubReleases(),
    ));

    $template_file = 'releases/form.tpl';
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, array($navigation, $cc_nav, $staffcp_nav), $widgets, $template);

if (Session::exists('releases_errors')) {
    $smarty->assign(array(
        'ERRORS_TITLE' => $language->get('general', 'error'),
        'ERRORS' => Session::flash('releases_errors')
    ));
}

if (Session::exists('releases_success')) {
    $smarty->assign(array(
        'SUCCESS_TITLE' => $language->get('general', 'success'),
        'SUCCESS' => Session::flash('releases_success')
    ));
}

$smarty->assign(array(
    'PAGE' => PANEL_PAGE,
    'PARENT_PAGE' => PARENT_PAGE,
    'DASHBOARD' => $language->get('admin', 'dashboard'),
    'TOKEN' => Token::get(),
    'NEW_LINK' => URL::build('/panel/releases', 'action=new'),
));

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate($template_file, $smarty);
