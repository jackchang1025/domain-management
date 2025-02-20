<?php

$account = env('SHORT_URL_ACCOUNT');

return [
    'username' => $account,
    'password' => env('SHORT_URL_PASSWORD'),
    'cookie_file' => storage_path("app/public/short-url-{$account}.json"),
];
