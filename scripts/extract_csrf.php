<?php
$s = file_get_contents(__DIR__ . '/../login.html');
if (preg_match('/name="_token" value="([^"]+)"/', $s, $m)) {
    echo $m[1];
}
