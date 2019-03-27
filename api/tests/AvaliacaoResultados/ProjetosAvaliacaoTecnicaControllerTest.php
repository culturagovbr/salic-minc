<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ProjetosAvaliacaoTecnicaControllerTest extends TestCase
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
            '/avaliacao-resultados/projetos-avaliacao-tecnica'
        )->seeJson([
            'teste Projetos Avaliacao Tecnica INDEX'
        ]);
    }
}
