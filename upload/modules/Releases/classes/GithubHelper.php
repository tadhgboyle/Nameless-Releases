<?php

class GithubHelper
{
    private static $_instance;
    /** @var Cache */
    private static $_cache;
    private $_github_releases;

    public static function getInstance(): self
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new GithubHelper;
            self::$_cache = new Cache;
        }

        return self::$_instance;
    }

    public function getGithubReleases()
    {
        if (!isset($this->_github_releases)) {
            self::$_cache->setCache('releases');

            if (self::$_cache->isCached('github_releases')) {
                $this->_github_releases = self::$_cache->retrieve('github_releases');
            } else {
                $releases_api = $this->callGithubApi('https://api.github.com/repos/NamelessMC/Nameless/releases');
                $releases = [];

                foreach ($releases_api as $release) {
                    if (!DB::getInstance()->get('releases', ['github_release_id', '=', $release->id])->count()) {
                        $releases[] = $release;
                    }
                }

                $this->_github_releases = $releases;

                self::$_cache->store('github_releases', $this->_github_releases, 600);
            }
        }

        return $this->_github_releases;
    }

    public function resetCache()
    {
        self::$_cache->setCache('releases');

        if (self::$_cache->isCached('github_releases')) {
            self::$_cache->erase('github_releases');
        }
    }

    public function getReleaseLinkFromId(int $release_id)
    {
        self::$_cache->setCache('releases');

        $cache_key = 'github_release_link-' . $release_id;

        if (self::$_cache->isCached($cache_key)) {
            return self::$_cache->retrieve($cache_key);
        }

        $release_link = $this->callGithubApi('https://api.github.com/repos/NamelessMC/Nameless/releases/' . $release_id)->html_url;
        self::$_cache->store($cache_key, $release_link, 600);

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