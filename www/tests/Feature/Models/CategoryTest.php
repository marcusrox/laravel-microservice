<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations; // faz as migrations antes dos testes

    public function testList()
    {
        factory(Category::class, 1)->create();
        $categories = Category::all();
        $this->assertCount(1, $categories);
        $categoryKeys = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            ['id', 'name', 'description', 'is_active', 'created_at', 'updated_at', 'deleted_at'],
            $categoryKeys
        );
    }

    public function testCreate()
    {
        $category = Category::create(['name' => 'Categoria Teste']);
        $category->refresh();
        $this->assertEquals("Categoria Teste", $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);
        // Validar se formato do UUID está correto
        $this->assertTrue(preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $category->id) === 1);

        $category = Category::create(['name' => 'Categoria Teste', 'is_active' => false]);
        $category->refresh();
        $this->assertFalse($category->is_active);
    }

    public function testDelete()
    {
        $category = Category::create(['name' => 'Teste']);
        $category->refresh();

        $category = Category::firstWhere('name', 'Teste');
        $this->assertNotNull($category);

        $category->delete();

        $category = Category::firstWhere('name', 'Teste');
        $this->assertNull($category);
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create(['description' => 'test description']);
        $data = [
            'name' => 'new name',
            'description' => 'new description',
            'is_active' => false
        ];
        $category->update($data);
        $category->refresh();

        foreach($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }

    }
}
