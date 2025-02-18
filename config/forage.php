<?php

$account = env('FORAGE_ACCOUNT', '');

return [
    'account' => $account,
    'password' => env('FORAGE_PASSWORD', ''),
    'cookie_file' => storage_path("app/public/{$account}.json"),
];
