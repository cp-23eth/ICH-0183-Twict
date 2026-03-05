<?php

declare(strict_types=1);

namespace App\Models;

use \Core\Model;

use function Core\Libs\array_path_explode;
use function Core\Libs\array_path_export;

abstract class AppModel extends Model
{
    protected static function expandRelationships(?array $models): ?array
    {
        if ($models === null) {
            return null;
        }

        if (!array_is_list($models)) {
            $models = self::expandRelationships([$models]);
            return array_shift($models);
        }

        for ($i = 0; $i < count($models); $i++) {
            $model = $models[$i];
            $model = array_path_explode('.', $model);
            $model = array_path_export($model);
            $models[$i] = $model;
        }

        return $models;
    }
}
