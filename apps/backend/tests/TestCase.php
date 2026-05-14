<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected ?User $authUser = null;
    protected ?string $token = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', [
            '--class' => 'CategorySeeder',
        ]);
    }

    /**
     * Create user + token
     */
    protected function actingAsUser(array $attributes = []): User
    {
        $this->authUser = User::factory()->create($attributes);

        $this->token = $this->authUser
            ->createToken('test')
            ->plainTextToken;

        return $this->authUser;
    }

    /**
     * Auth Helper
     */
    protected function auth()
    {
        return $this->withHeader(
            'Authorization',
            "Bearer {$this->token}"
        );
    }
}