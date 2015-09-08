<?php

require_once '../searchload.php';
define('TEST_DIR', 'Tests/examples');

class searchloadTest extends PHPUnit_Framework_TestCase {

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

    function deleteTetsFolder() {
        self::deleteDir(TEST_DIR);
    }

    function testInitConfig() {
        $jsonConfigPath = 'Tests/config.json';
        initConfig($jsonConfigPath);
        $this->assertThat(TORCLI, $this->logicalNot($this->equalTo('')));
    }

    function testInitSubtitlesList() {
        $this->createFiles(array('3.ass', '4.srt', '5.ass', '5.mkv'));
        $subtitlesList = initSubtitlesList('');
        $this->assertEquals($subtitlesList, false);
        $subtitlesList = initSubtitlesList(TEST_DIR);
        $this->assertNotEmpty($subtitlesList);
        $this->assertEquals(2, count($subtitlesList));
        $this->deleteTetsFolder();
    }

    function testHandleSubtitleFile() {
        $this->deleteTetsFolder();
        //$this->testInitConfig();
        $this->createFiles(array(base64_decode('W0hvcnJpYmxlU3Vic10gRmF0ZSBLYWxlaWQgTGluZXIgUFJJU01BIElMWUEgMndlaSBIZXJ6ISAtIDA2IFs3MjAuYXNz'), '4.srt', '5.ass'));
        $badsubPath = TEST_DIR . '/5.ass';
        file_put_contents($badsubPath, 'Video File: '. base64_decode('W09oeXMtUmF3c10gUHJpc29uIFNjaG9vbCAtIDA5IChNWCAxMjgweDcyMCB4MjY0IEFBQykubXA0'));
        $subtitlesList = initSubtitlesList(TEST_DIR);
        $dirInfo = array("dirnameRaw" => TEST_DIR, "dirname" => escapeshellarg(TEST_DIR));
        $context = stream_context_create(array('http' => array('timeout' => 20000, 'user_agent' => USERAGENT)));
        $torrents = array();
        foreach ($subtitlesList as $sub)
            handleSubtitleFile($dirInfo, $sub, $context);
        $this->assertFileNotExists($badsubPath);
        $this->deleteTetsFolder();
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

}

?>