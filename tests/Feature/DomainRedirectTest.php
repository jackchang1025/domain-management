<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Domain;
use App\Services\DomainRedirectService;
use Illuminate\Support\Facades\Redis;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DomainRedirectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Redis::del(DomainRedirectService::QUEUE_KEY);
    }

    public function test_redirect_to_valid_domain()
    {
        Domain::factory()->create(['domain' => 'example.com', 'status' => 'active']);
        
        $service = new DomainRedirectService();
        $domain = $service->getRedirectDomain();
        
        $this->assertEquals('example.com', $domain);
    }

    public function test_queue_refill_when_empty()
    {
        Redis::del(DomainRedirectService::QUEUE_KEY);
        
        Domain::factory(3)->create(['status' => 'active']);
        
        $service = new DomainRedirectService();
        $domain = $service->getRedirectDomain();
        
        $this->assertNotNull($domain);
        $this->assertEquals(2, Redis::llen(DomainRedirectService::QUEUE_KEY));
    }

    public function test_domain_order_by_group()
    {
        $group1 = Group::factory()->create();
        $group2 = Group::factory()->create();
        
        Domain::factory()->create(['group_id' => $group2->id, 'domain' => 'domain2.com']);
        Domain::factory()->create(['group_id' => $group1->id, 'domain' => 'domain1.com']);
        
        $service = new DomainRedirectService();
        $service->refillQueue();
        
        $queue = Redis::lrange(DomainRedirectService::QUEUE_KEY, 0, -1);
        $this->assertEquals(['domain1.com', 'domain2.com'], $queue);
    }

    public function test_throw_exception_when_no_domains()
    {
        Redis::del(DomainRedirectService::QUEUE_KEY);
        Domain::query()->delete();
        
        $this->expectException(\RuntimeException::class);
        
        $service = new DomainRedirectService();
        $service->getRedirectDomain();
    }

    public function test_only_active_domains_in_queue()
    {
        Domain::factory()->create(['status' => 'active', 'domain' => 'active.com']);
        Domain::factory()->create(['status' => 'expired', 'domain' => 'expired.com']);
        
        $service = new DomainRedirectService();
        $service->refillQueue();
        
        $queue = Redis::lrange(DomainRedirectService::QUEUE_KEY, 0, -1);
        $this->assertContains('active.com', $queue);
        $this->assertNotContains('expired.com', $queue);
    }

    public function test_gets_domain_from_queue()
    {
        Domain::factory()->create(['domain' => 'example.com', 'status' => 'active']);
        $service = new DomainRedirectService();
        $service->refillQueue();

        $result = $service->getRedirectDomain();
        $this->assertEquals('example.com', $result);
    }

    public function test_refills_queue_when_empty()
    {
        Domain::factory()->count(3)->create(['status' => 'active']);
        $service = new DomainRedirectService();

        // 首次调用会填充队列
        $service->getRedirectDomain();
        $queueLength = Redis::llen(DomainRedirectService::QUEUE_KEY);
        $this->assertEquals(2, $queueLength);
    }

    public function test_orders_domains_by_group_then_id()
    {
        $group1 = Group::factory()->create();
        $group2 = Group::factory()->create();

        Domain::factory()->create([
            'group_id' => $group2->id,
            'domain' => 'domain2.com',
            'id' => 2
        ]);
        Domain::factory()->create([
            'group_id' => $group1->id,
            'domain' => 'domain1.com',
            'id' => 1
        ]);

        $service = new DomainRedirectService();
        $service->refillQueue();

        $expected = ['domain1.com', 'domain2.com'];
        $actual = Redis::lrange(DomainRedirectService::QUEUE_KEY, 0, -1);
        $this->assertEquals($expected, $actual);
    }

    public function test_throws_exception_when_no_domains()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('没有活跃的域名可用');

        $service = new DomainRedirectService();
        $service->refillQueue();
    }

    public function test_filters_inactive_domains()
    {
        Domain::factory()->create(['status' => 'active', 'domain' => 'good.com']);
        Domain::factory()->create(['status' => 'expired', 'domain' => 'bad.com']);

        $service = new DomainRedirectService();
        $service->refillQueue();

        $domains = Redis::lrange(DomainRedirectService::QUEUE_KEY, 0, -1);
        $this->assertContains('good.com', $domains);
        $this->assertNotContains('bad.com', $domains);
    }
} 