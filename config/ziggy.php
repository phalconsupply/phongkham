<?php

return [
    /*
     * By default, Ziggy will use the current request's URL to generate
     * the base URL for routes. This is usually fine, but in a multi-tenant
     * application, we want to ensure routes are always generated with the
     * correct domain (tenant subdomain or central domain).
     */
    'url' => null, // Use request URL (tenant-aware)
    
    /*
     * Only include routes that match these patterns
     */
    'only' => [],
    
    /*
     * Exclude routes that match these patterns
     */
    'except' => [],
];
