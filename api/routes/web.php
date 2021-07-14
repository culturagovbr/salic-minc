<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['middleware' => 'jwt'], function () use ($router) {
    $router->get(
        'user',
        [
            'uses' => 'AuthController@user'
        ]
    );
});

$router->get('/check', function () use ($router) {
    echo '<pre> teste';
    $results = DB::select("SELECT top 1 * FROM controledeacesso.dbo.sgcacesso");
    dd($results);
});

$router->get(
    '/avaliacao-resultados/historico',
    ['uses' => 'HistoricoController@index']
);


$router->group(
    [
        'prefix' => 'avaliacao-resultados',
        'namespace' => '\App\AvaliacaoResultados\Http\Controllers'
    ],
    function() use ($router) {
        $router->get(
            'historico/{idpronac}',
            ['uses' => 'HistoricoController@index']
        );

        $router->get(
            'projetos-similares/{idpronac}',
            ['uses' => 'ProjetosSimilaresController@index']
        );

        $router->get(
            'assinaturas',
            ['uses' => 'AssinaturasController@get']
        );

        $router->get(
            'avaliacao-comprovante',
            ['uses' => 'AvaliacaoComprovanteController@get']
        );

        $router->get(
            'dashboard',
            ['uses' => 'DashboardController@index']
        );
        $router->get(
            'detalhamento-itens/{idpronac}',
            ['uses' => 'DetalhamentoItensRestController@get']
        );
        $router->get(
            'detalhamento-itens',
            ['uses' => 'DetalhamentoItensRestController@index']

        );
        $router->get(
            'diligencia/{idpronac}',
            ['uses' => 'DiligenciaController@get']
        );
        $router->get(
            'diligencia',
            ['uses' => 'DiligenciaController@index']
        );
        $router->get(
            'emissao-parecer/{idpronac}',
            ['uses' => 'EmissaoParecerRestController@get']
        );
        $router->get(
            'emissao-parecer',
            ['uses' => 'EmissaoParecerRestController@index']
        );
        $router->get(
            'estado/{idpronac}',
            ['uses' => 'EstadoController@get']
        );
        $router->get(
            'estado',
            ['uses' => 'EstadoController@index']
        );
        $router->get(
            'fluxo-projeto/{idpronac}',
            ['uses' => 'FluxoProjetoController@get']
        );
        $router->get(
            'fluxo-projeto',
            ['uses' => 'FluxoProjetoController@index']
        );
        $router->get(
            'fluxos',
            ['uses' => 'FluxosController@index']
        );
        $router->get(
            'index',
            ['uses' => 'IndexController@index']
        );
        $router->get(
            'laudo/{idpronac}',
            ['uses' => 'LaudoController@get']
        );

        $router->get(
            'laudo',
            ['uses' => 'LaudoController@index']
        );
        $router->get(
            'planilha-aprovada/{idpronac}',
            ['uses' => 'PlanilhaAprovadaController@get']
        );
        $router->get(
            'projeto-assinatura/{idpronac}',
            ['uses' => 'ProjetoAssinaturaController@get']
        );
        $router->get(
            'projeto-assinatura',
            ['uses' => 'ProjetoAssinaturaController@index']
        );
        $router->get(
            'projeto-inicio/{idpronac}',
            ['uses' => 'ProjetoInicioController@get']
        );
        $router->get(
            'projeto-inicio',
            ['uses' => 'ProjetoInicioController@index']
        );
        $router->get(
            'projetos-avaliacao-tecnica',
            ['uses' => 'ProjetosAvaliacaoTecnicaController@index']
        );
        $router->get(
            'tecnicos/{idpronac}',
            ['uses' => 'TecnicosController@get']
        );
        $router->get(
            'tecnicos',
            ['uses' => 'TecnicosController@index']
        );
        $router->get(
            'tipo-avaliacao/{idpronac}',
            ['uses' => 'TipoAvaliacaoRestController@get']
        );
        $router->get(
            'tipo-avaliacao',
            ['uses' => 'TipoAvaliacaoRestController@index']
        );


        

        $router->post(
            'assinaturas',
            ['uses'=> 'AssinaturasController@post']
        );
        $router->post(
            'avaliacao-comprovante',
            ['uses'=> 'AvaliacaoComprovanteController@post']
        );
        $router->post(
            'detalhamento-itens',
            ['uses' => 'DetalhamentoItensRestController@post']
        );
        $router->post(
            'diligencia',
            ['uses' => 'DiligenciaController@post']
        );
        $router->post(
            'emissao-parecer',
            ['uses' => 'EmissaoParecerRestController@post']
        );
        $router->post(
            'estado',
            ['uses' => 'EstadoController@post']
        );
        $router->post(
            'fluxo-projeto',
            ['uses' => 'FluxoProjetoController@post']
        );
        $router->post(
            'laudo',
            ['uses' => 'LaudoController@post']
        );
        $router->post(
            'projeto-assinatura',
            ['uses' => 'ProjetoAssinaturaController@post']
        );
        $router->post(
            'tipo-avaliacao',
            ['uses' => 'TipoAvaliacaoRestController@post']
        );












    }
);

