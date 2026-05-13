<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedNoCacheHeaderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_dashboard_response_has_no_store_cache_header(): void
    {
        $teacherRole = Role::firstOrCreate(['name' => Role::TEACHER], ['display_name' => 'Teacher']);

        $teacher = User::factory()->create([
            'role_id' => $teacherRole->id,
            'is_active' => true,
            'is_approved' => true,
        ]);

        $response = $this->actingAs($teacher)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertHeader('Pragma', 'no-cache');

        $cacheControl = strtolower((string) $response->headers->get('Cache-Control'));

        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertStringContainsString('private', $cacheControl);
    }
}
