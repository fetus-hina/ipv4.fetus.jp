<?php //phpcs:disable

declare(strict_types=1);

namespace Deployer;

require 'recipe/yii2-app-basic.php';

set('application', 'ipv4.fetus.jp');
set('repository', 'git@github.com:fetus-hina/ipv4.fetus.jp.git');
set('composer_options', implode(' ', [
    'install',
    '--no-interaction',
    '--no-progress',
    '--no-suggest',
    '--optimize-autoloader',
    '--prefer-dist',
]));
set('git_tty', true);
add('shared_files', [
    'config/components/db/password.php',
    'config/components/web/request--cookie.php',
    'config/params/adsense.php',
    'config/params/database-update-timestamp.php',
]);
add('shared_dirs', [
    'runtime',
]);
add('writable_dirs', [
    'runtime',
    'web/assets',
]);
set('writable_mode', 'chmod');
set('writable_chmod_recursive', false);
set('softwarecollections', []);

set('bin/php', function () {
    if ($scl = get('softwarecollections')) {
        return vsprintf('scl enable %s -- php', [
            implode(' ', array_map(
                'escapeshellarg',
                $scl
            )),
        ]);
    }

    return locateBinaryPath('php');
});

set('bin/npm', function () {
    if ($scl = get('softwarecollections')) {
        return vsprintf('scl enable %s -- npm', [
            implode(' ', array_map(
                'escapeshellarg',
                $scl
            )),
        ]);
    }

    return locateBinaryPath('npm');
});

set('bin/composer', fn (): string => sprintf('%s/composer.phar', get('release_path')));

host('2401:2500:102:1206:133:242:147:83')
    ->user('ipv4')
    ->stage('production')
    ->roles('app')
    ->set('deploy_path', '~/app');

task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:git_config',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:production',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'deploy:run_migrations',
    'deploy:build',
    'deploy:vendors_production',
    'deploy:flush_cache',
    'deploy:symlink',
    'deploy:clear_opcache',
    'deploy:clear_proxy',
    'deploy:unlock',
    'cleanup',
])->desc('Deploy the project');

task('deploy:git_config', function () {
    run('git config --global advice.detachedHead false');
});

task('deploy:production', function () {
    within('{{release_path}}', function () {
        run('touch .production');
        run('rm -f web/index-test.php');
    });
});

task('deploy:vendors', function () {
    within('{{release_path}}', function () {
        run('make composer.phar');
        run('{{bin/composer}} {{composer_options}}');
        run('{{bin/npm}} clean-install');
    });
});

task('deploy:vendors_production', function () {
    within('{{release_path}}', function () {
        run('{{bin/composer}} --no-dev {{composer_options}}');
        run('{{bin/npm}} prune --production');
    });
});

task('deploy:run_migrations', function () {
    within('{{release_path}}', function () {
        run('{{bin/php}} ./yii migrate up --interactive=0');
    });
});

task('deploy:build', function () {
    within('{{release_path}}', function () {
        if ($scl = get('softwarecollections')) {
            run(vsprintf('scl enable %s -- make', [
                implode(' ', array_map(
                    'escapeshellarg',
                    $scl
                )),
            ]));
        } else {
            run('make');
        }
    });
});

task('deploy:flush_cache', function () {
    within('{{release_path}}', function () {
        run('{{bin/php}} ./yii cache/flush-all --interactive=0');
    });
});

task('deploy:clear_opcache', function () {
    run('curl -f --insecure --resolve ipv4.fetus.jp:443:127.0.0.1 https://ipv4.fetus.jp/site/clear-opcache');
});

task('deploy:clear_proxy', function () {
});

after('deploy:failed', 'deploy:unlock');
