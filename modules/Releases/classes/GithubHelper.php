<?php

class GithubHelper extends Instanceable
{
    private Cache $cache;
    private array $githubReleases;

    private function __construct()
    {
        $this->cache = new Cache();
    }

    /**
     * Get array of all GitHub releases which have not been assigned to a NamelessMC release yet.
     * 
     * @return array GitHub releases
     */
    public function getGithubReleases(): array
    {
        if (isset($this->githubReleases)) {
            return $this->githubReleases;
        }

        $this->cache->setCache('releases');

        if ($this->cache->isCached('github_releases')) {
            return $this->githubReleases ??= $this->cache->retrieve('github_releases');
        }

        $githubReleases = [];

        $githubReleasesFromApi = $this->callGithubApi('https://api.github.com/repos/NamelessMC/Nameless/releases');

        foreach ($githubReleasesFromApi as $release) {
            if (!DB::getInstance()->get('releases', ['github_release_id', '=', $release->id])->count()) {
                $githubReleases[] = $release;
            }
        }

        $this->cache->store('github_releases', $githubReleases, 600);

        return $this->githubReleases ??= $githubReleases;
    }

    public function resetCache(): void
    {
        $this->cache->setCache('releases');

        if ($this->cache->isCached('github_releases')) {
            $this->cache->erase('github_releases');
        }
    }

    public function getGithubReleaseLinkFromId(int $githubReleaseId): string
    {
        $this->cache->setCache('releases');

        $cacheKey = "github_release_link-{$githubReleaseId}";

        if ($this->cache->isCached($cacheKey)) {
            return $this->cache->retrieve($cacheKey);
        }

        $releaseLink = $this->callGithubApi("https://api.github.com/repos/NamelessMC/Nameless/releases/{$githubReleaseId}")->html_url;
        $this->cache->store($cacheKey, $releaseLink, 600);

        return $releaseLink;
    }

    private function callGithubApi(string $url): object
    {
        return json_decode(
            HttpClient::get($url, [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: token ' . Config::get('github_token'),
            ])->data()
        );
    }
}