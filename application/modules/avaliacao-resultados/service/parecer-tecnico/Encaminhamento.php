<?php

namespace Application\Modules\AvaliacaoResultados\Service\ParecerTecnico;

class Encaminhamento
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

        $GrupoAtivo = new \Zend_Session_Namespace('GrupoAtivo');

//        $GrupoAtivo->unlockAll();
        // xd($GrupoAtivo);
//        xd($GrupoAtivo->__get('codGrupo'));
//        $authIdentityInstance = (array) \Zend_Auth::getInstance()->getIdentity();

//        xd(gettype($GrupoAtivo));
//        foreach($GrupoAtivo as $chave => $valor){
//            $this->{$chave} = $valor;
//        }
    }

    public function buscarHistorico()
    {

        $tblEncaminhamento = new \tbEncaminhamentoPrestacaoContas();
        $historicos = $tblEncaminhamento->HistoricoEncaminhamentoPrestacaoContas($this->request->idPronac)->toArray();

        return $historicos;
    }

    public function encaminharProjeto()
    {
        // caso o formulario seja enviado via post
        $GrupoAtivo = new \Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $auth = \Zend_Auth::getInstance();
        $Usuario = new \Autenticacao_Model_DbTable_Usuario();
        $idagente = $Usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
        $idAgenteOrigem = $idagente['idAgente'];
        $idPerfilDestino = (null === $this->request->getParam('idPerfilDestino')) ? 124 : $this->request->getParam('idPerfilDestino'); // se nao receber idPerfilDestino, define como 124 por padrao (tecnico)
        $this->usu_codigo = $auth->getIdentity()->usu_codigo;

        // recebe os dados via post
        $post = \Zend_Registry::get('post');
        if (!empty($post->dsjustificativa)) {
            $idPronac = $post->idPronac;
            $dtInicioEncaminhamento = new \Zend_Db_Expr('GETDATE()');
            $dsJustificativa = $post->dsjustificativa;
            $idOrgaoOrigem = $this->codOrgao;
            $idOrgaoDestino = $post->passaValor;
            $arrAgenteGrupo = explode("/", $post->recebeValor);
            $idAgenteOrigem = $auth->getIdentity()->usu_codigo;
            $idAgenteDestino = $arrAgenteGrupo[0];
            $idGrupoDestino = $arrAgenteGrupo[1];
            $idSituacaoPrestContas = $post->idSituacaoPrestContas;

            try {
                //GRUPO : ORGAO
                //100: 177 AECI
                //100: 12 CONJUR
                //SE O ENCAMINHAMENTO FOR DO COORDENADOR PARA O TECNICO - ALTERA SITUACAO DO PROJETO

                if (
                    ($this->codGrupo == 125 || $this->codGrupo == 126 || $this->codGrupo == 132) &&
                    ($idGrupoDestino == 124 || $idGrupoDestino == 125)
                ) {
                    // altera a situacao do projeto AO ENCAMINHAR PARA O TECNICO
                    $tblProjeto = new \Projetos();
                    $tblProjeto->alterarSituacao($idPronac, '', 'E27', 'Comprova&ccedil;&atilde;o Financeira do Projeto em Análise');
                } elseif ($this->codGrupo == 124 && $idGrupoDestino == 132) {
                    // SE O ENCAMINHAMENTO FOR DO TECNICO PARA O CHEFE/COORDENADOR (DEVOLUCAO) - ALTERAR SITUACAO DO PROJETO
                    $tblProjeto = new \Projetos();
                    $tblProjeto->alterarSituacao($idPronac, '', 'E68', 'Projeto devolvido para o Chefe de Divis&atilde;o - Aguarda an&aacute;lise financeira');
                }

                //BUSCA ULTIMO STATUS DO PROJETO
                $tblProjeto = new \Projetos();
                $rsProjeto = $tblProjeto->find($idPronac)->current();
                $idSituacao = $rsProjeto->Situacao;

                //ENCAMINHA PROJETO
                $dados = array(
                    'idPronac' => $idPronac,
                    'idAgenteOrigem' => $idAgenteOrigem,
                    'idAgenteDestino' => $idAgenteDestino,
                    'idOrgaoOrigem' => $idOrgaoOrigem,
                    'idOrgaoDestino' => $idOrgaoDestino,
                    'dtInicioEncaminhamento' => $dtInicioEncaminhamento,
                    'dtFimEncaminhamento' => new \Zend_Db_Expr('GETDATE()'),
                    'dsJustificativa' => $dsJustificativa,
                    'cdGruposOrigem' => $this->codGrupo,
                    'cdGruposDestino' => $idGrupoDestino,
                    'idSituacaoEncPrestContas' => $idSituacaoPrestContas,
                    'idSituacao' => $idSituacao,
                    'stAtivo' => 1
                );
                $tblEncaminhamento = new \EncaminhamentoPrestacaoContas();

                $idTblEncaminhamento = $tblEncaminhamento->inserir($dados);

                if ($idTblEncaminhamento) {
                    // altera todos os encaminhamentos anteriores para stAtivo = 0
                    $tblEncaminhamento->update(
                        array('stAtivo' => 0), 
                        array('idPronac = ?' => $idPronac, 'idEncPrestContas != ?' => $idTblEncaminhamento)
                    );
                }

//                if ($this->codGrupo == 132) {
//                    parent::message('Solicita&ccedil;&atilde;o enviada com sucesso!', "realizarprestacaodecontas/chefedivisaoprestacaocontas?tipoFiltro=" . $tipoFiltro, 'CONFIRM');
//                } elseif ($this->codGrupo == 124) {
//                    parent::message('Solicita&ccedil;&atilde;o enviada com sucesso!', "realizarprestacaodecontas/tecnicoprestacaocontas?tipoFiltro=" . $tipoFiltro, 'CONFIRM');
//                } else {
//                    parent::message('Solicita&ccedil;&atilde;o enviada com sucesso!', "realizarprestacaodecontas/painel?tipoFiltro=" . $tipoFiltro, 'CONFIRM');
//                }
            } catch (Exception $e) {
                parent::message('Erro ao tentar salvar os dados!', "principal", 'ERROR');
            }
        }
    }

    public function buscarDestinatariosAction()
    {
//        $tbProjetos = new \Projetos();
        $ttea = \Zend_Auth::getInstance()->getIdentity();
//        xd($ttea);

        xd($this->codOrgao);
//        xd($teste->codOrgao,$teste->codGrupo);

        return $tbProjetos->buscarComboOrgaos(
            $this->request->idOrgaoDestino,
            $this->request->idPerfilDestino
        );
    }

    public function salvar()
    {
//        $authInstance = \Zend_Auth::getInstance();
//        $arrAuth = array_change_key_case((array) $authInstance->getIdentity());
//
//        $parametros = $this->request->getParams();
//        $tbAvaliacaoFinanceira = new \AvaliacaoResultados_Model_tbAvaliacaoFinanceira($parametros);
//        $tbAvaliacaoFinanceira->setDtAvaliacaoFinanceira(date('Y-m-d h:i:s'));
//        $tbAvaliacaoFinanceira->setIdUsuario($arrAuth['usu_codigo']);
//
//        $mapper = new \AvaliacaoResultados_Model_tbAvaliacaoFinanceiraMapper();
//        $codigo = $mapper->save($tbAvaliacaoFinanceira);
//
//        $this->request->setParam('idAvaliacaoFinanceira', $codigo);
//
//        if(!$codigo){
//            return $mapper->getMessages();
//        }
//
//        return $this->buscarAvaliacaoFinanceira();
    }

}