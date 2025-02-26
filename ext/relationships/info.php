<?php

declare(strict_types=1);

namespace Shimmie2;

class RelationshipsInfo extends ExtensionInfo
{
    public const KEY = "relationships";

    public string $key = self::KEY;
    public string $name = "Post Relationships";
    public array $authors = ["Angus Johnston" => "admin@codeanimu.net"];
    public string $license = self::LICENSE_GPLV2;
    public string $description = "Allow posts to have relationships (parent/child).";
}
