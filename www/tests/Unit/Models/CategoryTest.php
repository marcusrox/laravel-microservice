<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use  App\Models\Traits\Uuid;

class CategoryTest extends TestCase
{
    //use DatabaseMigrations; // faz as migrations antes dos testes
    private $category;

    // Antes de executar os testes, roda somente uma vez esse método setUpBeforeClass()
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    // Antes de cada test (método) ser executado, ele roda esse setUp()
    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category();
    }

    //Após cada test (método) ser executado, ele roda esse tearDown()
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    // Depois de executar os testes, roda somente uma vez esse método tearDownAfterClass()
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
    }

    public function testIfUseTraits()
    {
        $traits = [SoftDeletes::class, Uuid::class ];
        $categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEquals($traits, $categoryTraits);
    }

    public function testFillableAttributes()
    {
        $fillable = ['name', 'description', 'is_active'];
        $this->assertEquals($fillable, $this->category->getFillable());
    }

    public function testDateAtributtes()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        foreach($dates as $date) {
            $this->assertContains($date, $this->category->getDates());
        }
        $this->assertCount(count($dates), $this->category->getDates());
    }
    
}
