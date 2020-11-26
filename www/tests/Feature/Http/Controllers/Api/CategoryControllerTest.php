<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\Traits\TesteValidations;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;
    use TestValidations;
    use TestSaves;

    private $category;

    // Antes de cada test (método) ser executado, ele roda esse setUp()
    protected function setUp(): void
    {
        parent::setUp();
        $this->category = factory(Category::class)->create();
    }    

    public function testIndex() 
    {
        $response = $this->get('/api/categories');
        $response
            ->assertStatus(200)
            ->assertJson([$this->category->toArray()]); // Verifica a caegoria está present no retorno em formato Json

    }

    public function testShow()
    {
        $response = $this->get('/api/categories/'. $this->category->id);
        $response
            ->assertStatus(200)
            ->assertJson($this->category->toArray()); // Verifica a caegoria está present no retorno em formato Json

    }

    public function testInvalidationData() 
    {
        $data = ['name' => ''];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = ['name' => str_repeat('x', 256)];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

        $data = ['is_active' => 'a'];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');

    }

    public function testStore()
    {

        $data = ['name' => 'teste store'];
        $this->assertStore($data, $data + ['description' => null, 'is_active' => true, 'deleted_at' => null]);

        $data = ['name' => 'teste store', 'description' => 'teste desc', 'is_active' => false];
        $response = $this->assertStore($data, $data);

        $response->assertJsonStructure(['created_at', 'updated_at']);

    }

    public function testUpdate()
    {

        $category = factory(Category::class)->create(
            ['name' => 'test name', 'description' => 'test desc', 'is_active' => false]
        );
        
        $data = ['name' => 'test name 2', 'description' => 'test desc 2', 'is_active' => true];

        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);
        $response->assertJsonStructure(['created_at', 'updated_at']);

        $data = ['name' => 'teste name 3', 'description' => ''];
        $response = $this->assertUpdate($data, array_merge($data, ['description' => null]));

    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', '/api/categories/'. $this->category->id, []);
        $response->assertStatus(204);
        $this->assertNull(Category::find($this->category->id)); // Garantir que foi excluido
        $this->assertNotNull(Category::withTrashed()->find($this->category->id)); // Garantir que está na lixeira
    }

    protected function assertInvalidationRequired(TestResponse $response) {

    }

    protected function routeStore()
    {
        return route('categories.store');
    }

    protected function routeUpdate()
    {
        return route('categories.update', ['category' => $this->category->id]);
    }

    protected function model() {
        return Category::class;
    }

}
