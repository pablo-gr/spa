<?php

use Symfony\Component\Dotenv\Dotenv;

if (isset($_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'])) {
    passthru(sprintf(
        'php "%s/console" cache:clear --env=%s --no-warmup',
        __DIR__ . '/../bin',
        $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV']
    ));

    passthru(sprintf(
        'php "%s/console" doctrine:database:drop --force --env=%s',
        __DIR__ . '/../bin',
        $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV']
    ));

    passthru(sprintf(
        'php "%s/console" doctrine:database:create --env=%s',
        __DIR__ . '/../bin',
        $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV']
    ));

    passthru(sprintf(
        'php "%s/console" doctrine:schema:update --force --complete --env=%s',
        __DIR__ . '/../bin',
        $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV']
    ));

    passthru(sprintf(
        'php "%s/console" doctrine:fixtures:load --env=%s --no-interaction',
        __DIR__ . '/../bin',
        $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV']
    ));
}


require dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(dirname(__DIR__) . '/config/bootstrap.php')) {
    require dirname(__DIR__) . '/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}
