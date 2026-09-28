<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_categories(): void
    {
        Category::factory()
            ->count(3)
            ->create();

        $this->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_can_view_category(): void
    {
        $category = Category::factory()->create();

        $this->getJson("/api/v1/categories/{$category->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $category->id)
            ->assertJsonPath('data.name', $category->name)
            ->assertJsonPath('data.slug', $category->slug)
            ->assertJsonPath('data.description', $category->description)
            ->assertJsonPath('data.is_active', $category->is_active);
    }

    public function test_returns_404_for_nonexistent_category(): void
    {
        $this->getJson('/api/v1/categories/999999')
            ->assertNotFound();
    }

    public function test_can_create_category(): void
    {
        $payload = [
            'name' => 'Hardware',
            'slug' => 'hardware',
            'description' => 'Problemas relacionados a hardware.',
            'is_active' => true,
        ];

        $this->postJson('/api/v1/categories', $payload)
            ->assertCreated()
            ->assertJsonPath('data.name', 'Hardware')
            ->assertJsonPath('data.slug', 'hardware')
            ->assertJsonPath('data.description', 'Problemas relacionados a hardware.')
            ->assertJsonPath('data.is_active', true);

        $this->assertDatabaseHas('categories', [
            'name' => 'Hardware',
            'slug' => 'hardware',
        ]);
    }

    public function test_category_requires_name_and_slug(): void
    {
        $this->postJson('/api/v1/categories', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'slug',
            ]);
    }

    public function test_category_name_must_be_unique(): void
    {
        Category::factory()->create([
            'name' => 'Hardware',
            'slug' => 'hardware',
        ]);

        $this->postJson('/api/v1/categories', [
            'name' => 'Hardware',
            'slug' => 'hardware-2',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_category_slug_must_be_unique(): void
    {
        Category::factory()->create([
            'name' => 'Hardware',
            'slug' => 'hardware',
        ]);

        $this->postJson('/api/v1/categories', [
            'name' => 'Hardware 2',
            'slug' => 'hardware',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('slug');
    }

    public function test_can_update_category(): void
    {
        $category = Category::factory()->create();

        $payload = [
            'name' => 'Updated Category',
            'slug' => 'updated-category',
            'description' => 'Updated description.',
            'is_active' => false,
        ];

        $this->putJson(
            "/api/v1/categories/{$category->id}",
            $payload
        )
            ->assertOk()
            ->assertJsonPath('data.id', $category->id)
            ->assertJsonPath('data.name', 'Updated Category')
            ->assertJsonPath('data.slug', 'updated-category')
            ->assertJsonPath('data.description', 'Updated description.')
            ->assertJsonPath('data.is_active', false);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
            'slug' => 'updated-category',
            'description' => 'Updated description.',
            'is_active' => false,
        ]);
    }

    public function test_can_update_category_without_changing_unique_fields(): void
    {
        $category = Category::factory()->create([
            'name' => 'Hardware',
            'slug' => 'hardware',
        ]);

        $this->putJson(
            "/api/v1/categories/{$category->id}",
            [
                'name' => 'Hardware',
                'slug' => 'hardware',
                'description' => 'Updated description.',
            ]
        )
            ->assertOk()
            ->assertJsonPath('data.name', 'Hardware')
            ->assertJsonPath('data.slug', 'hardware');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Hardware',
            'slug' => 'hardware',
            'description' => 'Updated description.',
        ]);
    }

    public function test_cannot_update_category_with_duplicate_name(): void
    {
        $category = Category::factory()->create([
            'name' => 'Hardware',
            'slug' => 'hardware',
        ]);

        Category::factory()->create([
            'name' => 'Software',
            'slug' => 'software',
        ]);

        $this->putJson(
            "/api/v1/categories/{$category->id}",
            [
                'name' => 'Software',
                'slug' => 'hardware-updated',
            ]
        )
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_cannot_update_category_with_duplicate_slug(): void
    {
        $category = Category::factory()->create([
            'name' => 'Hardware',
            'slug' => 'hardware',
        ]);

        Category::factory()->create([
            'name' => 'Software',
            'slug' => 'software',
        ]);

        $this->putJson(
            "/api/v1/categories/{$category->id}",
            [
                'name' => 'Hardware Updated',
                'slug' => 'software',
            ]
        )
            ->assertUnprocessable()
            ->assertJsonValidationErrors('slug');
    }

    public function test_returns_404_when_updating_nonexistent_category(): void
    {
        $this->putJson('/api/v1/categories/999999', [
            'name' => 'Hardware',
            'slug' => 'hardware',
        ])
            ->assertNotFound();
    }

    public function test_can_delete_category(): void
    {
        $category = Category::factory()->create();

        $this->deleteJson("/api/v1/categories/{$category->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_returns_404_when_deleting_nonexistent_category(): void
    {
        $this->deleteJson('/api/v1/categories/999999')
            ->assertNotFound();
    }
}
