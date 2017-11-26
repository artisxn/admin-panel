<?php

namespace InetStudio\AdminPanel\Http\Controllers\Back\Traits;

use InetStudio\AdminPanel\Events\Auth\ChangeMetaEvent;

trait MetaManipulationsTrait
{
    /**
     * Сохраняем мета теги.
     *
     * @param $item
     * @param $request
     */
    private function saveMeta($item, $request): void
    {
        if ($request->filled('meta')) {
            foreach ($request->get('meta') as $key => $value) {
                $item->updateMeta($key, $value);
            }

            event(new ChangeMetaEvent($item));
        }
    }
}