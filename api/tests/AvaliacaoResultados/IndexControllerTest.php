<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class IndexControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $this->json(
            'GET',
            '/avaliacao-resultados/index'
        )->seeJson([
            'teste IndexController INDEX'
        ]);
    }
}
