<?php

namespace App\Http\Controllers;

use App\Services\DomainRedirectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RedirectController extends Controller
{
    public function __construct(
        private DomainRedirectService $redirectService
    ) {}

    public function redirectDomain(): RedirectResponse
    {
        try {

            $domain = $this->redirectService->getRedirectDomain();

            //构建日志
            Log::info("Redirecting to {$domain}");
            
            $prefix = Str::random(6);
            return redirect()->away("http://{$prefix}.{$domain}");
        } catch (\RuntimeException $e) {
            abort(404, $e->getMessage());
        }
    }
}
