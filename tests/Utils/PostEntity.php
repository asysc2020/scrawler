<?php

declare(strict_types=1);

namespace Tests\Utils;

class PostEntity
{
    protected $content;

    protected $title;

    public function getContent()
    {
        return $this->content;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setContent($content): void
    {
        $this->content = $content;
    }

    public function setTitle($title): void
    {
        $this->title = $title;
    }
}
