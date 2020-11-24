<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;

    
    public function testIndex()
    {

        $genre = factory(Genre::class)->create();

        $response = $this->get('/api/genres');
        $response
            ->assertStatus(200)
            ->assertJson([$genre->toArray()]); // Verifica a caegoria está present no retorno em formato Json

    }

    public function testShow()
    {
        $genre = factory(Genre::class)->create();

        $response = $this->get('/api/genres/'. $genre->id);
        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray()); // Verifica a caegoria está present no retorno em formato Json

    }

    public function testInvalidData() {
        $response = $this->json('POST', '/api/genres', []);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active']);

        $response = $this->json('POST', '/api/genres', [
            'name' => str_repeat('a', 256), 'is_active' => 'a'
        ]);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'is_active']);

        //Testar uma atualização
        $genre = factory(Genre::class)->create();
        $response = $this->json('PUT', '/api/genres/'.$genre->id, []);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active']);        

    }

    public function testStore()
    {
        $response = $this->json('POST', '/api/genres', [
            'name' => 'Teste',
            'id_active' => 1
        ]);
        //dump($response->content());
        $id = $response->json('id');
        $genre = Genre::find($id);
        $this->assertNotNull($genre);

        $response
            ->assertStatus(201)
            ->assertJson($genre->toArray());

        $this->assertTrue($response->json('is_active'));

        $response = $this->json('POST', '/api/genres', [
            'name' => 'Teste 2',
        ]);
        $response->assertJsonFragment([
            'name' => 'Teste 2',

        ]);

    }

    public function testUpdate()
    {
        $genre = factory(Genre::class)->create([
            'name' => 'Teste',
            'is_active' => false
        ]);
        $response = $this->json('PUT', '/api/genres/'. $genre->id, [
            'name' => 'Teste 2',
            'is_active' => true
        ]);
        //dump($response->content());
        $id = $response->json('id');
        $genre = Genre::find($id);
        $this->assertNotNull($genre);

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray())
            ->assertJsonFragment([
                'name' => 'Teste 2',
                'is_active' => true
        ]);

    }

    public function testDestroy()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->json('DELETE', '/api/genres/'. $genre->id, []);
        $response->assertStatus(204);
        $this->assertNull(Genre::find($genre->id)); // Garantir que foi excluido
        $this->assertNotNull(Genre::withTrashed()->find($genre->id)); // Garantir que está na lixeira
    }
}
