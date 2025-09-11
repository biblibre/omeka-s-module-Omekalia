<?php

function usage()
{
    return <<<TEXT
    Usage: php data/scripts/sync.php [options]
           php data/scripts/sync.php --help

    Options:
        -h, --help
            Print this message and exit

        --sync-modules
            Synchronize modules

        --sync-themes
            Synchronize themes
    TEXT;
}

$options = getopt(
    'h',
    [
        'help',
        'sync-modules',
        'sync-themes',
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

if (!isset($options['sync-modules']) && !isset($options['sync-themes'])) {
    fprintf(STDERR, "At least one of these options is required: --sync-modules, --sync-themes\n");
    exit(1);
}

if (isset($options['sync-modules'])) {
    syncModules();
}

if (isset($options['sync-themes'])) {
    syncThemes();
}

function syncModules()
{
    $modulesDir = dirname(__DIR__, 3);
    $omekaliaData = getOmekaliaData();
    foreach ($omekaliaData['modules'] as $moduleData) {
        $modulePath = sprintf('%s/%s', $modulesDir, $moduleData['name']);
        if (is_dir($modulePath)) {
            $moduleConfig = parse_ini_file("$modulePath/config/module.ini", true);
            if ($moduleConfig !== false && $moduleConfig['info']['version'] === $moduleData['version']) {
                fprintf(STDERR, "module %s: nothing to do\n", $moduleData['name']);
                continue;
            }

            fprintf(STDERR, "module %s: replacing %s with %s\n", $moduleData['name'], $moduleConfig['info']['version'], $moduleData['version']);
            [$tempDir] = execute(['mktemp', '-d', '-p', sys_get_temp_dir(), sprintf('omekalia-%s.XXXXXX', $moduleData['name'])]);
            execute(['git', 'clone', '--quiet', $moduleData['url'], $tempDir]);
            execute(['git', '-C', $tempDir, 'reset', '--hard', $moduleData['tag']]);

            if (false === move('module', $tempDir, $modulePath)) {
                continue;
            }
        } else {
            fprintf(STDERR, "module %s: installing (%s)\n", $moduleData['name'], $moduleData['version']);

            execute(['git', 'clone', '--quiet', $moduleData['url'], $modulePath]);
            execute(['git', '-C', $modulePath, 'reset', '--hard', $moduleData['tag']]);
        }

        if (file_exists("$modulePath/composer.json")) {
            fprintf(STDERR, "module %s: installing composer dependencies\n", $moduleData['name']);
            $composerPath = dirname($modulesDir) . '/build/composer.phar';
            execute(['php', $composerPath, 'install', '--no-interaction', '--quiet', '--no-dev', '--working-dir', $modulePath]);
        }
    }
}

function syncThemes()
{
    $themesDir = dirname(__DIR__, 4) . '/themes';
    $omekaliaData = getOmekaliaData();
    foreach ($omekaliaData['themes'] as $themeData) {
        $themePath = sprintf('%s/%s', $themesDir, $themeData['name']);
        if (is_dir($themePath)) {
            $themeConfig = parse_ini_file("$themePath/config/theme.ini", true);
            if ($themeConfig !== false && $themeConfig['info']['version'] === $themeData['version']) {
                fprintf(STDERR, "theme %s: nothing to do\n", $themeData['name']);
                continue;
            }

            fprintf(STDERR, "theme %s: replacing %s with %s\n", $themeData['name'], $themeConfig['info']['version'], $themeData['version']);
            [$tempDir] = execute(['mktemp', '-d', '-p', sys_get_temp_dir(), sprintf('omekalia-%s.XXXXXX', $themeData['name'])]);
            execute(['git', 'clone', '--quiet', $themeData['url'], $tempDir]);
            execute(['git', '-C', $tempDir, 'reset', '--hard', $themeData['tag']]);

            move('theme', $tempDir, $themePath);
        } else {
            fprintf(STDERR, "theme %s: installing (%s)\n", $themeData['name'], $themeData['version']);

            execute(['git', 'clone', '--quiet', $themeData['url'], $themePath]);
            execute(['git', '-C', $themePath, 'reset', '--hard', $themeData['tag']]);
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

function move(string $type, string $from, string $to)
{
    $name = basename($to);
    $dataDir = getenv('XDG_DATA_HOME') ?: sprintf('%s/.local/share', getenv('HOME'));
    $archiveDir = "$dataDir/omekalia/archive";
    $archivePath = sprintf(
        '%s/%s-%s-%s-%s',
        $archiveDir,
        $type,
        $name,
        date('YmdHis'),
        sodium_bin2base64(random_bytes(3), SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING)
    );

    if (!is_dir($archiveDir)) {
        if (false === mkdir($archiveDir, 0777, true)) {
            fprintf(STDERR, "%s %s: failed to create archive directory %s\n", $type, $name, $archiveDir);

            return false;
        }
    }

    if (false === rename($to, $archivePath)) {
        fprintf(STDERR, "%s %s: failed to move %s to %s\n", $type, $name, $to, $archivePath);

        return false;
    }

    if (false === rename($from, $to)) {
        fprintf(STDERR, "%s %s: failed to move %s to %s\n", $type, $name, $from, $to);
        fprintf(STDERR, "%s %s: restoring from the archive\n", $type, $name);
        if (false === rename($archivePath, $to)) {
            fprintf(STDERR, "%s %s: failed to restore from the archive\n", $type, $name);
        }

        return false;
    }

    fprintf(STDERR, "%s %s: archive saved in %s\n", $type, $name, $archivePath);

    return true;
}
