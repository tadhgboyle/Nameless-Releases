<?php

class AllUpdatesEndpoint extends NoAuthEndpoint {

    public function __construct() {
        $this->_route = 'allUpdates';
        $this->_module = 'Releases';
        $this->_description = 'Get all NamelessMC updates';
        $this->_method = 'GET';
    }

    public function execute(Nameless2API $api) {
        $data = [];

        foreach (ReleasesHelper::getInstance()->getReleases() as $release) {
            if (!$release->hasBeenApproved()) {
                continue;
            }

            $data[] = $release->toArray();
        }

        die(json_encode($data));
    }

}
