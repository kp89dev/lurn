<?php

namespace Deployer;

host('34.207.90.53')
    ->stage('stage')
    ->user('deployer')
    ->set('branch', 'master')
    ->multiplexing(false)
    ->identityFile('~/.ssh/lc_deploy');

//server('production', '52.20', '2222')
//    ->user('polls')
//    ->env('branch', 'master')
//    ->identityFile();

set('ssh_type', 'native');
set('deploy_path', '/var/www/lurn-central');
set('timezone', 'UTC');
set('release_path', function () {
    static $release_path;

    if (! $release_path) {
        $release_path = str_replace("\n", '', run("readlink {{deploy_path}}/release"));
    }

    return $release_path;
});

set('releases_list', function () {
    $list = run('ls -dt {{deploy_path}}/releases/*')->toArray();

    return $list;
});
set('repository', 'git@bitbucket.org:aiellestad/lurn-central.git');
set('keep_releases', 10);

//argument('stage', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Run tasks only on this server or group of servers.');

task('deploy:release', function () {
    $release = date('M_d_H_i_s');
    $releasePath = "{{deploy_path}}/releases/$release";
    run("mkdir $releasePath && if [ -h {{deploy_path}}/release ]; then rm {{deploy_path}}/release; fi && ln -s $releasePath {{deploy_path}}/release");
})->desc('Prepare new directory release');

task('deploy:update_code', function() {
    $repository = get('repository');

    run("git clone -b {{branch}} -q --depth 1 $repository  {{release_path}}");
})->desc('Updating code');

task('deploy:set_env', function() {
    run("cp -p {{ deploy_path }}/shared_files/.env {{release_path}}/.env");
})->desc('Set env file');

task('deploy:setup_vendors', function() {
    run('ln -nfs {{ deploy_path }}/shared_files/vendor {{ release_path }}/vendor');
})->desc('Create symlinks for vendor folders');

task('deploy:update_vendors', function(){
    run("cd {{release_path}} && composer install --no-dev");
})->desc('Install libs from composer.lock');

task('deploy:setup_drupal_vendors', function() {
    run('ln -nfs {{ deploy_path }}/shared_files/drupal_vendor {{ release_path }}/drupal/vendor');
})->desc('Create symlinks for drupal vendor folders');

task('deploy:update_drupal_vendors', function(){
   run("cd {{release_path}}/drupal && composer install --no-dev");
})->desc('Install libs from composer.lock');

task('deploy:setup_storage', function(){
    run("rm -rf {{release_path}}/storage && ln -nfs {{ deploy_path }}/shared_files/storage {{release_path}}/storage");
})->desc('Set up storage directoy');

task('deploy:setup_static', function(){
    run("ln -nfs {{ deploy_path }}/shared_files/static {{  release_path }}/public/static");
})->onStage('stage');

task('deploy:set_permissions', function(){
    run("chmod -R a+w {{release_path}}/bootstrap/cache");
})->desc('Change permissions');

task('deploy:symlink', function(){
    run("ln -nfs {{release_path}} {{deploy_path}}/live");
})->desc('Create symlink');

task('deploy:artisan_optimize', function(){
    //run("php {{release_path}}/artisan config:cache && php {{release_path}}/artisan route:cache");
    //run("php artisan config:cache");
    run("php artisan cache:clear");
})->desc('Cache config and route');

task('deploy:migrate_db', function(){
    run("php {{release_path}}/artisan migrate --force --no-interaction");
})->desc('Migrate Database');

task('deploy:clear_op_cache', function(){
    run("php ~/cachetool.phar opcache:reset --fcgi=/var/run/php/php7.1-fpm.sock");
})->desc('Clear OPCACHE');

task('site:up', function () {
    $output = run('php {{deploy_path}}/live/artisan up');
    writeln('<info>'.$output.'</info>');
})->desc('Disable maintenance mode');

task('site:down', function () {
    $output = run('php {{deploy_path}}/live/artisan down');
    writeln('<error>'.$output.'</error>');
})->desc('Enable maintenance mode');

task('cleanup', function () {
    $releases = get('releases_list');
    $keep = get('keep_releases');
    while ($keep > 0) {
        array_shift($releases);
        --$keep;
    }
    foreach ($releases as $release) {
        run("rm -rf $release");
    }
    run("cd {{deploy_path}} && if [ -e release ]; then rm release; fi");
    run("cd {{deploy_path}} && if [ -h release ]; then rm release; fi");
})->desc('Cleaning up old releases');

task('rollback:code', function () {
    $releases = get('releases_list');

    if (isset($releases[1])) {
        $releaseDir = "{$releases[1]}";

        // Symlink to old release.
        run("cd {{deploy_path}} && ln -nfs $releaseDir live && php $releaseDir/artisan config:cache && php $releaseDir/artisan route:cache");

        writeln("Rollback to `{$releases[1]}` release was successful.");
        writeln("<comment>Note: You may consider running `dep rollback:db` if that's the case</comment>");

        // Remove release
        run("rm -rf {$releases[0]}");
    } else {
        writeln("<comment>No more releases you can revert to.</comment>");
    }
})->desc('Rollback to previous release');

task('deploy:confirm', function () {
    if (! askConfirmation("You are deploying live. Are you sure ?", false)) {
        writeln('<error>Deployment aborted!</error>');
        exit();
    }
})->onStage('production');

task('deploy', [
    'deploy:confirm',
    'deploy:release',
    'deploy:update_code',
    'deploy:set_env',
    'deploy:setup_vendors',
    'deploy:update_vendors',
    'deploy:setup_drupal_vendors',
    'deploy:update_drupal_vendors',
    'deploy:setup_storage',
    'deploy:setup_static',
    'deploy:set_permissions',
    'site:down',
    'deploy:symlink',
    'deploy:artisan_optimize',
    'deploy:migrate_db',
    'deploy:clear_op_cache',
    'site:up',
    'cleanup'
])->desc('Deploy a new release');

task('rollback', [
    'rollback:code',
    'deploy:clear_op_cache',
    'site:up'
]);
