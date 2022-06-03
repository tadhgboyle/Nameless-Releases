<?php

if (!$user->handlePanelPageLoad('admincp.releases')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

const PAGE = 'panel';
const PARENT_PAGE = 'releases';
const PANEL_PAGE = 'releases';
$page_title = 'Releases';
require_once(ROOT_PATH . '/core/templates/backend_init.php');

$template->assets()->include([
    AssetTree::TINYMCE,
]);

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

            if (Input::get('version_tag') == Input::get('required_version')) {
                Session::flash('releases_errors', 'Version tag must be different from required version');
                Redirect::to(URL::build('/panel/releases', 'action=edit&id=' . $editing_release->getId()));
            }

            DB::getInstance()->update('releases', $editing_release->getId(), [
                'name' => Output::getClean(Input::get('name')),
                'version_tag' => Output::getClean(Input::get('version_tag')),
                'github_release_id' => Output::getClean(Input::get('github_release_id')),
                'required_version' => Output::getClean(Input::get('required_version')),
                'checksum' => Output::getClean(Input::get('checksum')),
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

            $validator = Validate::check($_POST, [
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
                'urgent' => Validate::REQUIRED,
                'checksum' => Validate::REQUIRED,
                'install_instructions' => Validate::REQUIRED
            ]);

            if (!$validator->passed()) {
                Session::flash('releases_errors', $validator->errors());
                Redirect::to(URL::build('/panel/releases', 'action=new'));
            } else {

                if (Input::get('version_tag') == Input::get('required_version')) {
                    Session::flash('releases_errors', 'Version tag must be different from required version');
                    Redirect::to(URL::build('/panel/releases', 'action=new'));
                }

                DB::getInstance()->insert('releases', [
                    'name' => Output::getClean(Input::get('name')),
                    'version_tag' => Output::getClean(Input::get('version_tag')),
                    'required_version' => Output::getClean(Input::get('required_version')),
                    'github_release_id' => Output::getClean(Input::get('github_release_id')),
                    'urgent' => isset($_POST['urgent']) ? 1 : 0,
                    'checksum' => Output::getClean(Input::get('checksum')),
                    'install_instructions' => Input::get('install_instructions'),
                    'created_by' => $user->data()->id,
                    'created_at' => time(),
                ]);

                GithubHelper::getInstance()->resetCache();

                Session::flash('releases_success', 'Created new Release: ' . Input::get('name'));
                Redirect::to(URL::build('/panel/releases'));
            }
        }
    } else if ($_GET['action'] === 'approve') {

        $release = ReleasesHelper::getInstance()->getRelease($_GET['id']);
        if ($release === null || $release->hasBeenApproved() || $release->getCreatedBy() == $user->data()->id) {
            Session::flash('releases_errors', 'Could not find release or you do not have permission to approve this release, or it was already approved.');
            Redirect::to(URL::build('/panel/releases'));
        }

        DB::getInstance()->update('releases', $release->getId(), [
            'approved' => 1,
            'approved_by' => $user->data()->id,
            'approved_at' => time(),
        ]);

        GithubHelper::getInstance()->resetCache();

        Session::flash('releases_success', 'Approved Release: ' . $release->getName() . '. Users will begin to see this release in their StaffCP soon. I hope #support is open!');
        Redirect::to(URL::build('/panel/releases'));
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
        'ERRORS' => [Session::flash('releases_errors')]
    ));
}

if (Session::exists('releases_success')) {
    $smarty->assign(array(
        'SUCCESS_TITLE' => $language->get('general', 'success'),
        'SUCCESS' => Session::flash('releases_success')
    ));
}

$template->addJSScript(Input::createTinyEditor($language, 'install_instructions'));

$smarty->assign(array(
    'PAGE' => PANEL_PAGE,
    'PARENT_PAGE' => PARENT_PAGE,
    'DASHBOARD' => $language->get('admin', 'dashboard'),
    'TOKEN' => Token::get(),
    'NEW_LINK' => URL::build('/panel/releases', 'action=new'),
    'APPROVE_LINK' => URL::build('/panel/releases', 'action=approve'),
    'USER_ID' => $user->data()->id,
    'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
    'YES' => $language->get('general', 'yes'),
    'NO' => $language->get('general', 'no'),
    'CONFIRM_APPROVE_RELEASE' => 'Are you sure you want to approve this release? This will make it public and available to users.',
));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate($template_file, $smarty);
