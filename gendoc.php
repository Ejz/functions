<?php

include('functions.php');

$output = array();
$content = file_get_contents('functions.php');
preg_match_all('~^function\s+(.*?)\(~m', $content, $matches);
$matches = $matches[1];

$list = [];
foreach ($matches as $function) {
    $r = new ReflectionFunction($function);
    $block = $r->getDocComment();
    if (!$block) continue;
    $block = str_replace('*\\/', '*/', $block);
    $doc = $block;
    $doc = preg_replace('~^\s*/\*\*\s*~s', '', $doc);
    $doc = preg_replace('~\s*\*/\s*$~s', '', $doc);
    $list[] = "- [{$function}](#{$function})";
    $pos = strpos($content, "function {$function}(");
    $lstart = substr_count(preg_replace("~^function\s+{$function}\([\s\S]*~m", '', $content), "\n") + 1;
    $_ = preg_replace("~[\s\S]*^function\s+{$function}\(~m", '', $content);
    $lend = substr_count(preg_replace("~^\}[\s\S]*~m", '', $_), "\n") + $lstart;
    $output[] = (
        "#### {$function}\n\n" .
        preg_replace('~^\s*\* ?~m', '', $doc) .
        ("\n\n[![to top](totop.png)](#contents) [![view source](viewsource.png)](functions.php#L{$lstart}-L{$lend})")
    );
}

$output = implode("\n\n", $output);
$list = implode("\n", $list);
$readme = file_get_contents($file = 'README.md.tpl');
$readme = preg_replace_callback('~(### Contents).*(### Authors)~s', function ($match) use ($output, $list) {
    return $match[1] . "\n\n" . $list . "\n\n----\n\n" . $output . "\n\n" . $match[2];
}, $readme);
file_put_contents('README.md', $readme);
