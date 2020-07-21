<?php

$proxies = env('TRUSTED_PROXY', []);
if (is_string($proxies) && $proxies !== '*') {
    $proxies = [$proxies];
}

return [
    'proxies' => $proxies
];
