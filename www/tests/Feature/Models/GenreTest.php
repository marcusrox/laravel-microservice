<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations; // faz as migrations antes dos testes

    public function testList()
    {
        factory(Genre::class, 1)->create();
        $genre = Genre::all();
        $this->assertCount(1, $genre);
        $attributes = array_keys($genre->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            ['id', 'name', 'is_active', 'created_at', 'updated_at', 'deleted_at'],
            $attributes
        );
    }

    public function testCreate()
    {
        $genre = Genre::create(['name' => 'Teste']);
        $genre->refresh();
        $this->assertEquals("Teste", $genre->name);
        $this->assertTrue($genre->is_active);

        // Validar se formato do UUID está correto
        $this->assertTrue(preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $genre->id) === 1);

    }

    public function testDelete()
    {
        $genre = Genre::create(['name' => 'Teste']);
        $genre->refresh();

        $genre = Genre::firstWhere('name', 'Teste');
        $this->assertNotNull($genre);

        $genre->delete();

        $genre = Genre::firstWhere('name', 'Teste');
        $this->assertNull($genre);

    }


}
