<?php

namespace PhlyComic;

use JsonSerializable;

/**
 * Value object describing a comic
 */
class Comic implements JsonSerializable
{
    public static function createBaseComic(
        string $name,
        string $title,
        string $link,
    ): self {
        return new self($name, $title, $link);
    }

    /**
     * Implemented to allow debugging via json_encode
     */
    public function jsonSerialize(): array
    {
        return [
            'name'               => $this->name,
            'title'              => $this->title,
            'url'                => $this->url,
            'instance_url'       => $this->instanceUrl,
            'instance_image_url' => $this->instanceImageUrl,
            'error'              => $this->error,
        ];
    }

    public function withInstance(string $url, string $imageUrl): self
    {
        return new self(
            $this->name,
            $this->title,
            $this->url,
            $url,
            $imageUrl,
        );
    }

    public function withError(string $error): self
    {
        return new self(
            $this->name,
            $this->title,
            $this->url,
            $this->instanceUrl,
            $this->instanceImageUrl,
            $error,
        );
    }

    public function hasError(): bool
    {
        return null !== $this->error;
    }

    private function __construct(
        public readonly string $name,
        public readonly string $title,
        public readonly string $url,
        public readonly ?string $instanceUrl = null,
        public readonly ?string $instanceImageUrl = null,
        public readonly ?string $error = null,
    ) {
    }
}
