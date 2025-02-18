<?php

namespace App\Services;

use App\Services\Integrations\Forage\Data\ActiveList;
use App\Services\Integrations\Forage\ForageConnector;
use GuzzleHttp\Cookie\FileCookieJar;
use Saloon\Http\Response;
use Psr\Log\LoggerInterface;
use App\Services\Integrations\Forage\Data\SaveActive;

class ForageService
{
    protected ForageConnector $connector;

    protected ?bool $isLogin = null;

    public function __construct(protected string $account, protected string $password, protected string $cookieFile)
    {
        $this->connector = new ForageConnector();
        $this->connector->withCookies(new FileCookieJar($cookieFile,true));
        $this->connector->withLogger(app(LoggerInterface::class));

        // $this->connector->debug();
    }

    protected function zeroPadding(string $data, int $blocksize = 16): string
    {
        $pad = $blocksize - (strlen($data) % $blocksize);
        return $data . str_repeat("\0", $pad);
    }

    protected function encryptPassword(string $password, string $sessionId): string
    {
        // 获取 sessionId 最后16位作为密钥
        $key = substr($sessionId, -16);

        // 使用相同的值作为 IV
        $iv = $key;

        // 进行零填充
        $paddedPassword = $this->zeroPadding($password);

        // 使用 AES-128-CBC 加密
        $encrypted = openssl_encrypt(
            $paddedPassword,
            'AES-128-CBC',
            $key,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv
        );

        // Base64 编码
        return base64_encode($encrypted);
    }


    /**
     * @return ActiveList
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function domainList(): ActiveList
    {
        return $this->connector->getDomainResource()->list()->dto();
    }

    /**
     * @param SaveActive $saveActive
     * @return Response
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function saveActive(SaveActive $saveActive): Response
    {
        return $this->connector->getDomainResource()->saveActive($saveActive);
    }

    /**
     * @return Response
     * @throws \JsonException
     * @throws \Exception
     */
    public function login(): Response
    {
         /** @var \GuzzleHttp\Cookie\CookieJar $cookieJar */
         $cookieJar = $this->connector->getCookieJar();

         //清空cookie
         $cookieJar->clear();

         // 获取登录cookie
        $response = $this->connector->getLoginResource()->getCvid();


         /** @var \GuzzleHttp\Cookie\SetCookie $cookie */
         $cookie = $cookieJar->getCookieByName('PHPSESSID');
         if(!$cookie){
            throw new \RuntimeException('get cvid cookie failed!');
         }

        // 加密密码
        $encryptedPassword = $this->encryptPassword($this->password, $cookie->getValue());

        //登录
        $response = $this->connector->getLoginResource()->entry($this->account, urlencode($encryptedPassword));

        if($response->json('status') !== 1){
            throw new \RuntimeException($response->json('msg'));
        }

        $userId = $response->json('data.minfo.data.user_id');
        if(!$userId){
            throw new \RuntimeException('get user id failed!');
        }

        //获取token
        $response = $this->connector->getLoginResource()->getToken($userId);
        if($response->json('status') !== 1){
            throw new \RuntimeException($response->json('msg'));
        }

        return $response;

    }

    public function getForage(): ForageConnector
    {
        return $this->connector;
    }
}
