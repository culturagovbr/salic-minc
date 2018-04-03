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
            $post = $this->getRequest()->getPost();
            if (!$post['descricao_motivacao']) {
                $this->carregardadosEnquadramentoProposta($preprojeto);
            } else {
                $this->salvarSugestaoEnquadramento();
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
//        $this->view->historicoEnquadramento = $this->obterHistoricoSugestaoEnquadramento($preprojeto['idPreProjeto']);
    }

    private function salvarSugestaoEnquadramento()
    {
        try {
            $post = $this->getRequest()->getPost();
            $descricao_motivacao = trim($post['descricao_motivacao']);
            if (empty($descricao_motivacao)) {
                throw new Exception("O campo 'Parecer de Enquadramento' é de preenchimento obrigatório.");
            }
            $get = $this->getRequest()->getParams();

            $this->view->id_perfil_usuario = $this->grupoAtivo->codGrupo;
            $this->view->id_orgao = $this->grupoAtivo->codOrgao;
            $this->view->id_usuario_avaliador = $this->auth->getIdentity()->usu_codigo;

            $id_area = ($post['id_area']) ? $post['id_area'] : null;
            $id_segmento = ($post['id_segmento']) ? $post['id_segmento'] : null;
            $sugestaoEnquadramentoDbTable = new Admissibilidade_Model_DbTable_SugestaoEnquadramento();

            $orgaoDbTable = new Orgaos();
            $resultadoOrgaoSuperior = $orgaoDbTable->codigoOrgaoSuperior($this->grupoAtivo->codOrgao);
            $orgaoSuperior = $resultadoOrgaoSuperior[0]['Superior'];

            $distribuicaoAvaliacaoPropostaDtTable = new Admissibilidade_Model_DbTable_DistribuicaoAvaliacaoProposta();
            $distribuicaoAvaliacaoProposta = $distribuicaoAvaliacaoPropostaDtTable->findBy([
                'id_preprojeto' => $get['id_preprojeto'],
                'id_orgao_superior' => $orgaoSuperior,
                'id_perfil' => $this->grupoAtivo->codGrupo
            ]);

            if (!$distribuicaoAvaliacaoProposta &&
                (
                    $this->grupoAtivo->codGrupo != Autenticacao_Model_Grupos::TECNICO_ADMISSIBILIDADE
                    && $this->grupoAtivo->codGrupo != Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE
                )
            ) {
                throw new Exception("Distribui&ccedil;&atilde;o n&atilde;o localizada para o perfil atual.");
            }

            $dadosNovaSugestaoEnquadramento = [
                'id_orgao' => $this->grupoAtivo->codOrgao,
                'id_preprojeto' => $get['id_preprojeto'],
                'id_orgao_superior' => $orgaoSuperior,
                'id_perfil_usuario' => $this->grupoAtivo->codGrupo,
                'id_usuario_avaliador' => $this->auth->getIdentity()->usu_codigo,
                'id_area' => $id_area,
                'id_segmento' => $id_segmento,
                'descricao_motivacao' => $descricao_motivacao,
                'data_avaliacao' => $sugestaoEnquadramentoDbTable->getExpressionDate(),
                'ultima_sugestao' => Admissibilidade_Model_DbTable_SugestaoEnquadramento::ULTIMA_SUGESTAO_ATIVA,
            ];

            $dadosBuscaPorSugestao = $sugestaoEnquadramentoDbTable->findBy(
                [
                    'id_orgao' => $this->grupoAtivo->codOrgao,
                    'id_preprojeto' => $get['id_preprojeto'],
                    'id_orgao_superior' => $orgaoSuperior,
                    'id_perfil_usuario' => $this->grupoAtivo->codGrupo,
                    'id_usuario_avaliador' => $this->auth->getIdentity()->usu_codigo
                ]
            );


            if ($distribuicaoAvaliacaoProposta && $distribuicaoAvaliacaoProposta['id_distribuicao_avaliacao_prop']) {
                $dadosNovaSugestaoEnquadramento['id_distribuicao_avaliacao_proposta'] = $distribuicaoAvaliacaoProposta['id_distribuicao_avaliacao_prop'];
            }
            if (count($dadosBuscaPorSugestao) < 1) {
                $sugestaoEnquadramentoDbTable->inativarSugestoes($get['id_preprojeto']);
                $sugestaoEnquadramentoDbTable->inserir($dadosNovaSugestaoEnquadramento);
            } else {
                $dadosBuscaPorSugestao['id_distribuicao_avaliacao_proposta'] = $distribuicaoAvaliacaoProposta['id_distribuicao_avaliacao_prop'];
                $sugestaoEnquadramentoDbTable->update($dadosNovaSugestaoEnquadramento, [
                    'id_sugestao_enquadramento = ?' => $dadosBuscaPorSugestao['id_sugestao_enquadramento']
                ]);
            }

            parent::message('Enquadramento armazenado com sucesso!', "/admissibilidade/admissibilidade/exibirpropostacultural?idPreProjeto={$get['id_preprojeto']}&realizar_analise=sim", 'CONFIRM');
        } catch (Exception $objException) {
            parent::message($objException->getMessage(), "/admissibilidade/enquadramento-proposta/sugerir-enquadramento?id_preprojeto={$get['id_preprojeto']}");
        }
    }

    public function obterHistoricoSugestaoEnquadramentoAjaxAction()
    {
        try {
            $this->_helper->layout->disableLayout();

            $sugestaoEnquadramentoModel = new Admissibilidade_Model_DbTable_SugestaoEnquadramento();

            $get = $this->getRequest()->getParams();
            if (!isset($get['id_preprojeto']) || empty($get['id_preprojeto'])) {
                throw new Exception("Identificador da proposta não informado.");
            }

            $resultado = $sugestaoEnquadramentoModel->obterHistoricoEnquadramento(
                $get['id_preprojeto']
            );

            $resultado = array_map(function($dado) {
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
        $distribuicaoAvaliacaoProposta = new Admissibilidade_Model_DistribuicaoAvaliacaoProposta();
        $distribuicaoAvaliacaoProposta->setIdPerfil(Autenticacao_Model_Grupos::COMPONENTE_COMISSAO);
        $distribuicaoAvaliacaoPropostaDbTable->setDistribuicaoAvaliacaoProposta($distribuicaoAvaliacaoProposta);

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
}
