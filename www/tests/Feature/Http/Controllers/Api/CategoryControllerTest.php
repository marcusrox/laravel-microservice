<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    
    public function testIndex()
    {
        
        $category = factory(Category::class)->create();

        $response = $this->get('/api/categories');
        $response
            ->assertStatus(200)
            ->assertJson([$category->toArray()]); // Verifica a caegoria está present no retorno em formato Json

    }

    public function testShow()
    {
        $category = factory(Category::class)->create();

        $response = $this->get('/api/categories/'. $category->id);
        $response
            ->assertStatus(200)
            ->assertJson($category->toArray()); // Verifica a caegoria está present no retorno em formato Json

    }

    public function testInvalidData() {
        $response = $this->json('POST', '/api/categories', []);
        //$this->assertInvalidationRequired($response);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active']);
        

        $response = $this->json('POST', '/api/categories', [
            'name' => str_repeat('a', 256), 'is_active' => 'a'
        ]);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'is_active']);

        //$this->assertInvalidatioMax($response);
        //$this->assertInvalidatioBoolean($response);

        //Testar uma atualização
        $category = factory(Category::class)->create();
        $response = $this->json('PUT', '/api/categories/'.$category->id, []);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active']);        

    }

    public function testStore()
    {
        $response = $this->json('POST', '/api/categories', [
            'name' => 'Teste',
            'id_active' => 1
        ]);
        //dump($response->content());
        $id = $response->json('id');
        $category = Category::find($id);
        $this->assertNotNull($category);

        $response
            ->assertStatus(201)
            ->assertJson($category->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));

        $response = $this->json('POST', '/api/categories', [
            'name' => 'Teste 2',
            'description' => 'teste description'
        ]);
        $response->assertJsonFragment([
            'name' => 'Teste 2',
            'description' => 'teste description'

        ]);

    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create([
            'name' => 'Teste',
            'is_active' => false
        ]);
        $response = $this->json('PUT', '/api/categories/'. $category->id, [
            'name' => 'Teste 2',
            'description' => 'teste description',
            'is_active' => true
        ]);
        //dump($response->content());
        $id = $response->json('id');
        $category = Category::find($id);
        $this->assertNotNull($category);

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray())
            ->assertJsonFragment([
                'name' => 'Teste 2',
                'description' => 'teste description',
                'is_active' => true
        ]);

        // Testar novamente usando a mesma category já existente no banco
        // Nesse caso o description = '' deve ser convertida pra NULL pelo middleware TrimStrings
        $response = $this->json('PUT', '/api/categories/' . $category->id, [
            'description' => '',
        ]);
        $this->assertNull($response->json('description'));
    }

    public function testDestroy()
    {
        $category = factory(Category::class)->create();
        $response = $this->json('DELETE', '/api/categories/'. $category->id, []);
        $response->assertStatus(204);
        $this->assertNull(Category::find($category->id)); // Garantir que foi excluido
        $this->assertNotNull(Category::withTrashed()->find($category->id)); // Garantir que está na lixeira
    }
}
