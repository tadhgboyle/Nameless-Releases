<?php

class Release {

    private int $id;
    private string $name;
    private string $version_tag;
    private string $required_version;
    private int $github_release_id;
    private string $github_release_link;
    private bool $urgent;
    private string $created_at;
    private string $install_instructions;
    private bool $approved;

    public function __construct(array $data) {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getVersionTag(): string {
        return $this->version_tag;
    }

    public function getRequiredVersion(): string {
        return $this->required_version;
    }

    public function getGithubReleaseId(): int {
        return $this->github_release_id;
    }

    public function getGithubReleaseLink(): string {
        return $this->github_release_link;
    }

    public function isUrgent(): bool {
        return $this->urgent;
    }

    public function getCreatedAt(): string {
        return $this->created_at;
    }

    public function getInstallInstructions(): string {
        return $this->install_instructions;
    }

    public function isApproved(): bool {
        return $this->approved;
    }

    public function toArray(): array {
        return [
            'name' => $this->getName(),
            'version_tag' => $this->getVersionTag(),
            'required_version' => $this->getRequiredVersion(),
            'github_link' => $this->getGithubReleaseLink(),
            'urgent' => $this->isUrgent(),
            'install_instructions' => $this->getInstallInstructions(),
        ];
    }
}
