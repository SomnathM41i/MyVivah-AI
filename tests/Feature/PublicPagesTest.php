<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 5A — public marketing pages render, are indexable, and expose no
 * authenticated content.
 */
class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_and_promotes_the_product(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('MyVivahAI')
            ->assertSee('Get started free')
            ->assertSee('Real-time chat')
            ->assertSee('AI assistants');
    }

    public function test_about_page_loads(): void
    {
        $this->get('/about')
            ->assertOk()
            ->assertSee('Our story')
            ->assertSee('API-first');
    }

    public function test_services_page_lists_catalog_and_roadmap(): void
    {
        $this->get('/services')
            ->assertOk()
            ->assertSee('Available now')
            ->assertSee('On the roadmap');
    }

    public function test_plans_page_reflects_the_database_catalog(): void
    {
        $service = Service::factory()->active()->create(['name' => 'Real-time Chat']);
        Plan::factory()->active()->for($service)->create([
            'plan_key' => 'growth',
            'name' => 'Growth',
            'price' => 499.00,
            'features' => ['history_days' => 30],
        ]);

        $this->get('/plans')
            ->assertOk()
            ->assertSee('Plans & pricing')
            ->assertSee('Growth')
            ->assertSee('₹499');
    }

    public function test_plans_page_hides_inactive_plans(): void
    {
        $service = Service::factory()->active()->create();
        Plan::factory()->active()->for($service)->create(['name' => 'Visible Plan']);

        // An inactive plan for a live service must never render.
        Plan::factory()->for($service)->create(['name' => 'Hidden Draft Plan', 'is_active' => false]);
        Plan::factory()->active()->create(['name' => 'Orphaned Plan']);

        $this->get('/plans')
            ->assertOk()
            ->assertSee('Visible Plan')
            ->assertDontSee('Hidden Draft Plan')
            ->assertDontSee('Orphaned Plan');
    }

    public function test_contact_page_loads_with_the_form(): void
    {
        $this->get('/contact')
            ->assertOk()
            ->assertSee('Send us a message');
    }

    public function test_public_pages_are_indexable_by_search_engines(): void
    {
        foreach (['/', '/about', '/services', '/plans', '/contact'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('name="robots" content="index, follow"', false);
        }
    }

    public function test_auth_pages_are_not_indexable(): void
    {
        foreach (['/login', '/signup', '/forgot-password'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('name="robots" content="noindex, nofollow"', false);
        }
    }
}
