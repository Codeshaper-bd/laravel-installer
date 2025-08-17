<?php

if (! function_exists('isActive')) {
    /**
     * Set the active class to the current opened menu.
     *
     * @param  string|array $route
     * @param  string       $className
     * @return string
     */
    function isActive($route, $className = 'active')
    {
        if (is_array($route)) {
            return in_array(Route::currentRouteName(), $route) ? $className : '';
        }
        if (Route::currentRouteName() == $route) {
            return $className;
        }
        if (strpos(URL::current(), $route)) {
            return $className;
        }
    }
}

if (! function_exists('getCurrentDomain')) {
    /**
     * Get the current domain from the request.
     *
     * @return string
     */
    function getCurrentDomain()
    {
        $url = request()->url();
        $domain = parse_url($url, PHP_URL_HOST);
        return $domain ?: 'localhost';
    }
}

if (! function_exists('generateTenantDatabaseName')) {
    /**
     * Generate tenant database name based on prefix and domain.
     *
     * @param  string $prefix
     * @param  string $domain
     * @return string
     */
    function generateTenantDatabaseName($prefix, $domain = null)
    {
        if (!$domain) {
            $domain = getCurrentDomain();
        }
        
        $cleanDomain = preg_replace('/[^a-zA-Z0-9]/', '_', $domain);
        return $prefix . $cleanDomain;
    }
}
