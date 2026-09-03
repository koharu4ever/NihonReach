<?php

namespace App\Support;

class PublicSite
{
    public static function route(string $name, mixed $parameters = []): string
    {
        return route(self::routeName($name), $parameters);
    }

    public static function switchUrl(string $locale): string
    {
        $route = request()->route();
        $currentName = $route?->getName() ?? 'home';
        $baseName = str_starts_with($currentName, 'zh.')
            ? substr($currentName, 3)
            : $currentName;

        $parameters = array_merge(
            $route?->parameters() ?? [],
            request()->query(),
        );

        return route(self::routeName($baseName, $locale), $parameters);
    }

    private static function routeName(string $name, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'zh' ? 'zh.'.$name : $name;
    }
}
