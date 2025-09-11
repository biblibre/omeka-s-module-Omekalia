<?php

function usage()
{
    return <<<TEXT
    Usage: php data/scripts/check-new-versions.php [options]
           php data/scripts/check-new-versions.php --help

    Options:
        -h, --help
            Print this message and exit
    TEXT;
}

$options = getopt(
    'h',
    [
        'help',
    ]
);

if ($options === false) {
    fprintf(STDERR, "%s\n", usage());
    exit(1);
}

if (isset($options['h']) || isset($options['help'])) {
    printf("%s\n", usage());
    exit(0);
}

checkModules();
checkThemes();

function checkModules()
{
    $modulesDir = dirname(__DIR__, 3);
    $omekaliaData = getOmekaliaData();
    foreach ($omekaliaData['modules'] as $moduleData) {
        $lsRemoteLines = execute(['git', 'ls-remote', '--refs', '--tags', '--sort=-version:refname', $moduleData['url']]);
        foreach ($lsRemoteLines as $lsRemoteLine) {
            [$sha, $refname] = explode("\t", $lsRemoteLine);
            $tag = str_replace('refs/tags/', '', $refname);
            $version = preg_replace('/^v/', '', $tag);
            if (version_compare($version, $moduleData['version']) > 0) {
                fprintf(STDERR, "New version available for %s: %s -> %s - %s\n", $moduleData['name'], $moduleData['version'], $version, $moduleData['url']);
                break;
            }
        }
    }
}

function checkThemes()
{
    $themesDir = dirname(__DIR__, 3);
    $omekaliaData = getOmekaliaData();
    foreach ($omekaliaData['themes'] as $themeData) {
        $lsRemoteLines = execute(['git', 'ls-remote', '--refs', '--tags', '--sort=-version:refname', $themeData['url']]);
        foreach ($lsRemoteLines as $lsRemoteLine) {
            [$sha, $refname] = explode("\t", $lsRemoteLine);
            $tag = str_replace('refs/tags/', '', $refname);
            $version = preg_replace('/^v/', '', $tag);
            if (version_compare($version, $themeData['version']) > 0) {
                fprintf(STDERR, "New version available for %s: %s -> %s - %s\n", $themeData['name'], $themeData['version'], $version, $themeData['url']);
                break;
            }
        }
    }
}

function getOmekaliaData()
{
    static $omekaliaData = null;

    if (null === $omekaliaData) {
        $omekaliaDataJson = file_get_contents(dirname(__DIR__) . '/omekalia.json');
        $omekaliaData = json_decode($omekaliaDataJson, true, 512, JSON_THROW_ON_ERROR);
    }

    return $omekaliaData;
}

function execute(array $argv): array
{
    $command = implode(' ', array_map('escapeshellarg', $argv));

    $output = null;
    $result_code = null;
    exec($command, $output, $result_code);

    if ($result_code !== 0) {
        throw new \Exception(sprintf('Command failed: %s', $command));
    }

    return $output;
}
