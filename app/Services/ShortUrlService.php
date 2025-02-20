<?php

namespace App\Services;

use App\Services\Integrations\ShortUrl\ShortUrlConnector;
use GuzzleHttp\Cookie\FileCookieJar;
use Psr\Log\LoggerInterface;
use Saloon\Http\Response;
use App\Services\Integrations\ShortUrl\Data\ShortUrlList;
use App\Services\Integrations\ShortUrl\Data\ShortUrl as ShortUrlData;
use App\Models\ShortUrl;
use Illuminate\Support\Facades\DB;
use App\Services\Integrations\ShortUrl\Request\ListShortUrlRequest;

class ShortUrlService
{
    private const LOGIN_LINK_SELECTOR = 'a[href*="/User/login"]';

    protected ShortUrlConnector $connector;
    protected bool $isLogin = false;

    public function __construct(
        protected string $account,
        protected string $password,
        protected string $cookieFile,
    ) {
        $this->initializeConnector();
    }

    private function initializeConnector(): void
    {
        $this->connector = new ShortUrlConnector();
        $this->connector->withCookies(new FileCookieJar($this->cookieFile, true));
        $this->connector->withLogger(app(LoggerInterface::class));

        if (!$this->isLogin()) {
            $this->login();
            $this->isLogin = true;
        }
    }

    public function isLogin(): bool
    {
        if ($this->isLogin) {
            return true;
        }

        $response = $this->home();
        $crawler = $response->dom();

        $this->isLogin = $crawler->filter(self::LOGIN_LINK_SELECTOR)->count() === 0;
        return $this->isLogin;
    }

    public function home(): Response
    {
        return $this->connector->getResource()->home();
    }

    public function login(): Response
    {
        return $this->connector->getResource()->login($this->account, $this->password);
    }

    /**
     * @param string $code
     * @param string $url
     * @return Response
     * @throws \JsonException
     */
    public function updateShortUrl(string $code, string $url): Response
    {
        $response =  $this->connector->getResource()->updateShortUrl($code, $url);

        if ($response->successful() && $response->json('status') !== 1) {
            throw new \Exception("更新失败: {$response->json('msg')}");
        }

        return $response;
    }

    public function deleteShortUrl(string $code): Response
    {
        return $this->connector->getResource()->deleteShortUrl($code);
    }

    public function createShortUrl(string $url): Response
    {
        return $this->connector->getResource()->createShortUrl($url);
    }

    public function getTotalPage(): ShortUrlList
    {
        $paginator = $this->connector->paginate(
            new ListShortUrlRequest()
        );

        $data = $paginator->collect()->all();

        return ShortUrlList::from(['shortUrls' => $data]);
    }


    /**
     * @return void
     */
    public function syncDataFromApi(): void
    {
        // 在事务外获取数据
        $apiData = $this->getTotalPage()->shortUrls->toCollection();

        // 仅数据库操作在事务中
        DB::transaction(function () use ($apiData) {
            // 批量更新或创建
            $apiData->each(function (ShortUrlData $item) {
                ShortUrl::updateOrCreate(
                    ['code' => $item->code],
                    [
                        'short_url' => $item->short_url,
                        'long_url' => $item->long_url,
                        'visit_count' => $item->visit_count,
                        'edit_link' => $item->edit_link,
                    ]
                );
            });

            // 删除本地多余数据
            $apiCodes = $apiData->pluck('code');
            ShortUrl::whereNotIn('code', $apiCodes)->delete();
        });
    }

    public function getLatestShortUrl(): ?ShortUrl
    {
        return ShortUrl::latest('id')->first();
    }

}
