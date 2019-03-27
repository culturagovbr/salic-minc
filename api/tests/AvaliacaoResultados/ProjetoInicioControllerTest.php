<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ProjetoInicioControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    public function testGet()
    {
        $this->json(
            'GET',
            '/avaliacao-resultados/projeto-inicio/{idpronac}'
        )->seeJson([
            'teste Projeto Inicio GET'
        ]);
    }

    public function testIndex()
    {
        $this->json(
            'GET',
            '/avaliacao-resultados/projeto-inicio'
        )->seeJson([
            'teste Projeto Inicio INDEX'
        ]);
    }
}
