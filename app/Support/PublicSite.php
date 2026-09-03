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

        $parameters = $route?->parameters() ?? [];
        $queryKeys = match ($baseName) {
            'products.index' => ['category', 'page'],
            'inquiries.create' => ['product'],
            default => [],
        };

        // Only carry page-specific scalar query values; route parameters always win.
        foreach ($queryKeys as $key) {
            $value = request()->query($key);

            if (! is_string($value) || trim($value) === '') {
                continue;
            }

            if ($key === 'page' && filter_var($value, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1],
            ]) === false) {
                continue;
            }

            $parameters += [$key => trim($value)];
        }

        return route(self::routeName($baseName, $locale), $parameters);
    }

    private static function routeName(string $name, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'zh' ? 'zh.'.$name : $name;
    }
}
