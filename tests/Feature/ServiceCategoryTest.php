<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Company;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_a_category(): void
    {
        $user = $this->staff(Role::Manager);

        $this->actingAs($user)->post(route('service-categories.store'), [
            'name' => 'Consulting',
            'sort_order' => 5,
        ]);

        $category = ServiceCategory::firstWhere('name', 'Consulting');

        $this->assertNotNull($category);
        $this->assertSame('consulting', $category->slug);
        $this->assertSame($user->company_id, $category->company_id);
    }

    public function test_category_names_are_unique_per_company(): void
    {
        $user = $this->staff(Role::Manager);
        ServiceCategory::create(['company_id' => $user->company_id, 'name' => 'Consulting']);

        $this->actingAs($user)
            ->post(route('service-categories.store'), ['name' => 'Consulting'])
            ->assertSessionHasErrors('name');
    }

    public function test_renaming_updates_the_slug(): void
    {
        $user = $this->staff(Role::Manager);
        $category = ServiceCategory::create(['company_id' => $user->company_id, 'name' => 'Consulting']);

        $this->actingAs($user)->put(route('service-categories.update', $category), [
            'name' => 'Advisory Services',
        ]);

        $this->assertSame('advisory-services', $category->fresh()->slug);
    }

    public function test_a_category_in_use_cannot_be_deleted(): void
    {
        $user = $this->staff(Role::Manager);
        $category = ServiceCategory::create(['company_id' => $user->company_id, 'name' => 'Hosting']);
        Service::factory()->create([
            'company_id' => $user->company_id,
            'service_category_id' => $category->id,
        ]);

        $this->actingAs($user)
            ->delete(route('service-categories.destroy', $category))
            ->assertSessionHasErrors('category');

        $this->assertDatabaseHas('service_categories', ['id' => $category->id]);
    }

    public function test_an_empty_category_can_be_deleted(): void
    {
        $user = $this->staff(Role::Manager);
        $category = ServiceCategory::create(['company_id' => $user->company_id, 'name' => 'Unused']);

        $this->actingAs($user)->delete(route('service-categories.destroy', $category));

        $this->assertDatabaseMissing('service_categories', ['id' => $category->id]);
    }

    public function test_sales_cannot_manage_categories(): void
    {
        $this->actingAs($this->staff(Role::Sales))
            ->post(route('service-categories.store'), ['name' => 'Nope'])
            ->assertForbidden();
    }

    public function test_categories_from_another_company_are_protected(): void
    {
        $user = $this->staff(Role::Manager);
        $foreign = ServiceCategory::create([
            'company_id' => Company::factory()->create()->id,
            'name' => 'Foreign',
        ]);

        $this->actingAs($user)
            ->put(route('service-categories.update', $foreign), ['name' => 'Hijacked'])
            ->assertForbidden();
    }
}
