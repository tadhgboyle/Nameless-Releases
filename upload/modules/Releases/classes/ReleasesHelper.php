<?php

class ReleasesHelper
{
    private static $_instance;
    /** @var Cache */
    private static $_cache;
    private $_releases;
    private $_github_releases;

    public static function getInstance(): self
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new ReleasesHelper;
            self::$_cache = new Cache;
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
                'github_link' => $this->getReleaseLinkFromId($release->github_release_id),
                'required_version' => $release->required_version,
                'urgent' => (bool) $release->urgent,
                'created_at' => strftime('%B %e, %Y @ %I:%M %p', $release->created_at),
                'install_instructions' => $release->install_instructions,
            ];
        }

        $this->_releases = $data;

        return $this->_releases;
    }

    public function getGithubReleases()
    {
        if (!isset($this->_github_releases)) {
            self::$_cache->setCache('releases');

            if (self::$_cache->isCached('github_releases')) {
                $this->_github_releases = self::$_cache->retrieve('github_releases');
            } else {
                $this->_github_releases = $this->callGithubApi('https://api.github.com/repos/NamelessMC/Nameless/releases');
                self::$_cache->store('github_releases', $this->_github_releases, 3600);
            }
        }

        return $this->_github_releases;
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

    private function getReleaseLinkFromId(int $release_id)
    {
        self::$_cache->setCache('releases');

        $cache_key = 'github_release_link-' . $release_id;

        if (self::$_cache->isCached($cache_key)) {
            return self::$_cache->retrieve($cache_key);
        }

        $release_link = $this->callGithubApi('https://api.github.com/repos/NamelessMC/Nameless/releases/' . $release_id)->html_url;
        self::$_cache->store($cache_key, $release_link, 3600);

        return $release_link;
    }

    private function callGithubApi(string $url)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)');
        curl_setopt($c, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: token ' . Config::get('github_token'),
        ]);
        curl_setopt($c, CURLOPT_URL, $url);

        $content = curl_exec($c);
        curl_close($c);

        return json_decode($content);
    }
}