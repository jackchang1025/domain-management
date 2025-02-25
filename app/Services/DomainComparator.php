<?php

namespace App\Services;

class DomainComparator
{
    public static function equals(string $domain1, string $domain2): bool
    {
        return self::normalizeDomain($domain1) === self::normalizeDomain($domain2);
    }

    public static function normalizeDomain(string $domain): string
    {
        // 添加空值检查
        if (empty(trim($domain))) {
            return '';
        }
        
        // 修改正则表达式，仅去除协议
        $domain = preg_replace('/^(https?:\/\/)?/i', '', $domain);
        
        // 解析URL组件
        $parsed = parse_url('http://' . $domain);
        
        // 提取主机名
        $host = $parsed['host'] ?? $domain;
        
        // 去除端口
        $host = preg_replace('/:\d+$/', '', $host);
        
        // 转换为小写
        $host = mb_strtolower($host);
        
        return trim($host, '/');
    }

    public static function ensureProtocol(string $url, string $defaultProtocol = 'http://'): string
    {
        // 处理空值
        if (empty(trim($url))) {
            return $url;
        }

        // 检查是否已包含协议
        if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
            // 添加默认协议
            $url = $defaultProtocol . ltrim($url, '/');
        }

        return $url;
    }
} 