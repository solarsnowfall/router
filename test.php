<?php

header('Content-Type: text/plain');

$ch = curl_init('http://localhost/solar-router/farts/and/turds/?and=farts');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

var_dump(curl_exec($ch));