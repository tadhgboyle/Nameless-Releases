<?php

class LatestUpdateEndpoint extends NoAuthEndpoint {

    public function __construct() {
        $this->_route = 'latestUpdate';
        $this->_module = 'Releases';
        $this->_description = 'Get the latest NamelessMC update';
        $this->_method = 'GET';
    }

    public function execute(Nameless2API $api) {
        $latest_update = null;

        foreach (ReleasesHelper::getInstance()->getReleases() as $release) {
            if ($release->hasBeenApproved()) {
                $latest_update = $release;
                break;
            }
        }

        if ($latest_update == null) {
            die(json_encode([
                'error' => true,
                'message' => 'no updates available.'
            ]));
        }

        die(json_encode(
            $latest_update->toArray()
        ));
    }

}
