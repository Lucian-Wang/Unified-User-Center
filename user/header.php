<?php
if (session_status() == PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>灯火通明-统一用户中心</title>
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="https://github.githubassets.com/assets/github-ef14fd9f242b.css">
    <style>
        body {
            background: #fff;
            color: #24292f;
            font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji";
        }
        a,
        a:visited,
        a:active,
        a:focus {
            color: #0969da !important;
            text-decoration: none;
            transition: color .15s;
        }
a:hover {
    color: #218bff !important;
    text-decoration: underline;
}
        .header-logo {
            display: flex;
            justify-content: center;
            margin-top: 48px;
        }
        .header-logo svg {
            width: 48px;
            height: 48px;
            fill: #24292f;
        }
        .main-content {
            max-width: 340px;
            margin: 0 auto;
            margin-top: 8px;
            background: none;
        }
        @media (max-width:500px){
            .main-content {
                max-width: 99vw;
                padding-left: 2vw;
                padding-right: 2vw;
            }
        }
    </style>
</head>
<body>
<div class="header-logo">
    <img src="https://user.wsx.tax/logo.svg" alt="LOGO" width="60" height="60" style="display:block;">
</div>
<div class="main-content">