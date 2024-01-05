<?php

declare(strict_types=1);

namespace Shimmie2;

class AuthInfo extends ExtensionInfo
{
    public const KEY = "auth";

    public string $key = self::KEY;
    public string $name = "Auth";
    public string $url = "";
    public array $authors = ["nodate"];
    public string $description = "";
    public ExtensionVisibility $visibility = ExtensionVisibility::ADMIN;
}
