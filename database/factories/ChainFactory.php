<?php

namespace Database\Factories;

use App\Models\Chain;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChainFactory extends Factory
{
    protected $model = Chain::class;

    public function definition()
    {
        return [
            'chain' => $this->faker->unique()->slug(2),
            'target_url' => $this->faker->url(),
            'chain_title' => $this->faker->sentence(3),
            'domain' => 1,
            'status' => 'active',
            'create_time' => now(),
            'pv_history' => 0,
            'pv_today' => 0,
            'domain_url' => $this->faker->domainName(),
            'domain_status' => 1,
            'type' => 1,
            'sub_type' => 0,
            'render_url' => $this->faker->url(),
            'group_id' => null,
        ];
    }

    public function withTargetUrl(string $url)
    {
        return $this->state(function (array $attributes) use ($url) {
            return [
                'target_url' => $url,
            ];
        });
    }
} 