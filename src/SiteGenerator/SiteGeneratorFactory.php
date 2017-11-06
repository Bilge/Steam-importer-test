<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\SiteGenerator;

use ScriptFUSION\Steam250\Database\DatabaseFactory;

final class SiteGeneratorFactory
{
    public function create(): SiteGenerator
    {
        return new SiteGenerator(
            (new TwigFactory)->create(),
            (new DatabaseFactory)->create()
        );
    }
}
