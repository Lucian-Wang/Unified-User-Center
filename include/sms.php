<?php
require_once __DIR__ . '/config.php';

/**
 * 发送短信验证码（云片）
 * @param string $mobile 手机号
 * @param string $code 验证码数字
 * @return bool true=发送成功，false=失败
 */
function send_sms($mobile, $code) {
    $apikey = SMS_APIKEY;
    $sign = SMS_SIGN;
    if (!$apikey || !$sign) return false;

    $text = "【{$sign}】您的验证码是{$code}，5分钟内有效。";
    $post_data = [
        'apikey' => $apikey,
        'mobile' => $mobile,
        'text' => $text
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://sms.yunpian.com/v2/sms/single_send.json");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    $output = curl_exec($ch);
    $error = curl_errno($ch) ? curl_error($ch) : null;
    curl_close($ch);

    if($error) return false;

    $res = @json_decode($output, true);
    // 云片返回 code==0 表示成功
    return ($res && isset($res['code']) && $res['code'] == 0);
}
?>