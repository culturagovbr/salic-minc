<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class EmissaoParecerRestControllerTest extends TestCase
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
            '/avaliacao-resultados/emissao-parecer/{idpronac}'
        )->seeJson([
            'teste Emissao Parecer GET'
        ]);
    }
    public function testPost()
    {
        $this->json(
            'POST',
            '/avaliacao-resultados/emissao-parecer'
        )->seeJson([
            'teste Emissao Parecer Post'
        ]);
    }
    public function testIndex()
    {
        $this->json(
            'GET',
            '/avaliacao-resultados/emissao-parecer'
        )->seeJson([
            'teste Emissao Parecer INDEX'
        ]);
    }
}
