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

function chr_utf8($code)
    {
        if ($code < 0) return false;
        elseif ($code < 128) return chr($code);
        elseif ($code < 160) // Remove Windows Illegals Cars
        {
            if ($code==128) $code=8364;
            elseif ($code==129) $code=160; // not affected
            elseif ($code==130) $code=8218;
            elseif ($code==131) $code=402;
            elseif ($code==132) $code=8222;
            elseif ($code==133) $code=8230;
            elseif ($code==134) $code=8224;
            elseif ($code==135) $code=8225;
            elseif ($code==136) $code=710;
            elseif ($code==137) $code=8240;
            elseif ($code==138) $code=352;
            elseif ($code==139) $code=8249;
            elseif ($code==140) $code=338;
            elseif ($code==141) $code=160; // not affected
            elseif ($code==142) $code=381;
            elseif ($code==143) $code=160; // not affected
            elseif ($code==144) $code=160; // not affected
            elseif ($code==145) $code=8216;
            elseif ($code==146) $code=8217;
            elseif ($code==147) $code=8220;
            elseif ($code==148) $code=8221;
            elseif ($code==149) $code=8226;
            elseif ($code==150) $code=8211;
            elseif ($code==151) $code=8212;
            elseif ($code==152) $code=732;
            elseif ($code==153) $code=8482;
            elseif ($code==154) $code=353;
            elseif ($code==155) $code=8250;
            elseif ($code==156) $code=339;
            elseif ($code==157) $code=160; // not affected
            elseif ($code==158) $code=382;
            elseif ($code==159) $code=376;
        }
        if ($code < 2048) return chr(192 | ($code >> 6)) . chr(128 | ($code & 63));
        elseif ($code < 65536) return chr(224 | ($code >> 12)) . chr(128 | (($code >> 6) & 63)) . chr(128 | ($code & 63));
        else return chr(240 | ($code >> 18)) . chr(128 | (($code >> 12) & 63)) . chr(128 | (($code >> 6) & 63)) . chr(128 | ($code & 63));
    }

    // Callback for preg_replace_callback('~&(#(x?))?([^;]+);~', 'html_entity_replace', $str);
    function html_entity_replace($matches)
    {
        if ($matches[2])
        {
            return chr_utf8(hexdec($matches[3]));
        } elseif ($matches[1])
        {
            return chr_utf8($matches[3]);
        }
        switch ($matches[3])
        {
            case "nbsp": return chr_utf8(160);
            case "iexcl": return chr_utf8(161);
            case "cent": return chr_utf8(162);
            case "pound": return chr_utf8(163);
            case "curren": return chr_utf8(164);
            case "yen": return chr_utf8(165);
         }
        return false;
    }
    
    //source http://php.net/manual/en/function.html-entity-decode.php
    function htmlentities2utf8 ($string) 
    {
        $string = preg_replace_callback('~&(#(x?))?([^;]+);~', 'html_entity_replace', $string);
        return $string;
    } 
?>