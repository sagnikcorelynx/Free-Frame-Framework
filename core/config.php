<?php

return [
    'debug' => getenv('APP_DEBUG') === 'true',
    'env' => getenv('APP_ENV') ?: 'production',
];