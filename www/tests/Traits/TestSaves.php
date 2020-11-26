<?php

declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;

trait TestSaves
{

    protected abstract function model();
    protected abstract function routeStore();
    protected abstract function routeUpdate();    

    protected function assertStore(array $sendData, array $testData) : TestResponse
    {
        $response = $this->json('POST', $this->routeStore(), $sendData);
        if ($response->status() !== 201) {
            throw new \Exception("Response status must be 201, given {$response->status()} :\n {$response->content()}");
        }
        $this->assertInDatabase($response, $testData + ['id' => $response->json('id')]);
        return $response;
    }

    protected function assertUpdate(array $sendData, array $testData): TestResponse
    {
        $response = $this->json('PUT', $this->routeUpdate(), $sendData);
        if ($response->status() !== 200) {
            throw new \Exception("Response status must be 200, given {$response->status()} :\n {$response->content()}");
        }
        $this->assertInDatabase($response, $testData + ['id' => $response->json('id')]);
        return $response;
    }    

    private function assertInDatabase($response, $testData)
    {
        $model = $this->model();
        $table = (new $model)->getTable();
        $this->assertDatabaseHas($table, $testData + ['id' => $response->json('id')]);
    }

}
