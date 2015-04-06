<?php

if (!function_exists('sys_get_temp_dir')) {

    function sys_get_temp_dir() {
        if (!empty($_ENV['TMP'])) {
            return realpath($_ENV['TMP']);
        }
        if (!empty($_ENV['TMPDIR'])) {
            return realpath($_ENV['TMPDIR']);
        }
        if (!empty($_ENV['TEMP'])) {
            return realpath($_ENV['TEMP']);
        }
        $tempfile = tempnam(__FILE__, '');
        if (file_exists($tempfile)) {
            unlink($tempfile);
            return realpath(dirname($tempfile));
        }
        return null;
    }

}

if (!function_exists('scandir')) {

    function scandir($path) {
        $files = array();
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                $files[] = $file;
            }
            closedir($handle);
            return $files;
        }
    }

}


if (!function_exists('file_put_contents')) {

    function file_put_contents($path, $data) {

        $file = fopen($path, "w");
        fwrite($file, $data);
        fclose($file);
    }

}

if (!function_exists('json_decode')) {

    function json_decode($json) {
        // Author: walidator.info 2009
        $comment = false;
        $out = '$x=';

        for ($i = 0; $i < strlen($json); $i++) {
            if (!$comment) {
                if ($json[$i] == '{')
                    $out .= ' array(';
                else if ($json[$i] == '}')
                    $out .= ')';
                else if ($json[$i] == ':')
                    $out .= '=>';
                else
                    $out .= $json[$i];
            } else
                $out .= $json[$i];
            if ($json[$i] == '"')
                $comment = !$comment;
        }
        eval($out . ';');
        return $x;
    }

}

//http://stackoverflow.com/questions/708017/can-a-php-file-name-or-a-dir-in-its-full-path-have-utf-8-characters
function my_rename($src, $dst) {
    system("myren.exe \"$src\" \"$dst\"");
}

function get_path_without_ext($path) {
    $pos = strrpos($path, ".");
    return $pos > 0 ? substr($path, 0, $pos) : $path;
}

?>