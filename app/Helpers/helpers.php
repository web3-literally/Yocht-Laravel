<?php

if (!function_exists('relative_route')) {
    /**
     * Generate the URL to a named route.
     *
     * Example: Current rote is "dashboard.items.edit",
     * so relative_route('delete') will generate the URL for "dashboard.items.delete" with the same route parameters
     *
     * @param  string $name
     * @param  array $parameters
     * @param  bool $absolute
     * @return string
     */
    function relative_route($name, $parameters = [], $absolute = true)
    {
        $parts = explode('.', Request::route()->getName());
        $parts[count($parts)-1] = $name;

        return app('url')->route(implode('.', $parts), $parameters + Request::route()->parameters, $absolute);
    }
}