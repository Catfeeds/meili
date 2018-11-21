<?php
namespace Dock;

class Response
{
    public static function handle($content)
    {
        $result = json_decode($content, 1);
        if ($result['Result'] == false) {
            throw new \Exception($result['Message']);
        }
        return true;
    }
}