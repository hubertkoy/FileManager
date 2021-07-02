<?php

/**
 * @throws BadRequestJsonException
 */
function prepare_post_data(string $expected_mime_type): void {
    $all_headers = getallheaders();
    $content_type = $all_headers['Content-Type'];
    if ($content_type != $expected_mime_type) {
        throw new BadRequestJsonException(['message'=>'Invalid format request data.']);
    }
}

function uuid_v4()
{
    $data=openssl_random_pseudo_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}