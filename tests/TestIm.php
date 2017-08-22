<?php

class TestIm extends PHPUnit_Framework_TestCase {
    public function testImResize() {
        $tests = array(
            "100x200 -> 50x50 = 25x50",
            "100x200 -> 50x50^ = 50x100",
        );
        foreach ($tests as $test) {
            preg_match("~(?P<sw>\d+)x(?P<sh>\d+) -> (?P<size>\S+) = (?P<dw>\d+)x(?P<dh>\d+)~", $test, $m);
            $im = imagecreate($m['sw'], $m['sh']);
            $backgroundColor = imagecolorallocate($im, 0, 0, 0);
            $tmp = rtrim(`mktemp`);
            imagepng($im, $tmp);
            $target = im_resize($tmp, [
                'size' => $m['size'],
                'dir' => sys_get_temp_dir(),
                'log' => true,
                'overwrite' => true,
            ]);
            $this->assertTrue(is_file($target));
            list($w, $h) = getimagesize($target);
            $this->assertEquals($w, $m['dw']);
            $this->assertEquals($h, $m['dh']);
        }
    }
    public function testImCrop() {
        $tests = array(
            "100x200 -> 50x50+0+0 = 50x50"
        );
        foreach ($tests as $test) {
            preg_match("~(?P<sw>\d+)x(?P<sh>\d+) -> (?P<size>\S+) = (?P<dw>\d+)x(?P<dh>\d+)~", $test, $m);
            $im = imagecreate($m['sw'], $m['sh']);
            $backgroundColor = imagecolorallocate($im, 0, 0, 0);
            $tmp = rtrim(`mktemp`);
            imagepng($im, $tmp);
            $target = im_crop($tmp, [
                'size' => $m['size'],
                'dir' => sys_get_temp_dir(),
                'log' => true,
                'overwrite' => true,
            ]);
            $this->assertTrue(is_file($target));
            list($w, $h) = getimagesize($target);
            $this->assertEquals($w, $m['dw']);
            $this->assertEquals($h, $m['dh']);
        }
    }
    public function testImTransparent() {
        $tests = array(
            "100x200 -> white = 100x200"
        );
        foreach ($tests as $test) {
            preg_match("~(?P<sw>\d+)x(?P<sh>\d+) -> (?P<color>\S+) = (?P<dw>\d+)x(?P<dh>\d+)~", $test, $m);
            $im = imagecreate($m['sw'], $m['sh']);
            $backgroundColor = imagecolorallocate($im, 0, 0, 0);
            $tmp = rtrim(`mktemp`);
            imagepng($im, $tmp);
            $target = im_transparent($tmp, [
                'transparent' => $m['color'],
                'dir' => sys_get_temp_dir(),
                'log' => true,
                'overwrite' => true,
            ]);
            $this->assertTrue(is_file($target));
            list($w, $h) = getimagesize($target);
            $this->assertEquals($w, $m['dw']);
            $this->assertEquals($h, $m['dh']);
        }
    }
    public function testImBorder() {
        $tests = array(
            "100x200 -> 1 = 102x202",
            "100x200 -> 2 = 104x204"
        );
        foreach ($tests as $test) {
            preg_match("~(?P<sw>\d+)x(?P<sh>\d+) -> (?P<border>\S+) = (?P<dw>\d+)x(?P<dh>\d+)~", $test, $m);
            $im = imagecreate($m['sw'], $m['sh']);
            $backgroundColor = imagecolorallocate($im, 0, 0, 0);
            $tmp = rtrim(`mktemp`);
            imagepng($im, $tmp);
            $target = im_border($tmp, [
                'border' => $m['border'],
                'dir' => sys_get_temp_dir(),
                'log' => true,
                'overwrite' => true,
            ]);
            $this->assertTrue(is_file($target));
            list($w, $h) = getimagesize($target);
            $this->assertEquals($w, $m['dw']);
            $this->assertEquals($h, $m['dh']);
        }
    }
    public function testImCaptcha() {
        if (!function_exists('imagettfbbox'))
            $this->markTestSkipped('imagettfbbox() is not a function!');
        $result = im_captcha();
        $this->assertTrue(!!$result);
        $this->assertTrue(strlen($result['word']) > 0);
        $this->assertTrue(is_file($result['file']));
    }
}
