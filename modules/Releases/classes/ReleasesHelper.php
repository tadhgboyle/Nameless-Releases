<?php

class ReleasesHelper extends Instanceable
{
    private array $releases;

    public function getReleases()
    {
        if (isset($this->releases)) {
            return $this->releases;
        }

        $releases = [];

        $releasesFromDatabase = DB::getInstance()->selectQuery("SELECT * FROM nl2_releases ORDER BY created_at DESC")->results();

        foreach ($releasesFromDatabase as $release) {
            $releases[] = [
                'id' => (int) $release->id,
                'name' => $release->name,
                'version_tag' => $release->version_tag,
                'required_version' => $release->required_version,
                'github_release_id' => $release->github_release_id,
                'github_link' => GithubHelper::getInstance()->getGithubReleaseLinkFromId($release->github_release_id),
                'urgent' => (bool) $release->urgent,
                'created_at' => strftime('%B %e, %Y @ %I:%M %p', $release->created_at),
                'install_instructions' => $release->install_instructions,
            ];
        }

        return $this->releases ??= $releases;
    }

    public function getRelease(int $id): ?object
    {
        foreach ($this->getReleases() as $release) {
            if ($release['id'] == $id) {
                return $release;
            }
        }

        return null;
    }

    public function getUpdateForVersion(string $version): ?object
    {
        foreach ($this->getReleases() as $release) {
            if ($release['required_version'] == $version) {
                return $release;
            }
        }

        return null;
    }
}