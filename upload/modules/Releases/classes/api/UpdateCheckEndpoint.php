<?php

class UpdateCheckEndpoint extends EndpointBase
{
    public function __construct()
    {
        $this->_route = 'updateCheck';
        $this->_module = 'Releases';
        $this->_description = 'Check if NamelessMC install has an update available, and handle statistics tracking.';
        $this->_method = 'GET';
    }

    public function execute(Nameless2API $api)
    {
        $api->validateParams($_POST, ['uid', 'version']);

        $data = $_POST;

        if (!$this->isValidSiteId($data['uid'])) {
            $api->returnArray([
                'update_available' => false,
                'message' => 'Invalid site unique id'
            ]);
            return;
        }

        StatisticsHandler::getInstance()->handleRequest($data);

        $new_update = DB::getInstance()->query("SELECT * FROM nl2_releases WHERE required_version = ? ORDER BY created_at DESC LIMIT 1", [$data['version']]);

        if (!$new_update->count()) {
            $api->returnArray([
                'update_available' => false,
                'message' => 'No update available'
            ]);
            return;
        }

        $new_update = $new_update->results()[0];

        $api->returnArray([
            'update_available' => true,
            'name' => $new_update->name,
            'version_tag' => $new_update->version_tag,
            'download_link' => $new_update->download_link,
            'urgent' => (bool) $new_update->urgent,
            'install_instructions' => $new_update->install_instructions,
        ]);
    }

    private function isValidSiteId(string $id) {
        return true;
    }
}