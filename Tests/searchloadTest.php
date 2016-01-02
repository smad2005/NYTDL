<?php

require_once '../searchload.php';
define('TEST_DIR', 'Tests/examples');

class searchloadTest extends PHPUnit_Framework_TestCase {

    private $jsonConfigPath = "Tests/config.json";

    static function setUpBeforeClass() {
        chdir("..");
    }

    function createFiles($list) {
        if (!file_exists(TEST_DIR)) {
            mkdir(TEST_DIR);
        }
        foreach ($list as $name) {
            file_put_contents(TEST_DIR . '/' . $name, '');
        }
    }

    function deleteTestFolder() {
        self::deleteDir(TEST_DIR);
    }

    function testInitConfig() {

        initConfig($this->jsonConfigPath);
        $this->assertThat(TORCLI, $this->logicalNot($this->equalTo('')));
    }

    function testInitSubtitlesList() {
        $this->createFiles(array('3.ass', '4.srt', '5.ass', '5.mkv'));
        $subtitlesList = initSubtitlesList('');
        $this->assertEquals($subtitlesList, false);
        $subtitlesList = initSubtitlesList(TEST_DIR);
        $this->assertNotEmpty($subtitlesList);
        $this->assertEquals(2, count($subtitlesList));
        $this->deleteTestFolder();
    }

    function testHandleSubtitleFile() {
        $this->deleteTestFolder();
        //$this->testInitConfig();
        $this->createFiles(array(base64_decode('W0hvcnJpYmxlU3Vic10gRmF0ZSBLYWxlaWQgTGluZXIgUFJJU01BIElMWUEgMndlaSBIZXJ6ISAtIDA2IFs3MjAuYXNz'), '4.srt', '5.ass'));
        $badsubPath = TEST_DIR . '/5.ass';
        file_put_contents($badsubPath, 'Video File: ' . base64_decode('W09oeXMtUmF3c10gUHJpc29uIFNjaG9vbCAtIDA5IChNWCAxMjgweDcyMCB4MjY0IEFBQykubXA0'));
        $subtitlesList = initSubtitlesList(TEST_DIR);
        $dirInfo = array("dirnameRaw" => TEST_DIR, "dirname" => escapeshellarg(TEST_DIR));
        $context = stream_context_create(array('http' => array('timeout' => 20000, 'user_agent' => USERAGENT)));
        $torrents = array();
        foreach ($subtitlesList as $sub)
            handleSubtitleFile($dirInfo, $sub, $context);
        $this->assertFileNotExists($badsubPath);
        $this->deleteTestFolder();
    }

    function testHandleSubtitleFile_wrongVideoFile_fixedName() {
        $this->deleteTestFolder();
        //$this->testInitConfig();
        $this->createFiles(array('5.ass'));
        $badsubPath = TEST_DIR . '/5.ass';
        file_put_contents($badsubPath, 'Video File: ' . base64_decode('W09oeXMtUmF3c10gVmFsa3lyaWUgRHJpdmUgTWVybWFpZCAtIDEx'));
        $subtitlesList = initSubtitlesList(TEST_DIR);
        $dirInfo = array("dirnameRaw" => TEST_DIR, "dirname" => escapeshellarg(TEST_DIR));
        $context = stream_context_create(array('http' => array('timeout' => 20000, 'user_agent' => USERAGENT)));
        $torrents = array();
        foreach ($subtitlesList as $sub) {
            handleSubtitleFile($dirInfo, $sub, $context);
        }

        $this->assertFileExists(TEST_DIR . '/'.base64_decode('W09oeXMtUmF3c10gVmFsa3lyaWUgRHJpdmUgTWVybWFpZCAtIDExIChBVC1YIDEyODB4NzIwIHgyNjQgQUFDKS5hc3M='));
        $this->deleteTestFolder();
    }

    static function deleteDir($dirPath) {
        if (!is_dir($dirPath)) {
            return;
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    function testChr_utf8() {
        for ($i = 0; $i < 10000; $i++) {
            if (chr_utf8($i) === false) {
                $this->assertNotEquals(false, chr_utf8($i));
            }
        }
        $this->assertNotEquals(false, chr_utf8(128521));
        $this->assertEquals(false, chr_utf8(-10));
    }

    function testHtmlentities2utf8() {
        echo htmlentities2utf8("&nbsp;");
        $this->assertEquals(' ', htmlentities2utf8("&nbsp;"));
        $this->assertEquals('¡', htmlentities2utf8("&iexcl;"));
        $this->assertEquals('¢', htmlentities2utf8("&cent;"));
        $this->assertEquals('£', htmlentities2utf8("&pound;"));
        $this->assertEquals('¤', htmlentities2utf8("&curren;"));
        $this->assertEquals('¥', htmlentities2utf8("&yen;"));
        $this->assertEquals(false, htmlentities2utf8("&uknown;"));
    }

    function test_sys_get_temp_dir_PHP4() {
        $tmp = getenv('TMP');
        $tmpdir = getenv('TMPDIR');
        $temp = getenv('TEMP');
        putenv("TMP");
        putenv("TMPDIR");
        putenv("TEMP");
        $this->assertNotEmpty(sys_get_temp_dirPHP4());
        putenv("TEMP=tests");
        $result = sys_get_temp_dirPHP4();
        $this->assertNotEmpty($result);
        putenv("TMPDIR=tests");
        $this->assertNotEmpty(sys_get_temp_dirPHP4());
        putenv("TMP=tests");
        $this->assertNotEmpty(sys_get_temp_dirPHP4());
        putenv("TMP=$tmp");
        putenv("TMPDIR=$tmpdir");
        putenv("TEMP=$temp");
        $this->assertNotEmpty(sys_get_temp_dirPHP4());
    }

    function test_json_decode_PHP4() {
        $json_config = file_get_contents($this->jsonConfigPath);
        $json = json_decodePHP4($json_config);
        $this->assertNotEmpty($json['TorrentPath']);
    }

    function testGetRealPath() {
        putenv("testpath=testpath2");
        $path = "%testpath%/some";
        $this->assertEquals('testpath2/some', getPathWithEnv($path));
    }

}

?>