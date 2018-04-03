<?php

class Admissibilidade_EnquadramentoPropostaController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        parent::perfil();
        parent::init();
        $this->auth = Zend_Auth::getInstance();
        $this->grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
    }

    public function indexAction()
    {
        $this->redirect("/admissibilidade/enquadramento-proposta/gerenciar-enquadramento");
    }

    public function sugerirEnquadramentoAction()
    {
        try {
            $get = $this->getRequest()->getParams();
            if (!isset($get['id_preprojeto']) || empty($get['id_preprojeto'])) {
                throw new Exception("Identificador da proposta não informado.");
            }
            $this->view->id_preprojeto = $get['id_preprojeto'];

            $preProjetoDbTable = new Proposta_Model_DbTable_PreProjeto();
            $preprojeto = $preProjetoDbTable->findBy(array('idPreProjeto' => $this->view->id_preprojeto));

            if (!$preprojeto) {
                throw new Exception("Proposta não encontrada.");
            }

            $this->view->id_perfil_usuario = $this->grupoAtivo->codGrupo;
            $this->view->id_orgao = $this->grupoAtivo->codOrgao;
            $this->view->id_usuario_avaliador = $this->auth->getIdentity()->usu_codigo;

            $post = $this->getRequest()->getPost();
            if (!$post['descricao_motivacao']) {
                $this->carregardadosEnquadramentoProposta($preprojeto);
            } else {
                $dadosSugestaoEnquadramento['id_orgao'] = $this->grupoAtivo->codOrgao;
                $dadosSugestaoEnquadramento['id_perfil'] = $this->grupoAtivo->codGrupo;
                $dadosSugestaoEnquadramento['id_usuario_avaliador'] = $this->auth->getIdentity()->usu_codigo;
                $dadosSugestaoEnquadramento['id_preprojeto'] = $get['id_preprojeto'];
                $dadosSugestaoEnquadramento['descricao_motivacao'] = $post['descricao_motivacao'];
                $dadosSugestaoEnquadramento['id_area'] = $post['id_area'];
                $dadosSugestaoEnquadramento['id_segmento'] = $post['id_segmento'];
                $this->salvarSugestaoEnquadramento($dadosSugestaoEnquadramento);
            }
        } catch (Exception $objException) {
            parent::message($objException->getMessage(), '/admissibilidade/enquadramento/gerenciar-enquadramento');
        }
    }

    private function carregardadosEnquadramentoProposta(array $preprojeto)
    {

        $mapperArea = new Agente_Model_AreaMapper();
        $this->view->comboareasculturais = $mapperArea->fetchPairs('Codigo', 'Descricao');

        $this->view->preprojeto = $preprojeto;

        if (count($this->view->comboareasculturais) < 1) {
            throw new Exception("N&atilde;o foram encontradas &Aacute;reas Culturais para o PRONAC informado.");
        }

        $this->view->id_perfil_usuario = $this->grupoAtivo->codGrupo;


        $ultimaSugestaoPerfil = $this->_recuperarUltimaSugestaoPerfil($preprojeto['idPreProjeto'], $this->view->id_perfil_usuario);

        if(!empty($ultimaSugestaoPerfil['id_area'])){
            $Segmento = new Segmento();
            $combosegmentos = $Segmento->combo(array("a.codigo = ?" => $ultimaSugestaoPerfil['id_area']), array('s.segmento ASC'));
        }

        $this->view->combosegmentos = !empty($combosegmentos) ? $combosegmentos : [];
        $this->view->ultimaSugestaoPerfil = $ultimaSugestaoPerfil;
        
//        $this->view->historicoEnquadramento = $this->obterHistoricoSugestaoEnquadramento($preprojeto['idPreProjeto']);
    }

    public function salvarSugestaoEnquadramento(array $dadosSugetaoEnquadramento)
    {
        try {

            $sugestaoEnquadramentoDbTable = new Admissibilidade_Model_DbTable_SugestaoEnquadramento();
            $sugestaoEnquadramentoDbTable->salvarSugestaoEnquadramento($dadosSugetaoEnquadramento);

            parent::message(
                'Enquadramento armazenado com sucesso!',
                "/admissibilidade/admissibilidade/exibirpropostacultural?idPreProjeto={$dadosSugetaoEnquadramento['id_preprojeto']}&realizar_analise=sim",
                'CONFIRM'
            );
        } catch (Exception $objException) {
            parent::message(
                $objException->getMessage(),
                "/admissibilidade/enquadramento-proposta/sugerir-enquadramento?id_preprojeto={$dadosSugetaoEnquadramento['id_preprojeto']}"
            );
        }
    }

    public function obterHistoricoSugestaoEnquadramentoAjaxAction()
    {
        try {
            $this->_helper->layout->disableLayout();

            $sugestaoEnquadramentoDbTable = new Admissibilidade_Model_DbTable_SugestaoEnquadramento();

            $get = $this->getRequest()->getParams();
            if (!isset($get['id_preprojeto']) || empty($get['id_preprojeto'])) {
                throw new Exception("Identificador da proposta não informado.");
            }
            $sugestaoEnquadramentoDbTable->sugestaoEnquadramento->setIdPreprojeto($get['id_preprojeto']);
            $resultado = $sugestaoEnquadramentoDbTable->obterHistoricoEnquadramento();

            $resultado = array_map(function ($dado) {
                return array_map('utf8_encode', $dado);
            }, $resultado);

            $this->_helper->json(
                ['sugestoes_enquadramento' => $resultado]
            );
//xd($resultado);
        } catch (Exception $objException) {
            xd($objException->getMessage());
        }
    }

    public function tratarAvaliacoesVencidasComponentesComissaoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $configuracoesAplicacao = Zend_Registry::get("config")->toArray();

        if (!isset($configuracoesAplicacao['cronJobs'])
            || !isset($configuracoesAplicacao['cronJobs']['proponente'])
            || !isset($configuracoesAplicacao['cronJobs']['proponente']['avaliacaoProposta'])
            || !isset($configuracoesAplicacao['cronJobs']['proponente']['avaliacaoProposta']['hash'])
            || empty($configuracoesAplicacao['cronJobs']['proponente']['avaliacaoProposta']['hash'])
        ) {
            throw new Exception("Defini&ccedil;&otilde;es de CronJob n&atilde;o definidas.");
        }

        $hashArquivoConfiguracao = $configuracoesAplicacao['cronJobs']['proponente']['avaliacaoProposta']['hash'];
        $get = Zend_Registry::get('get');
        if (!isset($get->hash) || empty($get->hash)) {
            throw new Exception("Hash de autoriza&ccedil;&atilde;o n&atilde;o informado");
        }
        if ($hashArquivoConfiguracao != $get->hash) {
            throw new Exception("Hash de autoriza&ccedil;&atilde;o n&atilde;o coincide com o da aplica&ccedil;&atilde;o.");
        }

        $distribuicaoAvaliacaoPropostaDbTable = new Admissibilidade_Model_DbTable_DistribuicaoAvaliacaoProposta();
        $distribuicaoAvaliacaoPropostaDbTable->setDistribuicaoAvaliacaoProposta(['id_perfil' => Autenticacao_Model_Grupos::COMPONENTE_COMISSAO]);

        $avaliacoesVencidas = $distribuicaoAvaliacaoPropostaDbTable->obterAvaliacoesVencidas(5);

        $sugestaoEnquadramentoDbTable = new Admissibilidade_Model_DbTable_SugestaoEnquadramento();
        $distribuicaoAvaliacaoPropostaDbTable = new Admissibilidade_Model_DbTable_DistribuicaoAvaliacaoProposta();

        foreach ($avaliacoesVencidas as $avaliacaoVencida) {
            $ultimaSugestaoAtiva = $sugestaoEnquadramentoDbTable->obterSugestaoAtiva($avaliacaoVencida->id_preprojeto);

            unset($ultimaSugestaoAtiva['id_sugestao_enquadramento']);
            $ultimaSugestaoAtiva['descricao_motivacao'] = "A avalia&ccedil;&atilde;o foi automaticamente encaminhada para o Coordenador Geral concordando tacitamente com o enquadramento do Coordenador de Admissibilidade.";
            $ultimaSugestaoAtiva['id_distribuicao_avaliacao_proposta'] = $avaliacaoVencida->id_distribuicao_avaliacao_prop;
            unset($ultimaSugestaoAtiva['id_distribuicao_avaliacao_prop']);
            $sugestaoEnquadramentoDbTable->inativarSugestoes($avaliacaoVencida->id_preprojeto);
            $sugestaoEnquadramentoDbTable->inserir($ultimaSugestaoAtiva);

            unset($avaliacaoVencida->dias_corridos_distribuicao);
            unset($avaliacaoVencida->id_distribuicao_avaliacao_prop);
            $avaliacaoVencida->id_perfil = Autenticacao_Model_Grupos::COORDENADOR_GERAL_ADMISSIBILIDADE;
            $avaliacaoVencida->data_distribuicao = $distribuicaoAvaliacaoPropostaDbTable->getExpressionDate();
            $distribuicaoAvaliacaoPropostaDbTable->inativarAvaliacoesProposta($avaliacaoVencida->id_preprojeto);
            $distribuicaoAvaliacaoPropostaDbTable->inserir((array)$avaliacaoVencida);
        }
    }

    private function _recuperarUltimaSugestaoPerfil($idPreProjeto, $idPerfilUsuario)
    {
        $sugestaoEnquadramento = new Admissibilidade_Model_DbTable_SugestaoEnquadramento;

        $ultimaSugestaoPerfil = [];
        if($this->view->id_perfil_usuario){
            $sugestaoEnquadramento->sugestaoEnquadramento->setIdPreprojeto($idPreProjeto);
            $sugestaoEnquadramento->sugestaoEnquadramento->setIdPerfilUsuario($idPerfilUsuario);
            $ultimaSugestaoPerfil = $sugestaoEnquadramento->obterHistoricoEnquadramento();
            $ultimaSugestaoPerfil = count($ultimaSugestaoPerfil) >= 1 ? current($ultimaSugestaoPerfil) : $ultimaSugestaoPerfil;

            if(empty($ultimaSugestaoPerfil['id_area'])){
                $planoDistribuicao = (new Proposta_Model_DbTable_PlanoDistribuicaoProduto())->buscar(['stPrincipal = ?'=>1, 'idProjeto = ?' => $idPreProjeto]);
                $ultimaSugestaoPerfil['id_area'] = !empty($planoDistribuicao[0]['Area']) ? $planoDistribuicao[0]['Area'] : null;
                $ultimaSugestaoPerfil['id_segmento'] = !empty($planoDistribuicao[0]['Segmento']) ? $planoDistribuicao[0]['Segmento'] : null;
            }
        }

        return $sugestaoEnquadramento->createRow($ultimaSugestaoPerfil)->toArray();
    }

}
