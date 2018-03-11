<?php

include 'functions.php';

$ignore = ['xpath_callback_remove', 'xpath_callback_unwrap'];

$output = [];
$content = file_get_contents('functions.php');
preg_match_all('~^function\s+(.*?)\(~m', $content, $matches);
$matches = $matches[1];

$list = [];
foreach ($matches as $function) {
    if (in_array($function, $ignore)) continue;
    $r = new ReflectionFunction($function);
    $block = $r->getDocComment();
    if (!$block) continue;
    $block = str_replace('*\\/', '*/', $block);
    $doc = $block;
    $doc = preg_replace('~^\s*/\*\*\s*~s', '', $doc);
    $doc = preg_replace('~\s*\*/\s*$~s', '', $doc);
    $doc = preg_replace('~^\s*\* ?~m', '', $doc);
    preg_match_all('~^@param\s+(\S+)\s+(\S+)(.*)~m', $doc, $params, PREG_SET_ORDER);
    preg_match('~^@return\s+(\S+)~m', $doc, $return);
    $signature = $return[1] . ' ' . $function . '(' . implode(', ', array_map(function ($match) use ($content, $function) {
        $append = '';
        if (trim($match[3])) {
            preg_match(
                '~^function\s+' . $function . '\(.*?' . preg_quote($match[2], '~') . '\s*=\s*(\S+?)(\)|,)~m',
                $content,
                $_
            );
            $append .= ' = ' . $_[1];
        }
        return $match[1] . ' ' . $match[2] . $append;
    }, $params)) . ')';
    $doc = trim(preg_replace('~^@\w+\s+[\s\S]*~m', '', $doc));
    $list[] = "- [{$function}](#{$function})";
    $pos = strpos($content, "function {$function}(");
    $lstart = substr_count(preg_replace("~^function\s+{$function}\([\s\S]*~m", '', $content), "\n") + 1;
    $md5 = md5(mt_rand());
    $_ = preg_replace("~^function\s+{$function}\(~m", $md5, $content);
    $_ = substr($_, strpos($_, $md5));
    $lend = substr_count(preg_replace("~^\}[\s\S]*~m", '', $_), "\n") + $lstart;
    $output[] = (
        "#### {$function}\n\n" .
        '```' . "\n" . $signature . "\n" . '```' . "\n\n" .
        $doc .
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
