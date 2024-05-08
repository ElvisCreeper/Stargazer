<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: application/json; charset=UTF-8');

function createResponse($status, $message, $data = null)
{
    http_response_code($status);
    header("Status: $message");
    if ($data) {
        return json_encode($data);
    }
}
