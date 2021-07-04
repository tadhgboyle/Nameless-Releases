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

    public function getReleases(): array
    {
        if (!isset($this->_releases)) {
            $this->_releases = DB::getInstance()->query("SELECT * FROM nl2_releases ORDER BY created_at DESC")->results();
        }

        return $this->_releases;
    }

    public function getUpdateForVersion(string $version) {

        foreach ($this->getFormattedReleases() as $release) {
            if ($release['required_version'] == $version) {
                return $release;
            }
        }

        return null;
    }

    public function getFormattedReleases()
    {
        $data = [];

        foreach ($this->getReleases() as $release) {
            $data[] = $this->formatDataForRelease($release);
        }

        return $data;
    }

    public function formatDataForRelease($release)
    {
        return [
            'name' => $release->name,
            'version_tag' => $release->version_tag,
            'required_version' => $release->required_version,
            'download_link' => $this->getReleaseLinkFromId($release->github_release_id),
            'required_version' => $release->required_version,
            'urgent' => (bool) $release->urgent,
            'created_at' => strftime('%B %e, %Y @ %I:%M %p', $release->created_at),
            'install_instructions' => $release->install_instructions,
        ];
    }

    private function getReleaseLinkFromId(int $release_id)
    {

        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)');
        curl_setopt($c, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
        curl_setopt($c, CURLOPT_URL, 'https://api.github.com/repos/NamelessMC/Nameless/releases/' . $release_id);

        $content = curl_exec($c);
        curl_close($c);

        $data = json_decode($content);

        return $data->html_url;
    }
}