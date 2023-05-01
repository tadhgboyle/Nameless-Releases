<?php

class ReleasesHelper extends Instanceable {

    private array $releases;

    /**
     * @return Release[]
     */
    public function getReleases(): array {
        if (isset($this->releases)) {
            return $this->releases;
        }

        $releases = [];

        $releasesFromDatabase = DB::getInstance()->query("SELECT * FROM nl2_releases ORDER BY created_at DESC")->results();

        foreach ($releasesFromDatabase as $release) {
            $releases[] = new Release([
                'id' => (int)$release->id,
                'name' => $release->name,
                'version_tag' => $release->version_tag,
                'required_version' => $release->required_version,
                'github_release_id' => $release->github_release_id,
                'github_link' => GithubHelper::getInstance()->getGithubReleaseLinkFromId($release->github_release_id),
                'upgrade_zip_link' => $release->upgrade_zip_link,
                'urgent' => (bool)$release->urgent,
                'checksum' => $release->checksum,
                'install_instructions' => $release->install_instructions,
                'created_at' => date(DATE_FORMAT, $release->created_at),
                'created_by' => $release->created_by,
                'approved' => (bool)$release->approved,
            ]);
        }

        return $this->releases ??= $releases;
    }

    public function getRelease(int $id): ?Release {
        foreach ($this->getReleases() as $release) {
            if ($release->getId() === $id) {
                return $release;
            }
        }

        return null;
    }

    public function getUpdateForVersion(string $version): ?Release {
        foreach ($this->getReleases() as $release) {
            if ($release->hasBeenApproved() && $release->getRequiredVersion() === $version) {
                return $release;
            }
        }

        return null;
    }
}
