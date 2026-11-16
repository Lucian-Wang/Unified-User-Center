<?php
function jwt_encode($payload, $key, $exp_sec = 86400) {
    $header = ['alg'=>'HS256','typ'=>'JWT'];
    $payload['exp'] = time() + $exp_sec;
    $segments = [
        base64url_encode(json_encode($header)),
        base64url_encode(json_encode($payload))
    ];
    $signing_input = implode('.', $segments);
    $signature = base64url_encode(hash_hmac('sha256', $signing_input, $key, true));
    return $signing_input . '.' . $signature;
}
function jwt_decode($jwt, $key) {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) return false;
    list($header64, $payload64, $sig64) = $parts;
    $verify_sig = base64url_encode(hash_hmac('sha256', "$header64.$payload64", $key, true));
    if (!hash_equals($sig64, $verify_sig)) return false;
    $payload = json_decode(base64url_decode($payload64), true);
    if (!$payload || $payload['exp'] < time()) return false;
    return $payload;
}
function base64url_encode($data) { return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); }
function base64url_decode($data) { return base64_decode(strtr($data, '-_', '+/')); }