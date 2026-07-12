<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for Category (EmissionCategory) - T087
 */
class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_category(): void
    {
        $category = Category::factory()->create([
            'code' => '1.1',
            'name' => 'Sources fixes de combustion',
            'scope' => 1,
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'code' => '1.1',
            'scope' => 1,
        ]);
    }

    public function test_can_create_scope1_category(): void
    {
        $category = Category::factory()->scope1()->create();

        $this->assertEquals(1, $category->scope);
        $this->assertStringStartsWith('1.', $category->code);
    }

    public function test_can_create_scope2_category(): void
    {
        $category = Category::factory()->scope2()->create();

        $this->assertEquals(2, $category->scope);
        $this->assertStringStartsWith('2.', $category->code);
    }

    public function test_can_create_scope3_category(): void
    {
        $category = Category::factory()->scope3()->create();

        $this->assertEquals(3, $category->scope);
        $this->assertNotNull($category->scope_3_category);
    }

    public function test_category_can_have_parent(): void
    {
        $parent = Category::factory()->create(['code' => '3.1']);
        $child = Category::factory()->create([
            'code' => '3.1.1',
            'parent_id' => $parent->id,
        ]);

        $this->assertEquals($parent->id, $child->parent_id);
        $this->assertNotNull($child->parent);
        $this->assertEquals($parent->id, $child->parent->id);
    }

    public function test_category_can_have_children(): void
    {
        $parent = Category::factory()->create();
        $children = Category::factory()->count(3)->create([
            'parent_id' => $parent->id,
        ]);

        $this->assertCount(3, $parent->children);
    }

    public function test_category_has_translated_name(): void
    {
        $category = Category::factory()->create([
            'name' => 'Électricité',
            'translations' => [
                'en' => ['name' => 'Electricity'],
                'de' => ['name' => 'Elektrizität'],
            ],
        ]);

        app()->setLocale('fr');
        $this->assertEquals('Électricité', $category->translated_name);

        app()->setLocale('en');
        $this->assertEquals('Electricity', $category->translated_name);
    }

    public function test_active_scope_filters_inactive_categories(): void
    {
        Category::factory()->count(3)->create(['is_active' => true]);
        Category::factory()->count(2)->inactive()->create();

        $this->assertCount(3, Category::active()->get());
        $this->assertCount(5, Category::all());
    }

    public function test_scope_filter_by_scope_number(): void
    {
        Category::factory()->scope1()->count(2)->create();
        Category::factory()->scope2()->count(1)->create();
        Category::factory()->scope3()->count(3)->create();

        $this->assertCount(2, Category::scope(1)->get());
        $this->assertCount(1, Category::scope(2)->get());
        $this->assertCount(3, Category::scope(3)->get());
    }

    public function test_root_scope_excludes_child_categories(): void
    {
        $parent = Category::factory()->create();
        Category::factory()->count(2)->create(['parent_id' => $parent->id]);

        $this->assertCount(1, Category::root()->get());
    }

    public function test_matches_mcc_code(): void
    {
        $category = Category::factory()->create([
            'mcc_codes' => ['5411', '5412', '5413'],
        ]);

        $this->assertTrue($category->matchesMcc('5411'));
        $this->assertTrue($category->matchesMcc('5412'));
        $this->assertFalse($category->matchesMcc('9999'));
    }

    public function test_category_can_have_emission_factors(): void
    {
        $category = Category::factory()->create();

        // Note: This tests the relationship exists
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $category->emissionFactors());
    }
}
