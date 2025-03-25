<?php

namespace App\Http\Controllers;

use App\Services\DomainRedirectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function __construct(
        private DomainRedirectService $redirectService
    ) {}

    public function redirectDomain(Request $request): RedirectResponse|\Illuminate\Http\Response|\Illuminate\View\View
    {
        // 如果是OPTIONS请求直接返回空响应
        if ($request->isMethod('OPTIONS')) {
            return response()->noContent();
        }

        // 获取用户代理
        $userAgent = $request->header('User-Agent');
        
        // 检测微信环境
        $isWechat = $this->isWechatBrowser($userAgent);
        
        if ($isWechat) {
            return view('boot-page');
        }

        try {
            $domain = $this->redirectService->getRedirectDomain();

            Log::info("Redirecting to {$domain}");

            $prefix = Str::random(6);
            return redirect()->away("http://{$prefix}.{$domain}");
        } catch (\RuntimeException $e) {
            abort(404, $e->getMessage());
        }
    }

    /**
     * 检测是否为微信浏览器
     * 
     * @param string|null $userAgent
     * @return bool
     */
    private function isWechatBrowser(?string $userAgent): bool
    {
        if (empty($userAgent)) {
            return false;
        }

        return strpos(strtolower($userAgent), 'micromessenger') !== false;
    }

    /**
     * 检测是否为安卓微信环境
     * 
     * @param string|null $userAgent
     * @return bool
     */
    private function isAndroidWechat(?string $userAgent): bool
    {
        if (empty($userAgent)) {
            return false;
        }

        $userAgent = strtolower($userAgent);
        return $this->isWechatBrowser($userAgent) && strpos($userAgent, 'android') !== false;
    }

    /**
     * 检测是否为iOS微信环境
     * 
     * @param string|null $userAgent
     * @return bool
     */
    private function isIOSWechat(?string $userAgent): bool
    {
        if (empty($userAgent)) {
            return false;
        }

        $userAgent = strtolower($userAgent);
        return $this->isWechatBrowser($userAgent) && 
               (strpos($userAgent, 'iphone') !== false || 
                strpos($userAgent, 'ipad') !== false || 
                strpos($userAgent, 'ipod') !== false);
    }
}
