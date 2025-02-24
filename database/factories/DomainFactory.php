<?php

namespace Database\Factories;

use App\Models\Domain;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class DomainFactory extends Factory
{
    protected $model = Domain::class;

    public function definition(): array
    {
        return [
            'domain' => $this->faker->domainName(),
            'status' => 'active',
            'group_id' => Group::factory(),
        ];
    }

    public function withDomain(string $domain): DomainFactory
    {
        return $this->state([
            'domain' => $domain
        ]);
    }
}
