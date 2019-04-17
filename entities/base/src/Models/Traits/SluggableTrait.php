<?php

namespace InetStudio\AdminPanel\Base\Models\Traits;

use Cocur\Slugify\Slugify;
use Illuminate\Database\Eloquent\Builder;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

/**
 * Trait SluggableTrait.
 */
trait SluggableTrait
{
    use SluggableScopeHelpers;

    /**
     * Выборка объекта по slug.
     *
     * @param  Builder  $query
     * @param string $slug
     * @param array $params
     *
     * @return mixed
     */
    public function scopeItemBySlug(Builder $query, string $slug, array $params = []): Builder
    {
        return $query->buildQuery($params)->whereSlug($slug);
    }

    /**
     * Правила для транслита.
     *
     * @param  Slugify  $engine
     *
     * @return Slugify
     */
    public function customizeSlugEngine(Slugify $engine)
    {
        $rules = [
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'jo',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'j',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'shh',
            'ъ' => '',
            'ы' => 'y',
            'ь' => '',
            'э' => 'je',
            'ю' => 'ju',
            'я' => 'ja',
        ];

        $engine->addRules($rules);

        return $engine;
    }
}
