<?php
declare(strict_types=1);

namespace ScriptFUSION\Steam250\Import;

use ScriptFUSION\Mapper\AnonymousMapping;
use ScriptFUSION\Mapper\Strategy\Copy;
use ScriptFUSION\Mapper\Strategy\Merge;
use ScriptFUSION\Mapper\Strategy\TakeFirst;
use ScriptFUSION\Mapper\Strategy\TryCatch;
use ScriptFUSION\Porter\Provider\Steam\Resource\ApiResponseException;
use ScriptFUSION\Porter\Specification\ImportSpecification;
use ScriptFUSION\Porter\Transform\Mapping\Mapper\Strategy\SubImport;
use ScriptFUSION\Porter\Transform\Mapping\MappingTransformer;
use ScriptFUSION\Steam250\Resource\StaticSteamAppList;
use ScriptFUSION\Steam250\Transformer\ChunkingTransformer;

class GameReviewsListSpecification extends ImportSpecification
{
    public function __construct(string $appListPath, int $chunks, int $chunkIndex)
    {
        parent::__construct(new StaticSteamAppList($appListPath));

        $this->addTransformers([
            new ChunkingTransformer($chunks, $chunkIndex),
            new MappingTransformer(
                new AnonymousMapping(
                    new Merge(
                        [
                            'id' => new Copy('appid'),
                            'app_name' => new Copy('name'),
                        ],
                        new TryCatch(
                            new TakeFirst(
                                new SubImport(
                                    function (array $data): ImportSpecification {
                                        return new GameReviewsSpecification($data['appid']);
                                    }
                                )
                            ),
                            function (\Exception $exception): void {
                                if (!$exception instanceof ApiResponseException) {
                                    throw $exception;
                                }
                            },
                            null
                        )
                    )
                )
            ),
        ]);
    }
}
