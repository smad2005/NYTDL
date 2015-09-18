<?php
require_once 'library.php';
define('VIDEO_EXTS', 'mp4|mkv|avi');
define('SUBEXTS', 'ass|ssa|srt'); // Часть регекспа с расширениями файлов субтитров
define('LINKSUFFIX', 'http://www.nyaa.se/?page=search&cats=0_0&filter=0&term='); // Страница поиска субтитров
// %appdata%\uTorrent\uTorrent.exe
// http://forum.utorrent.com/topic/46012-utorrent-command-line-options/
define('TMPDIR', sys_get_temp_dir()); // Папка для временных файлов
define('TMPPRFX', 'vknkk-nytdl'); // Префикс для наших временных файлов
define('USERAGENT', 'Android-x86-1.6-r2 — Mozilla/5.0 (Linux; U; Android 1.6; en-us; eeepc Build/Donut) AppleWebKit/528.5+ (KHTML, like Gecko) Version/3.1.2 Mobile Safari/525.20.1');
$jsonConfigPath = 'config.json';
// Удаляем временные файлы, оставшиеся с прошлого раза
if ($tmpFiles = glob(TMPDIR . '/' . TMPPRFX . '*.*'))
    foreach ($tmpFiles as $tmpFl)
        unlink($tmpFl);

// Получаем список файлов субтитров, для которых в папке, из который был вызван скрипт, отсутствуют видеофайлы
$subtitlesList = array();
$dirname = null;
if (isset($argv[1])) {
    initConfig($jsonConfigPath);
    if (!file_exists(getPathWithEnv(TORCLI))) {
        echo TORCLI . " file not found, please check config.json";
        sleep(10);
        return;
    }
    $dirname = dirname($argv[1]);
    $subtitlesList = initSubtitlesList($dirname);
}
$dirInfo = array("dirnameRaw" => $dirname, "dirname" => escapeshellarg($dirname));
if ($subtitlesList) {
    $tmpSearchHTML = '';
    $context = stream_context_create(array('http' => array('timeout' => 20000, 'user_agent' => USERAGENT)));

    foreach ($subtitlesList as $sub)
        $tmpSearchHTML.= handleSubtitleFile($dirInfo, $sub, $context);


    if ($tmpSearchHTML) {
// Вывод ссылок на поиск для файлов, которые небыли найдены
        file_put_contents($tmpFl = mktmpfile('htm'), $tmpSearchHTML);
        system($tmpFl);
    }
}

function tryGetNameFromASS($path) {
    if (file_exists($path)) {
        $content = file_get_contents($path);
        if (preg_match("/^Video File:\s*(.*)\s*$/m", $content, $match)) {
            $name = get_basename($match[1]);
            return $name;
        }
    }
    return null;
}

function mktmpfile($ext) {
    $name = tempnam(TMPDIR, TMPPRFX);
    if (substr($name, -4) == '.tmp' && rename($name, $newname = substr($name, 0, -4)))
        $name = $newname;
    if (rename($name, $newname = "$name.$ext"))
        $name = $newname;
    return $name;
}

function initSubtitlesList($dirname) {
    $subtitlesList = null;
    if ($dirname) {
        if ($files = scandir($dirname)) {
            foreach ($files as $fileName) {
                if (preg_match('/^(.*)\.(' . SUBEXTS . ')$/', $fileName, $subsMatch)) {
                    $videoFound = false;

                    foreach (explode('|', VIDEO_EXTS) as $ext) {
                        if (in_array($subsMatch[1] . ".$ext", $files)) {
                            $videoFound = true;
                            break;
                        }
                    }
                    if (!$videoFound) {
                        $subtitlesList[] = array("name" => $subsMatch[1], "flag" => 0, "ext" => $subsMatch[2]);
                    }
                }
            }
        }
    }
    return $subtitlesList;
}

function initConfig($jsonConfigPath) {
    $json_config = file_get_contents($jsonConfigPath);
    $config = json_decode($json_config);
    $torrentPath = $config->{"TorrentPath"} or $torrentPath = $config["TorrentPath"];
    define('TORCLI', $torrentPath); // Путь к торрент-клиенту
}

function handleSubtitleFile($dirInfo, $torname, $context) {
    $dirnameRaw = $dirInfo["dirnameRaw"];
    $dirname = $dirInfo["dirname"];
    $name = $torname["name"];
    $linkname = rawurlencode($name);
    $urlPath = LINKSUFFIX . $linkname;
    $html = @file_get_contents($urlPath, false, $context) or ( $html = file_get_contents($urlPath));
    if (preg_match('~<div class="viewdownloadbutton">\s*<a href="([^"]*tid=(\d+)[^"]*)~', $html, $linkmath)) {
// Найдена ссылка на загрузку файла - качаем
        $tmpFl = mktmpfile($linkmath[2] . '.torrent');
        $dwnUrl = html_entity_decode($linkmath[1]);
        @copy($dwnUrl, $tmpFl, $context) or ( file_put_contents($tmpFl, file_get_contents(str_replace("&#38;", "&", $dwnUrl))));
        $runComand = TORCLI . ' /DIRECTORY ' . $dirname . ' ' . escapeshellarg($tmpFl);

        if (preg_match('~<td class="viewtorrentname">(.*?)</td>~', $html, $torFileName)) {
            $torFileName = htmlentities2utf8($torFileName[1]);
            $torFileNameWithoutExt = get_path_without_ext($torFileName);
            if (!$torname["flag"] && strcasecmp($torname["name"], $torFileNameWithoutExt) != 0) {
                $torname["oldname"] = $dirnameRaw . "/" . $torname["name"] . ".$torname[ext]";
                $torname["name"] = $torFileNameWithoutExt;
            }
        }
        if (isset($torname["oldname"])) {
            my_rename($torname["oldname"], $dirnameRaw . "/" . trim($torname["name"] . ".$torname[ext]"));
        }
        exec($runComand);
    } else {
        $fullname = $dirnameRaw . "/$torname[name].ass";
        if (!$torname["flag"] && ($nameFromAss = tryGetNameFromASS($fullname))) {
            $torname = array("name" => get_path_without_ext($nameFromAss), "flag" => 1, "oldname" => $fullname, "ext" => $torname["ext"]);
            return handleSubtitleFile($dirInfo, $torname, $context);
        } else {
// Файл не найден - будем выводить ссылку на поиск
            return '<p><a target="_blank" href="' . LINKSUFFIX . $linkname . '">' . $torname["name"] . '</a></p>';
        }
    }
}

function pathWithEnv_callback($match) {
    return getenv($match[1]);
}
function getPathWithEnv($path) {
    return preg_replace_callback("|%(.*?)%|", 'pathWithEnv_callback', $path);
}
?>