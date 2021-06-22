<?php

namespace Application\Modules\Projeto\Service\Projeto;


class Projeto
{
    /**
     * @var \Zend_Controller_Request_Abstract $request
     */
    private $request;

    /**
     * @var \Zend_Controller_Response_Abstract $response
     */
    private $response;


    function __construct(\Zend_Controller_Request_Abstract $request, \Zend_Controller_Response_Abstract $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function alterarSituacao($idPronac = null, $pronac = null, $situacao, $ProvidenciaTomada = null, $idUsuario = null)
    {
        // pega logon para gravar alteracao da situacao
        $auth = \Zend_Auth::getInstance();
        $Logon = empty($idUsuario) ? $auth->getIdentity()->usu_codigo : $idUsuario;

        // grava no hist?rico a situacao atual do projeto caso a trigger HISTORICO_INSERT esteja desabilitada
        $HistoricoInsert = new \HistoricoInsert();

        if ($HistoricoInsert->statusHISTORICO_INSERT() == 1) { // desabilitada

            // busca a situacao atual do projeto
            $tbProjeto = new \Projetos();
            $p = $tbProjeto->buscarSituacaoAtual($idPronac, $pronac);

            // grava o hist?rico da situa??o
            if ($situacao != $p['Situacao']) :
                $dadosHistorico = array(
                    'AnoProjeto' => $p['AnoProjeto'],
                    'Sequencial' => $p['Sequencial'],
                    'DtSituacao' => $p['DtSituacao'],
                    'Situacao' => $p['Situacao'],
                    'ProvidenciaTomada' => $p['ProvidenciaTomada'],
                    'Logon' => $p['Logon']);
                $HistoricoSituacao = new \HistoricoSituacao();
                $cadastrarHistorico = $HistoricoSituacao->insert($dadosHistorico);
            endif;
        } // fecha if

        $dados = array(
            'Situacao' => $situacao
        , 'DtSituacao' => new \Zend_Db_Expr('GETDATE()')
        , 'ProvidenciaTomada' => $ProvidenciaTomada
        , 'Logon' => $Logon);

        $where = '';
        // alterar pelo idPronac
        if (!empty($idPronac)) {
            $where = "IdPRONAC = " . $idPronac;
        }

        // alterar pelo pronac
        if (!empty($pronac)) {
            $where = "(AnoProjeto+Sequencial) = '" . $pronac . "'";
        }

        if (!empty($where)) {
            return $tbProjeto->update($dados, $where);
        } else {
            return new \Exception("Erro ao alterar situa&ccedil;&atilde;o do Projeto.");
        }
    }

}