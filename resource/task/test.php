<?php

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL            => "http://10.0.4.27/campus/mainsite/login/index.php",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 20,
    CURLOPT_HEADER  => 0,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0',
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_VERBOSE => true
]);
echo "http://10.0.4.27/campus/mainsite/login/index.php";
echo "<pre>";
curl_exec($ch);
echo "<p>";
print_r(curl_errno($ch));
echo "</p>";

print_r(curl_getinfo($ch));
curl_close($ch);
