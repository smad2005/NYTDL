<?php

require_once './library.php';

$videoexts = array(
    'mp4',
    'mkv',
    'avi'
);
define('SUBEXTS', 'ass|ssa|srt'); // Часть регекспа с расширениями файлов субтитров
define('LINKSUFFIX', 'http://www.nyaa.se/?page=search&cats=0_0&filter=0&term='); // Страница поиска субтитров
// %appdata%\uTorrent\uTorrent.exe
// http://forum.utorrent.com/topic/46012-utorrent-command-line-options/
define('TMPDIR', sys_get_temp_dir()); // Папка для временных файлов
define('TMPPRFX', 'vknkk-nytdl'); // Префикс для наших временных файлов
define('USERAGENT', 'Android-x86-1.6-r2 — Mozilla/5.0 (Linux; U; Android 1.6; en-us; eeepc Build/Donut) AppleWebKit/528.5+ (KHTML, like Gecko) Version/3.1.2 Mobile Safari/525.20.1');

// Удаляем временные файлы, оставшиеся с прошлого раза
if ($tmpFiles = glob(TMPDIR . '/' . TMPPRFX . '*.*'))
    foreach ($tmpFiles as $tmpFl)
        unlink($tmpFl);

// Получаем список файлов субтитров, для которых в папке, из который был вызван скрипт, отсутствуют видеофайлы
$torrents = array();
if (isset($argv[1])) {

    $json_config = file_get_contents("config.json");
    $config = json_decode($json_config);
    $torrentPath = $config->{"TorrentPath"} or $torrentPath = $config["TorrentPath"];
    define('TORCLI', $torrentPath); // Путь к торрент-клиенту

    $dirname = dirname($argv[1]);
    if ($dirname)
        if ($files = scandir($dirname))
            foreach ($files as $fileName)
                if (preg_match('/^(.*)\.(?:' . SUBEXTS . ')$/', $fileName, $subsMatch)) {
                    $videoFound = false;
                    foreach ($videoexts as $ext)
                        if (in_array($subsMatch[1] . ".$ext", $files)) {
                            $videoFound = true;
                            break;
                        }
                    if (!$videoFound)
                        $torrents[] = $subsMatch[1];
                }
}

$dirname = escapeshellarg($dirname);
if ($torrents) {
    $tmpSearchHTML = '';
    $context = stream_context_create(array(
        'http' => array(
            'timeout' => 20000,
            'user_agent' => USERAGENT
        )
    ));

    $torfiles = array();
    foreach ($torrents as $k => $torname) {
        $name = substr($torname, 0, ($pos = strpos($torname, '(', 5)) ? $pos + 1 : 60);
        $linkname = rawurlencode('"' . $name . '"');
        $urlPath = LINKSUFFIX . $linkname;
        $html = file_get_contents($urlPath, false, $context) or ( $html = file_get_contents($urlPath));
        if (preg_match('~<div class="viewdownloadbutton">\s*<a href="([^"]*tid=(\d+)[^"]*)~', $html, $linkmath)) {
            // Найдена ссылка на загрузку файла - качаем
            $tmpFl = mktmpfile($linkmath[2] . '.torrent');
            $dwnUrl = html_entity_decode($linkmath[1]);
            copy($dwnUrl, $tmpFl, $context) or ( file_put_contents($tmpFl, file_get_contents(str_replace("&#38;", "&", $dwnUrl))));
            $runComand = TORCLI . ' /DIRECTORY ' . $dirname . ' ' . escapeshellarg($tmpFl);
            exec($runComand);
        } else {
            // Файл не найден - будем выводить ссылку на поиск
            $tmpSearchHTML .= '<p><a target="_blank" href="' . LINKSUFFIX . $linkname . '">' . $torname . '</a></p>';
        }
    }

    if ($tmpSearchHTML) {
        // Вывод ссылок на поиск для файлов, которые небыли найдены
        file_put_contents($tmpFl = mktmpfile('htm'), $tmpSearchHTML);
        system($tmpFl);
    }
}

function mktmpfile($ext) {
    $name = tempnam(TMPDIR, TMPPRFX);
    if (substr($name, -4) == '.tmp' && rename($name, $newname = substr($name, 0, -4)))
        $name = $newname;
    if (rename($name, $newname = "$name.$ext"))
        $name = $newname;
    return $name;
}
