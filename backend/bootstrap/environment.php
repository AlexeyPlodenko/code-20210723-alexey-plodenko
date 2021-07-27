<?php
use Dotenv\Dotenv;

$env = $app->detectEnvironment(function() {
    $environmentPath = __DIR__ . '/../.env';
    if (file_exists($environmentPath)) {
        $setEnv = trim(file_get_contents($environmentPath));

        $platform = getenv('PLATFORM');
        if ($platform !== 'docker') {
            $setEnv = 'local';
        }

        putenv('APP_ENV=' . $setEnv);
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../environments/', '.' . getenv('APP_ENV') . '.env');
        $dotenv->load(); // this is important
    }
});
