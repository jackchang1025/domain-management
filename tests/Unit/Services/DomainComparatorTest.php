<?php

namespace Tests\Unit\Services;

use App\Services\DomainComparator;
use Tests\TestCase;

class DomainComparatorTest extends TestCase
{
    private DomainComparator $comparator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->comparator = new DomainComparator();
    }

    public function test_basic_equality()
    {
        $this->assertTrue(DomainComparator::equals('example.com', 'example.com'));
        $this->assertFalse(DomainComparator::equals('www.example.com', 'example.com'));
    }

    public function test_protocol_insensitive()
    {
        $this->assertTrue($this->comparator->equals('http://example.com', 'https://example.com'));
        $this->assertTrue($this->comparator->equals('//example.com', 'example.com'));
    }

    public function test_case_insensitive()
    {
        $this->assertTrue($this->comparator->equals('EXAMPLE.COM', 'example.com'));
        $this->assertTrue($this->comparator->equals('WwW.ExAmPlE.CoM', 'www.example.com'));
    }

    public function test_port_handling()
    {
        $this->assertTrue($this->comparator->equals('example.com:8080', 'example.com'));
        $this->assertTrue($this->comparator->equals('https://example.com:443', 'example.com'));
    }

    public function test_path_ignored()
    {
        $this->assertTrue($this->comparator->equals('example.com/path', 'example.com'));
        $this->assertTrue($this->comparator->equals('example.com?query=1', 'example.com'));
    }

    public function test_www_subdomain_handling()
    {
        $this->assertFalse($this->comparator->equals('www.example.com', 'example.com'));
        $this->assertTrue($this->comparator->equals('www.example.com', 'www.example.com'));
    }

    public function test_invalid_domains()
    {
        $this->assertFalse($this->comparator->equals('not a domain', 'example.com'));
        $this->assertFalse($this->comparator->equals('', 'example.com'));
        $this->assertTrue($this->comparator->equals('', ''));
    }

    public function test_subdomain_sensitivity()
    {
        $this->assertFalse($this->comparator->equals('blog.example.com', 'example.com'));
        $this->assertTrue($this->comparator->equals('a.b.c.example.com', 'a.b.c.example.com'));
    }

    public function test_special_characters()
    {
        $this->assertTrue(DomainComparator::equals('ex-ample.com', 'ex-ample.com'));
        $this->assertFalse(DomainComparator::equals('ex-ample.com', 'example.com'));
    }

    public function test_ensure_protocol()
    {
        // 测试无协议的情况
        $this->assertEquals(
            'http://example.com',
            DomainComparator::ensureProtocol('example.com')
        );

        // 测试已有http协议
        $this->assertEquals(
            'http://example.com',
            DomainComparator::ensureProtocol('http://example.com')
        );

        // 测试已有https协议
        $this->assertEquals(
            'https://example.com',
            DomainComparator::ensureProtocol('https://example.com')
        );

        // 测试带路径的情况
        $this->assertEquals(
            'http://example.com/path?query=1',
            DomainComparator::ensureProtocol('example.com/path?query=1')
        );

        // 测试自定义协议
        $this->assertEquals(
            'ftp://example.com',
            DomainComparator::ensureProtocol('ftp://example.com')
        );

        // 测试空值
        $this->assertEquals('', DomainComparator::ensureProtocol(''));
    }
}
