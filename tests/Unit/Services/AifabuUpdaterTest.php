<?php

namespace Tests\Unit\Services;

use App\Models\{Domain, Chain};
use App\Services\{AifabuService, AifabuUpdater};
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\LoggerInterface;
use App\Services\Integrations\Aifabu\AifabuConnector;
use App\Services\Integrations\Aifabu\Resource\ChainResource;
use Saloon\Http\Response;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
class AifabuUpdaterTest extends BaseTestCase
{
    use RefreshDatabase;

    protected AifabuUpdater $updater;
    /** @var \Mockery\MockInterface|AifabuService */
    protected $serviceMock;
    /** @var \Mockery\MockInterface|LoggerInterface */
    protected $loggerMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serviceMock = Mockery::mock(AifabuService::class);
        $this->loggerMock = Mockery::mock(LoggerInterface::class);

        $this->updater = new AifabuUpdater(
            $this->serviceMock,
            $this->loggerMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    #[group('prerequisites')]
    public function it_should_throw_exception_when_no_active_domain_exists()
    {
        // 只创建过期域名
        Domain::factory()->create(['status' => 'expired']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('系统缺少正常状态的域名');

        $this->updater->execute();
    }

    #[Test]
    #[group('chain_update')]
    public function it_should_successfully_update_chains_from_expired_to_active_domain(): void
    {
        // 创建活跃域名
        $activeDomain = Domain::factory()
            ->withDomain('https://active.com')
            ->create(['status' => 'active']);

        // 创建过期域名
        $expiredDomain = Domain::factory()
            ->withDomain('https://expired.com')
            ->create(['status' => 'expired']);

        // 创建需要更新的链接
        $chain = Chain::factory()
            ->withTargetUrl('https://expired.com/target')
            ->create([
                'chain' => 'test-chain',
                'chain_title' => 'Test Chain',
                'render_url' => 'https://expired.com/render'
            ]);

        // 模拟API更新响应
        $response = Mockery::mock(Response::class);
        $response->shouldReceive('successful')->andReturn(true);
        $response->shouldReceive('json')->andReturnUsing(function($path = null) {
            $data = [
                'result' => [
                    'render_url' => 'https://active.com/render',
                    'target_url' => 'https://active.com/target'
                ]
            ];
            return $path ? data_get($data, $path) : $data;
        });

        // 模拟服务调用
        $chainResource = Mockery::mock(ChainResource::class);
        $chainResource->shouldReceive('update')
            ->once()
            ->with('test-chain', 'https://active.com', 'Test Chain')
            ->andReturn($response);

        $connector = Mockery::mock(AifabuConnector::class);
        $connector->shouldReceive('getChainResource')
            ->once()
            ->andReturn($chainResource);

        $this->serviceMock->shouldReceive('syncGroupChains')->once();
        $this->serviceMock->shouldReceive('getAifabuConnector')
            ->once()
            ->andReturn($connector);

        // 模拟日志记录
        $this->loggerMock->shouldReceive('info')->twice();
        $this->loggerMock->shouldReceive('error')->never();

        // 执行更新
        $result = $this->updater->execute();

        // 验证结果
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
    }

    #[Test]
    #[group('chain_update')]
    public function it_should_handle_empty_chain_collection()
    {
        // 创建活跃和过期域名
        Domain::factory()->create(['status' => 'active']);
        Domain::factory()->create(['status' => 'expired']);

        $this->serviceMock->shouldReceive('syncGroupChains')->once();
        $this->loggerMock->shouldReceive('info')->once();
        $this->loggerMock->shouldReceive('error')->never();

        // 执行更新
        $result = $this->updater->execute();

        // 验证结果
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
    }

    #[Test]
    #[group('chain_update')]
    public function it_should_handle_multiple_expired_domains()
    {
        // 创建活跃域名
        $activeDomain = Domain::factory()
            ->withDomain('https://active.com')
            ->create(['status' => 'active']);

        // 创建多个过期域名
        $expiredDomain1 = Domain::factory()
            ->withDomain('https://expired1.com')
            ->create(['status' => 'expired']);

        $expiredDomain2 = Domain::factory()
            ->withDomain('https://expired2.com')
            ->create(['status' => 'expired']);

        // 创建需要更新的链接
        $chain1 = Chain::factory()
            ->withTargetUrl('https://expired1.com/target')
            ->create([
                'chain' => 'chain-1',
                'chain_title' => 'Chain 1',
                'render_url' => 'https://expired1.com/render'
            ]);

        $chain2 = Chain::factory()
            ->withTargetUrl('https://expired2.com/target')
            ->create([
                'chain' => 'chain-2',
                'chain_title' => 'Chain 2',
                'render_url' => 'https://expired2.com/render'
            ]);

        // 模拟API更新响应
        $response = Mockery::mock(Response::class);
        $response->shouldReceive('successful')->times(2)->andReturn(true);
        $response->shouldReceive('json')->andReturnUsing(function($path = null) {
            $data = [
                'result' => [
                    'render_url' => 'https://active.com/render',
                    'target_url' => 'https://active.com/target'
                ]
            ];
            return $path ? data_get($data, $path) : $data;
        });

        // 模拟服务调用
        $chainResource = Mockery::mock(ChainResource::class);
        $chainResource->shouldReceive('update')
            ->twice()
            ->andReturn($response);

        $connector = Mockery::mock(AifabuConnector::class);
        $connector->shouldReceive('getChainResource')
            ->twice()
            ->andReturn($chainResource);

        $this->serviceMock->shouldReceive('syncGroupChains')->once();
        $this->serviceMock->shouldReceive('getAifabuConnector')
            ->twice()
            ->andReturn($connector);

        // 模拟日志记录
        $this->loggerMock->shouldReceive('info')->times(3);
        $this->loggerMock->shouldReceive('error')->never();

        // 执行更新
        $result = $this->updater->execute();

        // 验证结果
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    #[group('chain_update')]
    public function it_should_handle_api_exception()
    {
        // 创建活跃域名
        $activeDomain = Domain::factory()
            ->withDomain('https://active.com')
            ->create(['status' => 'active']);

        // 创建过期域名
        $expiredDomain = Domain::factory()
            ->withDomain('https://expired.com')
            ->create(['status' => 'expired']);

        // 创建需要更新的链接
        $chain = Chain::factory()
            ->withTargetUrl('https://expired.com/target')
            ->create([
                'chain' => 'test-chain',
                'chain_title' => 'Test Chain',
                'render_url' => 'https://expired.com/render'
            ]);

        // 模拟服务调用抛出异常
        $chainResource = Mockery::mock(ChainResource::class);
        $chainResource->shouldReceive('update')
            ->once()
            ->andThrow(new \Exception('API Connection Error'));

        $connector = Mockery::mock(AifabuConnector::class);
        $connector->shouldReceive('getChainResource')
            ->once()
            ->andReturn($chainResource);

        $this->serviceMock->shouldReceive('syncGroupChains')->once();
        $this->serviceMock->shouldReceive('getAifabuConnector')
            ->once()
            ->andReturn($connector);

        // 模拟日志记录
        $this->loggerMock->shouldReceive('info')->once();
        $this->loggerMock->shouldReceive('error')->once()->with(
            '更新链接时发生异常',
            Mockery::on(function ($args) {
                return $args['chain'] === 'test-chain' &&
                       $args['error'] === 'API Connection Error';
            })
        );

        // 执行更新
        $result = $this->updater->execute();


        // 验证结果
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
    }

    #[Test]
    #[group('chain_update')]
    public function it_should_handle_api_response_without_required_fields()
    {
        // 创建活跃域名
        $activeDomain = Domain::factory()
            ->withDomain('https://active.com')
            ->create(['status' => 'active']);

        // 创建过期域名
        $expiredDomain = Domain::factory()
            ->withDomain('https://expired.com')
            ->create(['status' => 'expired']);

        // 创建需要更新的链接
        $chain = Chain::factory()
            ->withTargetUrl('https://expired.com/target')
            ->create([
                'chain' => 'test-chain',
                'chain_title' => 'Test Chain',
                'render_url' => 'https://expired.com/render'
            ]);

        // 模拟API返回缺少必要字段的响应
        $response = Mockery::mock(Response::class);
        $response->shouldReceive('successful')->andReturn(true);
        $response->shouldReceive('json')->andReturnUsing(function($path = null) {
            $data = [
                'result' => [
                    // 缺少 render_url 和 target_url
                    'other_field' => 'some value'
                ]
            ];
            return $path ? data_get($data, $path) : $data;
        });
        $response->shouldReceive('status')->andReturn(200);

        // 模拟服务调用
        $chainResource = Mockery::mock(ChainResource::class);
        $chainResource->shouldReceive('update')
            ->once()
            ->with('test-chain', 'https://active.com', 'Test Chain')
            ->andReturn($response);

        $connector = Mockery::mock(AifabuConnector::class);
        $connector->shouldReceive('getChainResource')
            ->once()
            ->andReturn($chainResource);

        $this->serviceMock->shouldReceive('syncGroupChains')->once();
        $this->serviceMock->shouldReceive('getAifabuConnector')
            ->once()
            ->andReturn($connector);

        // 模拟日志记录
        $this->loggerMock->shouldReceive('info')->once();
        $this->loggerMock->shouldReceive('error')->once();

        // 执行更新
        $result = $this->updater->execute();

        // 验证结果
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
    }

    #[Test]
    #[group('chain_update')]
    public function it_should_not_domain_exception()
    {
        // 创建活跃域名
        $activeDomain = Domain::factory()
            ->withDomain('https://active.com')
            ->create(['status' => 'active']);

        // 创建需要更新的链接
        $chain = Chain::factory()
            ->withTargetUrl('https://expired.com/target')
            ->create([
                'chain' => 'test-chain',
                'chain_title' => 'Test Chain',
                'render_url' => 'https://expired.com/render'
            ]);


        $this->serviceMock->shouldReceive('syncGroupChains')->once();


        // 模拟日志记录
        $this->loggerMock->shouldReceive('info')->once();


        // 执行更新
        $result = $this->updater->execute();


        // 验证结果
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
    }
}
