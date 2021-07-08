<?php

class ReleasesHelper
{
    private static $_instance;
    private $_releases;

    public static function getInstance(): self
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new ReleasesHelper;
        }

        return self::$_instance;
    }

    public function getReleases()
    {
        if (isset($this->_releases)) {
            return $this->_releases;
        }

        if (!isset($this->_releases_query)) {
            $this->_releases_query = DB::getInstance()->query("SELECT * FROM nl2_releases ORDER BY created_at DESC")->results();
        }

        $data = [];

        foreach ($this->_releases_query as $release) {
            $data[] = [
                'id' => $release->id,
                'name' => $release->name,
                'version_tag' => $release->version_tag,
                'required_version' => $release->required_version,
                'github_release_id' => $release->github_release_id,
                'github_link' => GithubHelper::getInstance()->getReleaseLinkFromId($release->github_release_id),
                'required_version' => $release->required_version,
                'urgent' => (bool) $release->urgent,
                'created_at' => strftime('%B %e, %Y @ %I:%M %p', $release->created_at),
                'install_instructions' => $release->install_instructions,
            ];
        }

        $this->_releases = $data;

        return $this->_releases;
    }

    public function getRelease($id)
    {
        foreach ($this->getReleases() as $release) {
            if ($release['id'] == $id) {
                return $release;
            }
        }

        return null;
    }

    public function getUpdateForVersion(string $version)
    {
        foreach ($this->getReleases() as $release) {
            if ($release['required_version'] == $version) {
                return $release;
            }
        }

        return null;
    }
}