<?php

namespace Application\Modules\Readequacao\Service\Readequacao;

use MinC\Servico\IServicoRestZend;
use \Application\Modules\Documento\Service\Documento\Documento as DocumentoService;
use \Application\Modules\Readequacao\Service\Assinatura\ReadequacaoAssinatura as ReadequacaoAssinaturaService;

class Readequacao implements IServicoRestZend
{
    /**
     * @var \Zend_Controller_Request_Abstract $request
     */
    private $request;

    /**
     * @var \Zend_Controller_Response_Abstract $response
     */
    private $response;

    function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function buscar($idReadequacao)
    {
        $modelTbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
        
        $result = $modelTbReadequacao->buscarReadequacao($idReadequacao)[0];

        $return = [
            'idReadequacao' => $result['idReadequacao'],
            'idPronac' => $result['idPronac'],
            'idTipoReadequacao' => $result['idTipoReadequacao'],
            'dtSolicitacao' => $result['dsSolicitacao'],
            'idSolicitante' => $result['idSolicitante'],
            'dsJustificativa' => $result['dsJustificativa'],
            'dsSolicitacao' => $result['dsSolicitacao'],
            'idDocumento' => $result['idDocumento'],
            'idAvaliador' => $result['idAvaliador'],
            'dtAvaliador' => $result['dtAvaliador'],
            'dsAvaliacao' => $result['dsAvaliacao'],
            'stAtendimento' => $result['stAtendimento'],
            'siEncaminhamento' => $result['siEncaminhamento'],
            'stAnalise' => $result['stAnalise'],
            'idNrReuniao' => $result['idNrReuniao'],
            'stEstado' => $result['stEstado'],
        ];
        $where = [
            'idReadequacao' => $idReadequacao
        ];
        $readequacao = $modelTbReadequacao->findBy($where);

        $modelTbTipoReadequacao = new \Readequacao_Model_DbTable_TbTipoReadequacao();
        if ($readequacao['idTipoReadequacao']) {
            
            $where = [
                'idTipoReadequacao = ?' => $readequacao['idTipoReadequacao']
            ];
            $result = $modelTbTipoReadequacao->buscar($where);
            if (count($result) > 0) {
                $readequacao['dsTipoReadequacao'] = $result[0]['dsReadequacao'];
            }
        }
        return $readequacao;
    }

    private function __disponivelFinalizarSolicitacao($dadosReadequacao)
    {
        $minChar = [
            'solicitacao' => 3,
            'justificativa' => 10,
        ];
        
        $valido = [
            'solicitacao' => false,
            'justificativa' => false,
        ];
        
        if (strlen($dadosReadequacao['dsJustificativa']) > $minChar['justificativa']) {
            $valido['justificativa'] = true;
        }
        
        if ((int) $dadosReadequacao['idTipoReadequacao'] === \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PERIODO_EXECUCAO) {
            $campoAtual = $this->buscarCampoAtual($dadosReadequacao['idPronac'], $dadosReadequacao['idTipoReadequacao']);
            
            if (preg_match('/\//', $campoAtual[0]['dsCampo'])) {
                $dataOriginal = preg_replace('/\//', '-', $campoAtual[0]['dsCampo']);
            }
            if (preg_match('/\//', $dadosReadequacao['dsSolicitacao'])) {
                $dataReadequada = preg_replace('/\//', '-', $dadosReadequacao['dsSolicitacao']);
            }
            if (strtotime($dataReadequada) > strtotime($dataOriginal)) {
                return true;
            }
        } else {
            if (strlen($dadosReadequacao['dsSolicitacao']) > $minChar['solicitacao']) {
                $valido['solicitacao'] = true;
            }
        }
        return $valido['solicitacao'] && $valido['justificativa'];
    }
    
    public function buscarReadequacoes($idPronac, $idTipoReadequacao = '', $stStatusAtual = '')
    {
        $modelTbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
        $where = [
            'tbReadequacao.idPronac = ?' => $idPronac
        ];

        if ($idTipoReadequacao != '') {
            $where['tbReadequacao.idTipoReadequacao = ?'] = $idTipoReadequacao;
        }

        switch ($stStatusAtual) {
            case 'proponente':
                $where['tbReadequacao.siEncaminhamento IN (?)'] = [
                    \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_CADASTRADA_PROPONENTE,
                    \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_NAO_ENVIA_MINC
                ];
                $where['tbReadequacao.stEstado = ?'] = \Readequacao_Model_DbTable_TbReadequacao::ST_ESTADO_EM_ANDAMENTO;
                break;
            case 'em_analise':
                $where['tbReadequacao.siEncaminhamento NOT IN (?)'] = [
                    \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_CADASTRADA_PROPONENTE,
                    \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_NAO_ENVIA_MINC
                ];
                $where['tbReadequacao.stEstado = ?'] = \Readequacao_Model_DbTable_TbReadequacao::ST_ESTADO_EM_ANDAMENTO;
                break;
            case 'finalizadas':
                $where['tbReadequacao.stEstado = ?'] = \Readequacao_Model_DbTable_TbReadequacao::ST_ESTADO_FINALIZADO;
                break;

            default:
                break;
        }

        $result = $modelTbReadequacao->buscarReadequacoes($where)->toArray();

        $resultArray = [];
        if (!empty($result)) {
            foreach($result as $item) {
                $item['isDisponivelFinalizar'] = $this->__disponivelFinalizarSolicitacao($item);
                $item['dsTipoReadequacao'] = utf8_encode($item['dsTipoReadequacao']);
                $item['dsSolicitacao'] = utf8_encode($item['dsSolicitacao']);
                $item['dsJustificativa'] = utf8_encode($item['dsJustificativa']);
                $item['dsNomeSolicitante'] = utf8_encode($item['dsNomeSolicitante']);
                $item['dsNomeAvaliador'] = utf8_encode($item['dsNomeAvaliador']);
                $item['stStatusAtual'] = $stStatusAtual;
                $resultArray[] = $item;
            }
        }
        return $resultArray;
    }

    private function __buscarPaineisCoordenadorParecer($idOrgao)
    {
        $parametros = $this->request->getParams();
        $result = ''; 
        $filtro = $parametros['filtro'];
        if (!$filtro) {
            return;
        }

        $where = [];
        $where['idUnidade = ?'] = $idOrgao;
        $tbDistribuirReadequacao = new \Readequacao_Model_tbDistribuirReadequacao();
        
        switch ($filtro) {
            case 'painel_aguardando_distribuicao':
                $result = $tbDistribuirReadequacao->buscarReadequacaoCoordenadorParecerAguardandoAnalise($where);
                break;
            case 'em_analise':
                $result = $tbDistribuirReadequacao->buscarReadequacaoCoordenadorParecerEmAnalise($where);
                break;
            case 'analisados':
                $result = $tbDistribuirReadequacao->buscarReadequacaoCoordenadorParecerAnalisados($where);
                break;
        }
        return $result;
    }
    
    private function __buscarPaineisCoordenador($idOrgao)
    {
        $grupoAtivo = new \Zend_Session_Namespace('GrupoAtivo');
        $idPerfil = $grupoAtivo->codGrupo;
        
        $parametros = $this->request->getParams();
        $result = ''; 
        $filtro = $parametros['filtro'];
        if (!$filtro) {
            return;
        }
        
        if ($filtro == 'painel_aguardando_distribuicao') {
            $where['Orgao = ?'] = $idOrgao;
        } else {
            $where['projetos.Orgao = ?'] = $idOrgao;
        }
        
        if ($parametros['pronac']) {
            $where['a.PRONAC = ?'] = $parametros['pronac'];
        }

        if ($filtro == 'analisados') {
            if (in_array($idPerfil, [\Autenticacao_Model_Grupos::PRESIDENTE_DE_VINCULADA])) {
                unset($where['projetos.Orgao = ?']);
                $where['tbDistribuirReadequacao.idUnidade = ?'] = $idOrgao;
            } else if (in_array($idPerfil, [\Autenticacao_Model_Grupos::DIRETOR_DEPARTAMENTO])) {
                unset($where['projetos.Orgao = ?']);
                $where["CASE 
                WHEN projetos.Orgao IN (" . \Orgaos::ORGAO_GEAR_SACAV . "," . \Orgaos::ORGAO_SUPERIOR_SEFIC . "," . \Orgaos::SEFIC_DEIPC .")
                THEN " . \Orgaos::SEFIC_DEIPC . "
                WHEN projetos.Orgao IN (" . \Orgaos::ORGAO_SUPERIOR_SAV . "," . \Orgaos::ORGAO_SAV_CAP . "," . \Orgaos::ORGAO_SAV_SAL . "," . \Orgaos::ORGAO_SAV_CEP . "," . \Orgaos::ORGAO_SAV_DAP . "," . \Orgaos::ORGAO_SAV_DIECI . ")
                THEN " . \Orgaos::SAV_DPAV . "
                ELSE projetos.Orgao
                END = ?"] = $idOrgao;
            } else {
                unset($where['projetos.Orgao = ?']);
                $where["CASE 
	      WHEN projetos.Orgao in (" . \Orgaos::ORGAO_SUPERIOR_SAV . "," . \Orgaos::ORGAO_SAV_SAL . "," . \Orgaos::SAV_DPAV . ")
		   THEN " . \Orgaos::ORGAO_SAV_CAP . "
	     WHEN projetos.Orgao in (" . \Orgaos::ORGAO_SUPERIOR_SEFIC . "," . \Orgaos::SEFIC_DEIPC .")
		   THEN " . \Orgaos::ORGAO_GEAR_SACAV . "
                    ELSE projetos.Orgao
                    END = ?"] = $idOrgao;
            }
            if ($parametros['pronac']) {
                unset($where['a.PRONAC = ?']);
                $where[new \Zend_Db_Expr('projetos.anoprojeto + projetos.sequencial') . ' = ?'] = $parametros['pronac'];
            }
        }

        $modelTbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
        $result = $modelTbReadequacao->painelReadequacoesCoordenadorAcompanhamento($where, null, null, null, null, $filtro);
        return $result;
    }

    public function buscarReadequacoesPainel()
    {
        $parametros = $this->request->getParams();
        $where = [];
        $grupoAtivo = new \Zend_Session_Namespace('GrupoAtivo');
        $auth = \Zend_Auth::getInstance();
        
        $idOrgao = $grupoAtivo->codOrgao;
        $idPerfil = $grupoAtivo->codGrupo;
        
        if ($parametros['pronac']) {
            $where['projetos.AnoProjeto+projetos.Sequencial = ?'] = $parametros['pronac'];
        }
        
        switch ($idPerfil) {
            case \Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO:
                if ($idOrgao == \Orgaos::ORGAO_GEAR_SACAV) {
                    $idOrgao = \Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI;
                }
                $where['idUnidade = ?'] = $idOrgao;
                $modelTbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
                $where['dtDistribuicao.idAvaliador = ?'] = $auth->getIdentity()->usu_codigo;
                $result = $modelTbReadequacao->painelReadequacoesTecnicoAcompanhamento($where);
                break;
            case \Autenticacao_Model_Grupos::PARECERISTA:
                $where['idUnidade = ?'] = $idOrgao;
                $modelTbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
                $where['dtDistribuicao.idAvaliador = ?'] = $auth->getIdentity()->usu_codigo;
                $result = $modelTbReadequacao->painelReadequacoesTecnicoAcompanhamento($where);
                break;
            case \Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER:
                $result = $this->__buscarPaineisCoordenadorParecer($idOrgao);
                break;
            case \Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO:
                $result = $this->__buscarPaineisCoordenador($idOrgao);
                break;
            case \Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO:
                $result = $this->__buscarPaineisCoordenador($idOrgao);
                break;
            case \Autenticacao_Model_Grupos::DIRETOR_DEPARTAMENTO:
                $result = $this->__buscarPaineisCoordenador($idOrgao);
                break;
            case \Autenticacao_Model_Grupos::PRESIDENTE_DE_VINCULADA:
                $result = $this->__buscarPaineisCoordenador($idOrgao);
                break;
            case \Autenticacao_Model_Grupos::SECRETARIO:
                $result = $this->__buscarPaineisCoordenador($idOrgao);
                break;
        }
        
        $resultArray = [];
        if (!empty($result)) {
            foreach($result as $item) {
                $item->tpReadequacao = utf8_encode($item->tpReadequacao);
                $item->NomeProjeto = utf8_encode($item->NomeProjeto);
                $item->dsTipoReadequacao = utf8_encode($item->dsTipoReadequacao);
                $item->dsSolicitacao = utf8_encode($item->dsSolicitacao);
                $item->dsJustificativa = utf8_encode($item->dsJustificativa);
                $item->dsNomeSolicitante = utf8_encode($item->dsNomeSolicitante);
                $item->dsNomeAvaliador = utf8_encode($item->dsNomeAvaliador);
                if ($item->dsEncaminhamento) {
                    $item->dsEncaminhamento = utf8_encode($item->dsEncaminhamento);
                }
                if ($item->nmTecnicoParecerista) {
                    $item->nmTecnicoParecerista = utf8_encode($item->nmTecnicoParecerista);
                }
                if ($item->nmReceptor) {
                    $item->nmReceptor = utf8_encode($item->nmReceptor);
                }
                if ($item->nmParecerista) {
                    $item->nmParecerista = utf8_encode($item->nmParecerista);
                    $item->nmTecnicoParecerista = $item->nmParecerista;
                }
                if ($item->dsTipoReadequacao == '') {
                    $item->dsTipoReadequacao = $item->tpReadequacao;
                }
                if ($item->qtDiasAguardandoDistribuicao != '') {
                    $item->qtAguardandoDistribuicao = $item->qtDiasAguardandoDistribuicao;
                }
                if ($item->dtDistribuicao != '') {
                    $item->dtEncaminhamento = $item->dtDistribuicao;
                }
                if ($item->qtDiasEmAnalise != '') {
                    $item->qtDiasEncaminhar = $item->qtDiasEmAnalise;
                }
                if ($item->idPRONAC != '') {
                    $item->idPronac = $item->idPRONAC;
                }
                if ($item->sgUnidade == '') {
                    $tbOrgaos = new \Orgaos();
                    $orgao = $tbOrgaos->pesquisarUnidades(['Codigo = ?' => $item->idOrgao]);
                    $item->sgUnidade = $orgao[0]->Sigla;
                }

                if (in_array($idPerfil, [\Autenticacao_Model_Grupos::PARECERISTA, \Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO])) {
                    $item->stDiligencia = $this->definirStatusDiligencia($item);
                    $item->diasEmDiligencia = $this->obterTempoDiligencia($item);
                    $item->diasEmAvaliacao = $this->obterTempoRestanteDeAvaliacao($item);
                }
                
                $resultArray[] = $item;
            }
        }
        
        return $resultArray;
    }

    private function definirStatusDiligencia($item)
    {
        $diligencia = 0;
        if ($item->DtSolicitacao && $item->DtResposta == NULL) {
            $diligencia = 1;
        } else if ($item->DtSolicitacao && $item->DtResposta != NULL) {
            $diligencia = 2;
        } else if ($item->DtSolicitacao
            && round(\data::CompararDatas($item->DtDistribuicao)) > $item->tempoFimDiligencia) {
            $diligencia = 3;
        }

        return $diligencia;
    }

    private function obterTempoRestanteDeAvaliacao($item)
    {
        switch ($item->stDiligencia) {
            case 1:
                $tempoRestante = round(\data::CompararDatas($item->dtDistribuicao, $item->DtSolicitacao));
                break;
            case 2:
            case 3:
                $tempoRestante = round(\data::CompararDatas($item->DtResposta));
                break;
            default:
                $tempoRestante = round(\data::CompararDatas($item->dtDistribuicao));
                break;
        }

        return $tempoRestante;
    }

    private function obterTempoDiligencia($item)
    {
        $tempoDiligencia = 0;
        if ($item->stDiligencia == 1) {
            $tempoDiligencia = round(\data::CompararDatas($item->DtSolicitacao));
        }

        return $tempoDiligencia;
    }
    
    public function buscarReadequacoesPorPronacTipo($idPronac, $idTipoReadequacao)
    {
        $modelTbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
        $where = [
            'idPronac' => $idPronac,
            'idTipoReadequacao' => $idTipoReadequacao,
        ];

        return $modelTbReadequacao->findBy($where);
    }

    public function buscarReadequacaoDocumento($idReadequacao, $idDocumento)
    {
        return [];
    }

    public function buscarIdPronac($idReadequacao)
    {
        $modelTbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
        $where = [
            'idReadequacao' => $idReadequacao
        ];
        return $modelTbReadequacao->findBy($where);
    }

    public function buscarTiposDisponiveis($idPronac)
    {
        $tbTipoReadequacao = new \Readequacao_Model_DbTable_TbTipoReadequacao();
        $tiposDisponiveis = $tbTipoReadequacao->buscarTiposReadequacoesPermitidos($idPronac, 1)->toArray();

        $resultArray = [];
        foreach ($tiposDisponiveis as $item) {
            $itemOk = [];
            $itemOk['idTipoReadequacao'] = $item['idTipoReadequacao'];
            $itemOk['descricao'] = $item['dsReadequacao'];
            $resultArray[] = $itemOk;
        }
        $resultArray = \TratarArray::utf8EncodeArray($resultArray);

        return $resultArray;
    }

    public function buscarCampoAtual($idPronac, $idTipoReadequacao)
    {
        $valorPreCarregado = null;

        switch($idTipoReadequacao) {
            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_REMANEJAMENTO_PARCIAL:
                $descricao = 'Remanejamento 50%';
                $tpCampo = 'remanejamento_50';
                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA:
                $descricao = 'Planilha orçamentária';
                $tpCampo = 'planilha';
                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_RAZAO_SOCIAL:
                $Projetos = new \Projetos();
                $dadosProjeto = $Projetos->buscarDadosUC75($idPronac)->current();

                if ($dadosProjeto) {
                    $valorPreCarregado = $dadosProjeto->Proponente;
                }
                $descricao = 'Razão social';
                $tpCampo = 'input';
                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_AGENCIA_BANCARIA:
                $Projetos = new \Projetos();
                $dadosProjeto = $Projetos->buscar(
                    ['IdPRONAC = ?' => $idPronac]
                )->current();

                if ($dadosProjeto) {
                    $ContaBancaria = new \ContaBancaria();
                    $dadosBancarios = $ContaBancaria->contaPorProjeto($idPronac);
                    if (count($dadosBancarios) > 0) {
                        $valorPreCarregado = $dadosBancarios->Agencia;
                    }
                }
                $descricao = 'Agência bancária';
                $tpCampo = 'input';
                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SINOPSE_OBRA:
                $Projetos = new \Projetos();
                $dadosProjeto = $Projetos->buscar(
                    ['IdPRONAC = ?' => $idPronac]
                )->current();

                if ($dadosProjeto) {
                    $PreProjeto = new \Proposta_Model_DbTable_PreProjeto();
                    $dadosPreProjeto = $PreProjeto->buscar(
                        ['idPreProjeto = ?' => $dadosProjeto->idProjeto]
                    )->current();

                    if ($dadosPreProjeto) {
                        $valorPreCarregado = $dadosPreProjeto->Sinopse;
                    }
                }
                $descricao = 'Sinopse';
                $tpCampo = 'textarea';
                break;

        case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_IMPACTO_AMBIENTAL:
                $Projetos = new \Projetos();
                $dadosProjeto = $Projetos->buscar(
                    ['IdPRONAC = ?' => $idPronac]
                )->current();

                if ($dadosProjeto) {
                    $PreProjeto = new \Proposta_Model_DbTable_PreProjeto();
                    $dadosPreProjeto = $PreProjeto->buscar(
                        ['idPreProjeto = ?' => $dadosProjeto->idProjeto]
                    )->current();

                    if ($dadosPreProjeto) {
                        $valorPreCarregado = $dadosPreProjeto->ImpactoAmbiental;
                    }
                }
                $descricao = 'Impacto ambiental';
                $tpCampo = 'textarea';
                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ESPECIFICACAO_TECNICA:
                $Projetos = new \Projetos();
                $dadosProjeto = $Projetos->buscar(
                    ['IdPRONAC = ?' => $idPronac]
                )->current();

                if ($dadosProjeto) {
                    $PreProjeto = new \Proposta_Model_DbTable_PreProjeto();
                    $dadosPreProjeto = $PreProjeto->buscar(
                        ['idPreProjeto = ?' => $dadosProjeto->idProjeto]
                    )->current();

                    if ($dadosPreProjeto) {
                        $valorPreCarregado = $dadosPreProjeto->EspecificacaoTecnica;
                    }
                }
                $descricao = 'Especificação técnica';
                $tpCampo = 'textarea';
                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ESTRATEGIA_EXECUCAO:
                $Projetos = new \Projetos();
                $dadosProjeto = $Projetos->buscar(
                    ['IdPRONAC = ?' => $idPronac]
                )->current();

                if ($dadosProjeto) {
                    $PreProjeto = new \Proposta_Model_DbTable_PreProjeto();
                    $dadosPreProjeto = $PreProjeto->buscar(
                        ['idPreProjeto = ?' => $dadosProjeto->idProjeto]
                    )->current();

                    if ($dadosPreProjeto) {
                        $valorPreCarregado = $dadosPreProjeto->EstrategiadeExecucao;
                    }
                }
                $descricao = 'Estratégia de execução';
                $tpCampo = 'textarea';
                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_LOCAL_REALIZACAO:
                $descricao = 'Local de realização';
                $tpCampo = 'local_realizacao';
                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ALTERACAO_PROPONENTE:
                $Projetos = new \Projetos();
                $dadosProjeto = $Projetos->buscar(
                    ['IdPRONAC = ?' => $idPronac]
                )->current();

                if ($dadosProjeto) {
                    $valorPreCarregado = $dadosProjeto->CgcCpf;
                }
                $descricao = 'Alteração do proponente';
                $tpCampo = 'input';

                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DISTRIBUICAO:
                $descricao = 'Plano de distribuição';
                $tpCampo = 'plano_distribuicao';
                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_NOME_PROJETO:
                $Projetos = new \Projetos();
                $dadosProjeto = $Projetos->buscar(
                    ['IdPRONAC = ?' => $idPronac]
                )->current();

                if ($dadosProjeto) {
                    $valorPreCarregado = $dadosProjeto->NomeProjeto;
                }
                $descricao = 'Nome do projeto';
                $tpCampo = 'input';

                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PERIODO_EXECUCAO:
                $Projetos = new \Projetos();
                $dadosProjeto = $Projetos->buscar(
                    ['IdPRONAC = ?' => $idPronac]
                )->current();

                $DtFimExecucao = \Data::tratarDataZend($dadosProjeto->DtFimExecucao, 'brasileira');

                if ($dadosProjeto) {
                    $valorPreCarregado = $DtFimExecucao;
                }
                $descricao = 'Período de execução';
                $tpCampo = 'date';
                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DIVULGACAO:
                $descricao = 'Plano de divulgação';
                $tpCampo = 'plano_divulgacao';
                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_RESUMO_PROJETO:
                $Projetos = new \Projetos();
                $dadosProjeto = $Projetos->buscar(
                    ['IdPRONAC = ?' => $idPronac]
                )->current();

                if ($dadosProjeto) {
                    $valorPreCarregado = $dadosProjeto->ResumoProjeto;
                }
                $descricao = 'Resumo do projeto';
                $tpCampo = 'textarea';
                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_OBJETIVOS:
                $Projetos = new \Projetos();
                $dadosProjeto = $Projetos->buscar(
                    ['IdPRONAC = ?' => $idPronac]
                )->current();

                if ($dadosProjeto) {
                    $PreProjeto = new \Proposta_Model_DbTable_PreProjeto();
                    $dadosPreProjeto = $PreProjeto->buscar(
                        ['idPreProjeto = ?' => $dadosProjeto->idProjeto]
                    )->current();

                    if ($dadosPreProjeto) {
                        $valorPreCarregado = $dadosPreProjeto->Objetivos;
                    }
                }
                $descricao = 'Objetivos';
                $tpCampo = 'textarea';
                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_JUSTIFICATIVA:
                $Projetos = new \Projetos();
                $dadosProjeto = $Projetos->buscar(
                    ['IdPRONAC = ?' => $idPronac]
                )->current();

                if ($dadosProjeto) {
                    $PreProjeto = new \Proposta_Model_DbTable_PreProjeto();
                    $dadosPreProjeto = $PreProjeto->buscar(
                        ['idPreProjeto = ?' => $dadosProjeto->idProjeto]
                    )->current();

                    if ($dadosPreProjeto) {
                        $valorPreCarregado = $dadosPreProjeto->Justificativa;
                    }
                }
                $descricao = 'Justificativa';
                $tpCampo = 'textarea';
                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ACESSIBILIDADE:
                $Projetos = new \Projetos();
                $dadosProjeto = $Projetos->buscar(
                    ['IdPRONAC = ?' => $idPronac]
                )->current();

                if ($dadosProjeto) {
                    $PreProjeto = new \Proposta_Model_DbTable_PreProjeto();
                    $dadosPreProjeto = $PreProjeto->buscar(
                        ['idPreProjeto = ?' => $dadosProjeto->idProjeto]
                    )->current();

                    if ($dadosPreProjeto) {
                        $valorPreCarregado = $dadosPreProjeto->Acessibilidade;
                    }
                }
                $descricao = 'Acessibilidade';
                $tpCampo = 'textarea';
                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_DEMOCRATIZACAO_ACESSO:
                $Projetos = new \Projetos();
                $dadosProjeto = $Projetos->buscar(
                    ['IdPRONAC = ?' => $idPronac]
                )->current();

                if ($dadosProjeto) {
                    $PreProjeto = new \Proposta_Model_DbTable_PreProjeto();
                    $dadosPreProjeto = $PreProjeto->buscar(
                        ['idPreProjeto = ?' => $dadosProjeto->idProjeto]
                    )->current();

                    if ($dadosPreProjeto) {
                        $valorPreCarregado = $dadosPreProjeto->DemocratizacaoDeAcesso;
                    }
                }
                $descricao = 'Democratização do acesos';
                $tpCampo = 'textarea';
                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ETAPAS_TRABALHO:
                $Projetos = new \Projetos();
                $dadosProjeto = $Projetos->buscar(
                    ['IdPRONAC = ?' => $idPronac]
                )->current();

                if ($dadosProjeto) {
                    $PreProjeto = new \Proposta_Model_DbTable_PreProjeto();
                    $dadosPreProjeto = $PreProjeto->buscar(
                        ['idPreProjeto = ?' => $dadosProjeto->idProjeto]
                    )->current();

                    if ($dadosPreProjeto) {
                        $valorPreCarregado = $dadosPreProjeto->EtapaDeTrabalho;
                    }
                }
                $descricao = 'Etapas de trabalho';
                $tpCampo = 'textarea';
                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_FICHA_TECNICA:
                $Projetos = new \Projetos();
                $dadosProjeto = $Projetos->buscar(
                    ['IdPRONAC = ?' => $idPronac]
                )->current();

                if ($dadosProjeto) {
                    $PreProjeto = new \Proposta_Model_DbTable_PreProjeto();
                    $dadosPreProjeto = $PreProjeto->buscar(
                        ['idPreProjeto = ?' => $dadosProjeto->idProjeto]
                    )->current();

                    if ($dadosPreProjeto) {
                        $valorPreCarregado = $dadosPreProjeto->FichaTecnica;
                    }
                }
                $descricao = 'Ficha técnica';
                $tpCampo = 'textarea';
                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SALDO_APLICACAO:
                $descricao = 'Saldo de aplicação';
                $tpCampo = 'saldo_aplicacao';
                break;

            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_TRANSFERENCIA_RECURSOS:
                $descricao = 'Transferência de recursos';
                $tpCampo = 'transferencia_recursos';
                break;

            default:
                $descricao = 'Tipo não encontrado';
                $tpCampo = 'N/A';
        }

        $resultArray = [];
        $resultArray[] = [
            'idTipoReadequacao' => $idTipoReadequacao,
            'descricao' => $descricao,
            'tpCampo' => $tpCampo,
            'dsCampo' => utf8_encode($this->converteTextoEmHtml($valorPreCarregado)),
        ];
        
        return $resultArray;
    }

    public function salvar()
    {
        $parametros = $this->request->getParams();

        if (isset($parametros['idReadequacao'])){
            $idReadequacao = $parametros['idReadequacao'];
            $readequacao = $this->buscar($idReadequacao);
            
            $documento = new DocumentoService(
                $this->request,
                $this->response
            );

            if (($readequacao['idDocumento'] != '' && isset($_FILES['documento']))
                || $readequacao['idDocumento'] != '' && $parametros['idDocumento'] == '' && !isset($_FILES['documento'])
            ) {
                $excluir = $documento->excluir($readequacao['idDocumento']);
                if (!$excluir) {
                    $errorMessage = "Não foi possível remover o idDocumento {$readequacao['idDocumento']}!";
                    throw new \Exception($errorMessage);
                }
                $parametros['idDocumento'] = 0;
            }
            if (!empty($_FILES['documento'])) {
                $metadata = [
                    'idTipoDocumento' => \Documento_Model_DbTable_tbTipoDocumento::TIPO_DOCUMENTO_READEQUACAO,
                    'dsDocumento' => 'Solicita&ccedil;&atilde;o de Readequa&ccedil;&atilde;o',
                    'nmTitulo' => 'Readequa&ccedil;&atilde;o'
                ];
                
                $parametros['idDocumento'] = $documento->inserir(
                    $_FILES['documento'],
                    'pdf',
                    $metadata
                );
                if (!$parametros['idDocumento']){
                    $errorMessage = "Não foi possível inserir o documento!";
                    throw new \Exception($errorMessage);
                }
            }
        }

        $parametros = $this->__prepararDadosGravacao($parametros);
        
        $mapper = new \Readequacao_Model_TbReadequacaoMapper();
        $idReadequacao = $mapper->salvarSolicitacaoReadequacao($parametros);

        $result = $this->buscar($idReadequacao);
        $result = \TratarArray::utf8EncodeArray($result);

        return $result;
    }

    public function converteTextoEmHtml($texto)
    {
        $list = get_html_translation_table(HTML_ENTITIES);
        unset($list['"']);
        unset($list['\'']);
        unset($list['<']);
        unset($list['>']);
        unset($list['&']);
        
        $search = array_map('utf8_encode', $list);
        $values = array_values($list);
        $texto = str_replace($search, $values, $texto);
        
        $weirdChars = [
            '–' => '&ndash;',
            ';' => '&#894;',
            '’' => '&#8217;',
        ];
        foreach ($weirdChars as $char => $substitution) {
            $texto = str_replace($char, $substitution, $texto);
        }
        
        return $texto;
    }

    private function __prepararDadosGravacao($parametros)
    {
        if ($parametros['dsSolicitacao']) {
            if ($parametros['idTipoReadequacao'] == \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PERIODO_EXECUCAO) {
                if (strpos($parametros['dsSolicitacao'], '-')) {
                    $data = explode('-', $parametros['dsSolicitacao']);
                    $parametros['dsSolicitacao'] = implode('/', array_reverse($data));
                }
            } else if ($parametros['idTipoReadequacao'] == \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ALTERACAO_PROPONENTE) {
                $parametros['dsSolicitacao'] = preg_replace('/[^0-9]/', '', $parametros['dsSolicitacao']);
            } else if ($parametros['idTipoReadequacao'] == \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_AGENCIA_BANCARIA) {
                $parametros['dsSolicitacao'] = strtoupper($parametros['dsSolicitacao']);
                $parametros['dsSolicitacao'] = preg_replace('/[^0-9\-X]/', '', $parametros['dsSolicitacao']);
            } else {
                $parametros['dsSolicitacao'] = $this->converteTextoEmHtml($parametros['dsSolicitacao']);
            }
        }

        return $parametros;
    }

    public function remover()
    {
        $parametros = $this->request->getParams();
        if (isset($parametros['id'])){
            $idReadequacao = $parametros['id'];
            $readequacaoModel = new \Readequacao_Model_DbTable_TbReadequacao();
            $readequacao = $readequacaoModel->obterDadosReadequacao(
                \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SALDO_APLICACAO,
                '',
                $idReadequacao
            );
            
            if (!empty($readequacao['idDocumento'])) {
                $tbDocumento = new \tbDocumento();
                $tbDocumento->excluirDocumento($readequacao['idDocumento']);
            }
            
            switch($readequacao['idTipoReadequacao']) {
            case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_REMANEJAMENTO_PARCIAL:
                    $this->removerRemanejamentoParcial($readequacao);
                    break;
                case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA:
                    $this->removerPlanilhaOrcamentaria($readequacao);
                    break;
                case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DISTRIBUICAO:
                    $this->removerPlanoDistribuicao($readequacao);
                    break;
                case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SALDO_APLICACAO:
                    $this->removerSaldoAplicacao($readequacao);
                    break;
                case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_TRANSFERENCIA_RECURSOS:
                    $this->removerTransferenciaRecursos($readequacao);
                    break;
                default:
                    break;
            }
            $excluir = $readequacaoModel->delete(
                ['idReadequacao = ?' => $idReadequacao]
            );
            return $excluir;
        }
    }

    private function __removerItensPlanilha($idReadequacao)
    {
        $tbPlanilhaAprovacao = new \tbPlanilhaAprovacao();
        $planilha = $tbPlanilhaAprovacao->buscar([
            'idReadequacao = ?' => $idReadequacao,
        ]);
        $idsExcluir = [];
        foreach($planilha as $item) {
            $idsExcluir[] = $item['idPlanilhaAprovacao'];
        }
        $this->__doRemoveItemPlanilha($idsExcluir);
    }

    private function __doRemoveItemPlanilha($idsExcluir)
    {
        $chunkSize = 30;
        $idsInsert = array_splice($idsExcluir, 0, $chunkSize);
        $tbPlanilhaAprovacao = new \tbPlanilhaAprovacao();
        $tbPlanilhaAprovacao->delete([
            'idPlanilhaAprovacao IN (?)' => $idsInsert
        ]);
        if (!empty($idsExcluir)) {
            $this->__doRemoveItemPlanilha($idsExcluir);
        } else {
            return;
        }
    }

    public function removerRemanejamentoParcial($readequacao) {
        if (isset($readequacao['idReadequacao'])
            && $readequacao['idReadequacao'] > 0) {
            $this->__removerItensPlanilha($readequacao['idReadequacao']);
        }
    }
    
    public function removerPlanilhaOrcamentaria($readequacao) {
        if (isset($readequacao['idReadequacao'])
            && $readequacao['idReadequacao'] > 0) {
            $this->__removerItensPlanilha($readequacao['idReadequacao']);
        }
    }

    public function removerPlanoDistribuicao($readequacao) {
        $tbReadequacaoMapper = new \Readequacao_Model_TbPlanoDistribuicaoMapper();
        $tbReadequacaoMapper->excluirReadequacaoPlanoDistribuicaoAtiva($readequacao['idPronac']);
    }
    
    public function removerSaldoAplicacao($readequacao) {
        if (isset($readequacao['idReadequacao'])
            && $readequacao['idReadequacao'] > 0) {
            $this->__removerItensPlanilha($readequacao['idReadequacao']);
        }
    }
    
    public function removerTransferenciaRecursos($readequacaoModel) {
        
    }    
    
    public function finalizarSolicitacao()
    {
        $parametros = $this->request->getParams();
        $data = [];
        
        if (isset($parametros['idReadequacao'])
            && isset($parametros['idPronac'])
        ){
            $idPronac = $parametros['idPronac'];
            $idReadequacao = $parametros['idReadequacao'];

            $tbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
            $finalizar = $tbReadequacao->finalizarSolicitacao($idReadequacao);
            if (!$finalizar) {
                $data['error'] = true;
                $data['message'] = "Houve um erro e não foi possível finalizar a readequação.";
            }

            $data['message'] = "Readequação enviada para análise.";
        } else {
            $data['message'] = "É preciso especificar idPronac e idReadequação para finalizar uma readequação.";
            $data['erro'] = true;
        }
        return $data;
    }

    public function buscarDocumento($idReadequacao, $idDocumento) {
        $data = [];
        
        $readequacao = $this->buscar($idReadequacao);
        if ($readequacao['idDocumento'] == $idDocumento) {
            $documento = new DocumentoService(
                $this->request,
                $this->response
            );
            $data = $documento->abrirDocumento($idDocumento);
        }
        return $data;
    }

    private function __verificarPermissaoProponenteNoProjeto($idPronac)
    {
        $parametros = $this->request->getParams();

        $auth = \Zend_Auth::getInstance()->getIdentity();
        $arrAuth = array_change_key_case((array)$auth);
        
        $idUsuarioLogado = $arrAuth['idusuario'];
        $fnVerificarPermissao = new \Autenticacao_Model_FnVerificarPermissao();
        
        if ($idPronac == '') {
            $idPronac = $parametros['idpronac'] ? $parametros['idpronac'] : $parametros['idPronac'];
        }
        if ($idPronac == '') {
            $idReadequacao = $parametros['id'];
            $dados = $this->buscarIdPronac($idReadequacao);
            $idPronac = $dados['idPronac'];
        }
        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }
        $consulta = $fnVerificarPermissao->verificarPermissaoProjeto($idPronac, $idUsuarioLogado);
        
        return $consulta->Permissao;
    }

    public function verificarPermissaoNoProjeto($idPronac = '') {
        $parametros = $this->request->getParams();

        $permissao = false;
        $auth = \Zend_Auth::getInstance()->getIdentity();
        $arrAuth = array_change_key_case((array)$auth);
        
        if (!isset($arrAuth['usu_codigo'])) {
            $permissao = $this->__verificarPermissaoProponenteNoProjeto($idPronac);
        } else {
            $permissao = true;
        }
        return $permissao;
    }

    public function buscarAvaliacao($idReadequacao = '')
    {
        $parametros = $this->request->getParams();
        $data = [];

        $tbReadequacaoXParecer = new \Readequacao_Model_DbTable_TbReadequacaoXParecer();
        $result = $tbReadequacaoXParecer->buscarParecerReadequacao($idReadequacao);
        
        $data = [
            'idParecer' => $result['IdParecer'],
            'ParecerFavoravel' => $result['ParecerFavoravel'],
            'ParecerDeConteudo' => $result['ResumoParecer'],
            'DtParecer' => $result['DtParecer'],
        ];
        
        return $data;
    }

    public function salvarAvaliacao($idReadequacao = '')
    {
        $parametros = $this->request->getParams();
        $data = [];

        $tbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
        $auth = \Zend_Auth::getInstance()->getIdentity();
        $arrAuth = array_change_key_case((array)$auth);
        
        $idPronac = $parametros['idPronac'];
        $idTipoReadequacao = $parametros['idTipoReadequacao'];
        $idReadequacao = $parametros['idReadequacao'];
        $parecerFavoravel = $parametros['ParecerFavoravel'];
        $parecerDeConteudo = $parametros['ParecerDeConteudo'];
        $campoTipoParecer = 8;
        $vlPlanilha = 0;
        $idUsuarioLogado = $arrAuth['usu_codigo'];
        
        $enquadramentoDAO = new \Admissibilidade_Model_Enquadramento();
        $buscaEnquadramento = $enquadramentoDAO->buscarDados(
            $idPronac,
            null,
            false
        );

        $projetos = new \Projetos();
        $dadosProjeto = $projetos->buscar([
            'IdPRONAC = ?' => $idPronac
        ]);
            
        if (count($dadosProjeto) < 1) {
            throw new \Exception("Projeto Cultural n&atilde;o encontrado.");
        }
            
        $parecerDAO = new \Parecer_Model_DbTable_Parecer();
        $dadosParecer = [
            'idPRONAC' => $idPronac,
            'AnoProjeto' => $dadosProjeto[0]->AnoProjeto,
            'Sequencial' => $dadosProjeto[0]->Sequencial,
            'TipoParecer' => $campoTipoParecer,
            'ParecerFavoravel' => $parecerFavoravel,
            'DtParecer' => \MinC_Db_Expr::date(),
            'NumeroReuniao' => null,
            'ResumoParecer' => utf8_decode($parecerDeConteudo),
            'SugeridoReal' => $vlPlanilha,
            'Atendimento' => 'S',
            'idEnquadramento' => $buscaEnquadramento['IdEnquadramento'],
            'stAtivo' => 1,
            'idTipoAgente' => 1,
            'Logon' => $idUsuarioLogado
        ];
            
        $parecerAntigo = [
            'Atendimento' => 'S',
            'stAtivo' => 0
        ];

        $tbReadequacaoXParecer = new \Readequacao_Model_DbTable_TbReadequacaoXParecer();
        $parecerAlterado = $tbReadequacaoXParecer->buscarPareceresReadequacao([
            'a.idReadequacao = ?' => $idReadequacao
        ])->current();

        if (!empty($readequacaoXParecer)) {
            $whereUpdateParecer = 'idParecer = ' . $parecerAlterado->IdParecer;
            $alteraParecer = $parecerDAO->alterar($parecerAntigo, $whereUpdateParecer);
        }
            
        if ($parecerAlterado) {
            $whereUpdateParecer = 'IdParecer = ' . $parecerAlterado->IdParecer;
            $parecerDAO->alterar($dadosParecer, $whereUpdateParecer);
            $idParecer = $parecerAlterado->IdParecer;
        } else {
            $idParecer = $parecerDAO->inserir($dadosParecer);
        }

        $parecerReadequacao = $tbReadequacaoXParecer->buscar([
            'idReadequacao = ?' => $idReadequacao,
            'idParecer =?' => $idParecer
        ]);
        if (count($parecerReadequacao) == 0) {
            $dadosInclusao = [
                'idReadequacao' => $idReadequacao,
                'idParecer' => $idParecer
            ];
            $tbReadequacaoXParecer->inserir($dadosInclusao);
        }

        $tbReadequacaoXParecerDbTable = new \Readequacao_Model_DbTable_TbReadequacaoXParecer();
        $possuiDocumentoAssinatura = $tbReadequacaoXParecerDbTable->possuiDocumentoAssinatura($idReadequacao);
        
        if (count($possuiDocumentoAssinatura) > 0) {
            $this->__invalidarAssinatura($idReadequacao);
        }
        
        $data = [
            'idParecer' => $idParecer,
            'ParecerFavoravel' => $parecerFavoravel,
            'ParecerDeConteudo' => $parecerDeConteudo,
        ];
        return $data;
    }

    public function finalizarAvaliacao()
    {
        $parametros = $this->request->getParams();
        $data = [];
        
        $idPronac = $parametros['idPronac'];
        $idParecer = $parametros['idParecer'];
        $idTipoReadequacao = $parametros['idTipoReadequacao'];
        
        $auth = \Zend_Auth::getInstance();
        $grupoAtivo = new \Zend_Session_Namespace('GrupoAtivo');
        
        $servicoReadequacaoAssinatura = new ReadequacaoAssinaturaService(
            $grupoAtivo,
            $auth
        );
        $idTipoDoAto = $servicoReadequacaoAssinatura->obterAtoAdministrativoPorTipoReadequacao($idTipoReadequacao);
        
        $servicoDocumentoAssinatura = new \Application\Modules\Readequacao\Service\Assinatura\DocumentoAssinatura(
            $idPronac,
            $idTipoDoAto,
            $idParecer
        );
        $idDocumentoAssinatura = $servicoDocumentoAssinatura->iniciarFluxo();
        
        $data = [
            'idDocumentoAssinatura' => $idDocumentoAssinatura,
        ];
        
        return $data;
    }

    public function solicitarSaldo($idPronac) {
        $data = [];
        
        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }
        
        $tbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
        $readequacao = $tbReadequacao->obterDadosReadequacao(
            \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SALDO_APLICACAO,
            $idPronac
        );
        
        if (empty($readequacao)) {
            $idReadequacao = $tbReadequacao->criarReadequacaoPlanilha(
                $idPronac,
                \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SALDO_APLICACAO
            );
            
            $readequacao = $tbReadequacao->obterDadosReadequacao(
                \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SALDO_APLICACAO,
                $idPronac,
                $idReadequacao
            );
            
            $tbPlanilhaAprovacao = new \tbPlanilhaAprovacao();
            $verificarPlanilhaReadequadaAtual = $tbPlanilhaAprovacao->buscarPlanilhaReadequadaEmEdicao($idPronac, $idReadequacao);
            
            if (count($verificarPlanilhaReadequadaAtual) == 0) {
                $planilhaAtiva = $tbPlanilhaAprovacao->buscarPlanilhaAtiva($idPronac);
                $criarPlanilha = $tbPlanilhaAprovacao->copiarPlanilhas($idPronac, $idReadequacao);
                
                if ($criarPlanilha) {
                    $data = $readequacao;
                } else {
                    $data = [
                        'msg' => utf8_decode('Houve um erro ao criar a solicitação de uso de saldo de aplicação.'),
                        'success' => 'false',
                        'readequacao' => []
                    ];
                }
            }
        } else {
            $data = [
                'msg' => utf8_decode('Já existe uma solicitação de uso de saldo de aplicação.'),
                'success' => 'false',
                'readequacao' => $readequacao
            ];
        }
        return $data;
    }

    private function __obterPlanilhaAtiva($idPronac)
    {
        $tbPlanilhaAprovacao = new \tbPlanilhaAprovacao();
        $planilhaOrcamentaria = $tbPlanilhaAprovacao->obterPlanilhaAtiva($idPronac);

        $planilha = [];
        foreach ($planilhaOrcamentaria as $item) {
            if ($item->idPlanilhaAprovacaoPai != null) {
                $planilha[$item->idPlanilhaAprovacaoPai] = $item;
            } else {
                $planilha[] = $item;
            }
        };
        
        return $planilha;
    }

    private function __obterPlanilhaEmReadequacao($idPronac, $tipoPlanilha)
    {
        $spPlanilhaOrcamentaria = new \spPlanilhaOrcamentaria();
        $params = [
            'link' => true,
        ];
        $tbPlanilhaAprovacao = new \tbPlanilhaAprovacao();

        $planilhaReadequada = $spPlanilhaOrcamentaria->exec($idPronac, $tipoPlanilha, $params);
        $planilhaAtiva = $tbPlanilhaAprovacao->obterPlanilhaAtiva($idPronac);
        
        $planilha = [];
        foreach ($planilhaReadequada as $item) {
            if ($item->idPlanilhaAprovacaoPai != null) {
                $planilha[$item->idPlanilhaAprovacaoPai] = $item;
            } else {
                $planilha[] = $item;
            }
        }

        foreach ($planilhaAtiva as $itemAtiva) {
            if ($itemAtiva->tpAcao == 'E') {
                continue;
            }

            if (array_key_exists($itemAtiva->idPlanilhaAprovacao, $planilha)) {
                $planilha[$itemAtiva->idPlanilhaAprovacao]->idUnidadeAtivo = $itemAtiva->idUnidade;
                $planilha[$itemAtiva->idPlanilhaAprovacao]->OcorrenciaAtivo = $itemAtiva->Ocorrencia;
                $planilha[$itemAtiva->idPlanilhaAprovacao]->QuantidadeAtivo = $itemAtiva->Quantidade;
                $planilha[$itemAtiva->idPlanilhaAprovacao]->QtdeDiasAtivo = $itemAtiva->QtdeDias;
                $planilha[$itemAtiva->idPlanilhaAprovacao]->vlUnitarioAtivo = $itemAtiva->vlUnitario;
            } else {
                $planilha[] = $itemAtiva;
            }
        }
        
        return $planilha;
    }
    
    public function obterPlanilha() {
        $parametros = $this->request->getParams();

        $idPronac = $parametros['idPronac'];
        $idTipoReadequacao = $parametros['idTipoReadequacao'];

        $tipos = [
            \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_REMANEJAMENTO_PARCIAL => \spPlanilhaOrcamentaria::TIPO_PLANILHA_REMANEJAMENTO,
            \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA => \spPlanilhaOrcamentaria::TIPO_PLANILHA_READEQUACAO,
            \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SALDO_APLICACAO => \spPlanilhaOrcamentaria::TIPO_PLANILHA_SALDO_APLICACAO
        ];

//        $tipoPlanilha = ($idTipoReadequacao) ? $tipoPlanilha[$idTipoReadequacao] : \spPlanilhaOrcamentaria::TIPO_PLANILHA_APROVADA_ATIVA;
        $planilhaOrcamentariaAtiva = [];
        
        if ($idTipoReadequacao) {
            $planilha = $this->__obterPlanilhaEmReadequacao($idPronac, $tipos[$idTipoReadequacao]);
        } else {
            $planilha = $this->__obterPlanilhaAtiva($idPronac);
        }

        $result = [];
        foreach ($planilha as $item) {
            $item->Produto = utf8_encode($item->Produto);
            $item->NomeProjeto = utf8_encode($item->NomeProjeto);
            $item->Etapa = utf8_encode($item->Etapa);
            $item->Municipio = utf8_encode($item->Municipio);
            $item->Item = utf8_encode($item->Item);
            $item->dsJustificativa = utf8_encode($item->dsJustificativa);
            $item->FonteRecurso = utf8_encode($item->FonteRecurso);
            $item->Unidade = utf8_encode($item->Unidade);
            $item->vlComprovado = (!is_null($item->vlComprovado)) ? $item->vlComprovado : 0;
            if ($item->JustProponente != '') {
                $item->JustProponente = utf8_encode($item->JustProponente);
            }
            if ($item->JustParecerista != '') {
                $item->JustParecerista = utf8_encode($item->JustParecerista);
            }
            if ($item->JustComponente != '') {
                $item->JustComponente = utf8_encode($item->JustComponente);
            }
            $result[] = $item;
        }
        return $result;
    }

    public function obterUnidadesPlanilha() {
        $TbPlanilhaUnidade = new \Proposta_Model_DbTable_TbPlanilhaUnidade();
        $unidades = $TbPlanilhaUnidade->buscarUnidade();

        $unidadesOut = [];
        foreach ($unidades as $unidade) {
            $unidadeObj = new \StdClass();
            $unidadeObj->idUnidade = $unidade->idUnidade;
            $unidadeObj->Sigla = utf8_encode($unidade->Sigla);
            $unidadeObj->Descricao = utf8_encode($unidade->Descricao);
            $unidadesOut[] = $unidadeObj;
        }

        return $unidadesOut;
    }

    public function alterarItemPlanilha() {
        $parametros = $this->request->getParams();
        
        $idPronac = $parametros['idPronac'];
        $idPlanilhaAprovacao = $parametros['idPlanilhaAprovacao'];
        $idReadequacao = $parametros['idReadequacao'];
        $grupoAtivo = new \Zend_Session_Namespace('GrupoAtivo');
        $idPerfil = $grupoAtivo->codGrupo;

        if ($parametros['idTipoReadequacao']) {
            $idTipoReadequacao = $parametros['idTipoReadequacao'];
        } else {
            $tbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
            $readequacao = $tbReadequacao->buscar([
                'idReadequacao = ?' => $idReadequacao
            ])->current();
            $idTipoReadequacao = $readequacao->idTipoReadequacao;
        }
        
        $auth = \Zend_Auth::getInstance();
        $cpf = isset($auth->getIdentity()->Cpf) ? $auth->getIdentity()->Cpf : $auth->getIdentity()->usu_identificacao;

        $tblAgente = new \Agente_Model_DbTable_Agentes();
        $rsAgente = $tblAgente->buscar(['CNPJCPF = ?' => $cpf]);
        $idAgente = 0;
        if ($rsAgente->count() > 0) {
            $idAgente = $rsAgente[0]->idAgente;
        }
        
        $tbPlanilhaAprovacao = new \tbPlanilhaAprovacao();
        $editarItem = $tbPlanilhaAprovacao->buscar(
            [
                'IdPRONAC=?' => $idPronac,
                'idPlanilhaAprovacao=?' => $idPlanilhaAprovacao
            ])->current();


        $qtd = substr_count($parametros['ValorUnitario'], '.');
        $valorUnitario = preg_replace(
            '/\./',
            '',
            $parametros['ValorUnitario'],
            $qtd - 1
        );
        
        $editarItem->idUnidade = $parametros['idUnidade'];
        $editarItem->qtItem = $parametros['Quantidade'];
        $editarItem->nrOcorrencia = $parametros['Ocorrencia'];
        $editarItem->vlUnitario = $parametros['ValorUnitario'];
        $editarItem->qtDias = $parametros['QtdeDias'];
        $editarItem->nrFonteRecurso = $parametros['idFonte'];
        $editarItem->dsJustificativa = utf8_decode($parametros['dsJustificativa']);
        $editarItem->idAgente = $idAgente;
        
        if ($editarItem->tpAcao == 'N') {
            $editarItem->tpAcao = 'A';
        }

        if (in_array($idPerfil, [
            \Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO,
            \Autenticacao_Model_Grupos::PARECERISTA,
        ])) {
            $tbReadequacaoXParecerDbTable = new \Readequacao_Model_DbTable_TbReadequacaoXParecer();
            $possuiDocumentoAssinatura = $tbReadequacaoXParecerDbTable->possuiDocumentoAssinatura($idReadequacao);
            
            if (count($possuiDocumentoAssinatura) > 0) {
                $this->__invalidarAssinatura($idReadequacao);
            }
            
            $editarItem->stCustoPraticado = 0;
        }
        
        $editarItem->save();
        
        $projetosDbTable = new \Projeto_Model_DbTable_Projetos();
        if ($projetosDbTable->possuiCalculoAutomaticoCustosVinculados($idPronac)
            && $idTipoReadequacao == \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA
        ) {
            $atualizarCustosVinculados = $this->atualizarCustosVinculados(
                $idPronac,
                $idReadequacao
            );
            
            if ($atualizarCustosVinculados['erro']) {
                $this->reverterAlteracaoItem(
                    $idPronac,
                    $idReadequacao,
                    $editarItem->idPlanilhaItem
                );
                
                throw new \Exception($atualizarCustosVinculados['mensagem']);
            } else {
                $data = [
                    'message' => 'Item atualizado',
                    'success' => 'true',
                ];
            }
        } else {
            $data = [
                'msg' => 'Item atualizado',
                'success' => 'true',
            ];
        }
        return $data;
    }

    public function atualizarCustosVinculados(
        $idPronac,
        $idReadequacao
    ) {
        $retorno = [
            'mensagem' => 'Custos vinculados atualizados!',
            'erro' => false
        ];
        
        $tbPlanilhaAprovacao = new \tbPlanilhaAprovacao();
        $tipoReadequacao = $tbPlanilhaAprovacao->calculaSaldoReadequacaoBaseDeCusto($idPronac, $idReadequacao);
        
        if (in_array($tipoReadequacao, ['COMPLEMENTACAO', 'REDUCAO'])) {
            $propostaTbCustosVinculados = new \Proposta_Model_TbCustosVinculadosMapper();
            $custosVinculados = $propostaTbCustosVinculados->obterCustosVinculadosReadequacao($idPronac, $idReadequacao);
            foreach ($custosVinculados as $item) {
                $tbPlanilhaAprovacao = new \tbPlanilhaAprovacao();
                $editarItem = $tbPlanilhaAprovacao->buscar([
                    'idPronac = ?' => $idPronac,
                    'idPlanilhaItem = ?' => $item['idPlanilhaItens'],
                    'idReadequacao = ?' => $idReadequacao
                ])->current();

                if (!$editarItem) {
                    continue;
                }
                
                $comprovantePagamentoxxPlanilhaAprovacao = new \PrestacaoContas_Model_ComprovantePagamentoxPlanilhaAprovacao();
                
                $valorComprovado = $comprovantePagamentoxxPlanilhaAprovacao->valorComprovadoPorItem($idPronac, $item['idPlanilhaItens']);
                if ($valorComprovado > $item['valorUnitario']) {
                    
                    $retorno['mensagem'] = "Somente ser&aacute; permitido reduzir ou excluir itens or&ccedil;ament&aacute;rios caso tal a&ccedil;&atilde;o n&atilde;o afete negativamente os custos vinculados abaixo de valores j&aacute; comprovados.";
                    $retorno['erro'] = true;
                    return $retorno;
                }
                
                if ($itemOriginal->vlUnitario != $item['valorUnitario']) {
                    $editarItem->vlUnitario = $item['valorUnitario'];
                    $editarItem->tpAcao = 'A';
                    $editarItem->dsJustificativa = "Rec&aacute;lculo autom&aacute;tico com base no percentual solicitado pelo proponente ao enviar a proposta ao MinC.";

                    $editarItem->save();
                } else {
                    $editarItem->tpAcao = 'N';
                    $editarItem->save();
                }
            }
        } else if ($tipoReadequacao == 'REMANEJAMENTO') {
                $tbPlanilhaAprovacao = new \tbPlanilhaAprovacao();
                
                $itensOriginais = $tbPlanilhaAprovacao->buscar([
                    'idPronac = ?' => $idPronac,
                    'idEtapa IN (?)' => [
                        \PlanilhaEtapa::ETAPA_CUSTOS_VINCULADOS,
                        \PlanilhaEtapa::ETAPA_CAPTACAO_RECURSOS
                    ],
                    'stAtivo = ?' => 'S'
                ]);
                
                foreach ($itensOriginais as $itemOriginal) {
                    $editarItens = $tbPlanilhaAprovacao->buscar([
                        'idPronac = ?' => $idPronac,
                        'idPlanilhaItem = ?' => $itemOriginal['idPlanilhaItem'],
                        'idReadequacao = ?' => $idReadequacao
                    ]);
                    foreach ($editarItens as $editarItem) {
                        $editarItem->vlUnitario = $itemOriginal['vlUnitario'];
                        $editarItem->tpAcao = 'N';
                        $editarItem->save();
                    }
                }
        } else {
            $retorno['erro'] = true;
        }
        return $retorno;
    }    
    
    public function reverterAlteracaoItem(
        $idPronac = '',
        $idReadequacao = '',
        $idPlanilhaItem = '',
        $idPlanilhaAprovacao = '',
        $idPlanilhaAprovacaoPai = ''
    ) {
        $parametros = $this->request->getParams();
        $idPronac = ($idPronac) ? $idPronac : $parametros['idPronac'];
        $idReadequacao = ($idReadequacao) ? $idReadequacao : $parametros['idReadequacao'];
        $idPlanilhaItem = ($idPlanilhaItem) ? $idPlanilhaItem : $parametros['idPlanilhaItem'];
        $idPlanilhaAprovacao = ($idPlanilhaAprovacao) ? $idPlanilhaAprovacao : $parametros['idPlanilhaAprovacao'];
        $idPlanilhaAprovacaoPai = ($idPlanilhaAprovacaoPai) ? $idPlanilhaAprovacaoPai : $parametros['idPlanilhaAprovacaoPai'];
        
        $tbPlanilhaAprovacao = new \tbPlanilhaAprovacao();
        
        if ($idPlanilhaAprovacaoPai) {
            $itemOriginal = $tbPlanilhaAprovacao->buscar([
                'idPlanilhaAprovacao = ?' => $idPlanilhaAprovacaoPai,
            ])->current();
        } else {
            $itemOriginal = $tbPlanilhaAprovacao->buscar([
                'idPronac = ?' => $idPronac,
                'idPlanilhaItem = ?' => $idPlanilhaItem,
                'stAtivo = ?' => 'S'
            ])->current();
        }
        
        if ($idPlanilhaAprovacao) {
            $itemAlterado = $tbPlanilhaAprovacao->buscar([
                'idPlanilhaAprovacao = ?' => $idPlanilhaAprovacao,
            ])->current();
        } else {
            $itemAlterado = $tbPlanilhaAprovacao->buscar([
                'idPronac = ?' => $idPronac,
                'idPlanilhaItem = ?' => $idPlanilhaItem,
                'idReadequacao = ?' => $idReadequacao,
            ])->current();
        }
        
        if (!empty($itemAlterado)
            && !empty($itemOriginal)) {
            $itemAlterado->vlUnitario = $itemOriginal->vlUnitario;
            $itemAlterado->qtItem = $itemOriginal->qtItem;
            $itemAlterado->nrOcorrencia = $itemOriginal->nrOcorrencia;
            $itemAlterado->dsJustificativa = "";
            $itemAlterado->save();

            return ['message' => 'Dados do item revertidos!'];
        } else {
            $errorMessage = "Não foi possível reverter dados do item.";
            throw new \Exception($errorMessage);
        }
    }
    
    public function calcularResumoPlanilha()
    {
        $parametros = $this->request->getParams();
        
        $idPronac = $parametros['idPronac'];
        $idTipoReadequacao = $parametros['idTipoReadequacao'];

        $tbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
        $valorEntrePlanilhas = $tbReadequacao->carregarValorEntrePlanilhas(
            $idPronac,
            $idTipoReadequacao
        );

        if (empty($valorEntrePlanilhas)) {
            $errorMessage = "Não foi possível calcular a diferença entre planilhas.";
            throw new \Exception($errorMessage);
        }

        return $valorEntrePlanilhas;
    }

    private function __getVinculadasExcetoIphan()
    {
        return [
            \Orgaos::ORGAO_FUNARTE,
            \Orgaos::ORGAO_FBN,
            \Orgaos::ORGAO_FCP,
            \Orgaos::ORGAO_FCRB,
            \Orgaos::ORGAO_IBRAM,
        ];
    }
    
    public function buscarDestinatariosDistribuicao()
    {
        $parametros = $this->request->getParams();
        
        $vinculada = $parametros['vinculada'];
        $area = $parametros['area'];
        $segmento = $parametros['segmento'];
        
        $a = 0;
        $dadosUsuarios = [];
        
        if ($vinculada == \Orgaos::ORGAO_SAV_CAP || $vinculada == \Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI) {
            $dados = [];
            $dados['sis_codigo = ?'] = 21;
            $dados['uog_status = ?'] = 1;
            $dados['gru_codigo = ?'] = \Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO;
            if ($vinculada == \Orgaos::ORGAO_SAV_CAP) {
                $dados['org_superior = ?'] = \Orgaos::ORGAO_SUPERIOR_SAV;
            } else {
                $dados['org_superior = ?'] = \Orgaos::ORGAO_SUPERIOR_SEFIC;
            }

            $vw = new \vwUsuariosOrgaosGrupos();
            $result = $vw->buscar($dados, ['usu_nome']);
            
            if (count($result) > 0) {
                foreach ($result as $registro) {
                    $dadosUsuarios[$a]['id'] = $registro['usu_codigo'];
                    $dadosUsuarios[$a]['nome'] = utf8_encode($registro['usu_nome']);
                    $a++;
                }
            }
        } else if (in_array($vinculada, $this->__getVinculadasExcetoIphan()) || $vinculada == \Orgaos::ORGAO_SUPERIOR_SAV) {
            $agentesModel = new \Agente_Model_DbTable_Agentes();
            $result = $agentesModel->buscarPareceristas($vinculada, $area, $segmento);
            
            if ($result) {
                foreach ($result as $registro) {
                    $dadosUsuarios[$a]['id'] = $registro['id'];
                    $dadosUsuarios[$a]['nome'] = utf8_encode($registro['nome']);
                    $a++;
                }
            }
        }
        
        return $dadosUsuarios;
    }

    private function __invalidarAssinatura($idReadequacao)
    {
        $grupoAtivo = new \Zend_Session_Namespace('GrupoAtivo');
        $auth = \Zend_Auth::getInstance();
        
        $tbDocumentoAssinaturaDbTable = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $tbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
        $servicoReadequacaoAssinatura = new ReadequacaoAssinaturaService(
            $grupoAtivo,
            $auth
        );
        
        $readequacao = $tbReadequacao->buscarDadosReadequacoes(['idReadequacao = ?' => $idReadequacao])->current();
        $idTipoDoAto = $servicoReadequacaoAssinatura->obterAtoAdministrativoPorTipoReadequacao($readequacao['idTipoReadequacao']);

        if (!$idTipoDoAto) {
            $errorMessage = "Ato administrativo não encontrado!";
            throw new \Exception($errorMessage);
        }
        
        $documentoAssinatura = $tbDocumentoAssinaturaDbTable->obterDocumentoAssinatura(
            $readequacao['idPronac'],
            $idTipoDoAto
        );

        if (!$idTipoDoAto) {
            $errorMessage = "Documento assinatura não encontrado!";
            throw new \Exception($errorMessage);
        }
        
        $data = [
            'cdSituacao' => \Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_FECHADO_PARA_ASSINATURA,
            'stEstado' => \Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_INATIVO
        ];
        $where = [
            'idDocumentoAssinatura = ?' => $documentoAssinatura['idDocumentoAssinatura'],
        ];
        
        $tbDocumentoAssinaturaDbTable->update(
            $data,
            $where
        );
        
        return true;
    }

    public function distribuirReadequacao()
    {
        $parametros = $this->request->getParams();

        $auth = \Zend_Auth::getInstance();
        $idUsuario = $auth->getIdentity()->usu_codigo;
        
        $idReadequacao = $parametros['idReadequacao'];
        $idUnidade = $parametros['vinculada'];
        
        $tbReadequacaoModel = new \Readequacao_Model_DbTable_TbReadequacao();
        $readequacao = $tbReadequacaoModel->find(['idReadequacao = ?' => $idReadequacao])->current();
        $stValidacaoCoordenador = 0;
        $dataEnvio = null;
        if ($readequacao) {
            if ($parametros['stAtendimento']) {
                $readequacao->stAtendimento = $parametros['stAtendimento'];
            }
            $readequacao->dsAvaliacao = $parametros['dsAvaliacao'];
            $readequacao->dtAvaliador = new \Zend_Db_Expr('GETDATE()');
            $readequacao->idAvaliador = $idUsuario;
            
            if ($parametros['stAtendimento'] == \Readequacao_Model_DbTable_TbReadequacao::ST_ATENDIMENTO_INDEFERIDA) {
                $readequacao->siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_INDEFERIDA;
                $readequacao->stEstado = \Readequacao_Model_DbTable_TbReadequacao::ST_ESTADO_FINALIZADO;
                
            } elseif ($parametros['stAtendimento'] == \Readequacao_Model_DbTable_TbReadequacao::ST_ATENDIMENTO_DEVOLVIDA) {
                $readequacao->siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_CADASTRADA_PROPONENTE;
                $readequacao->stEstado = \Readequacao_Model_DbTable_TbReadequacao::ST_ESTADO_EM_ANDAMENTO;
                $readequacao->stAtendimento = \Readequacao_Model_DbTable_TbReadequacao::ST_ATENDIMENTO_DEVOLVIDA;
                
            } else {
                if ($parametros['vinculada'] == \Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI
                    || ($parametros['vinculada'] == \Orgaos::ORGAO_SAV_CAP && $parametros['destinatario'] > 0)) {
                    $readequacao->siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_ANALISE_TECNICA;
                    $dataEnvio = new \Zend_Db_Expr('GETDATE()');
                    $readequacao->idAvaliador = $parametros['destinatario'];
                } else if ($parametros['vinculada'] == \Orgaos::ORGAO_SAV_CAP && $parametros['destinatario'] == 0) {
                    $readequacao->siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_UNIDADE_ANALISE;
                    $idUnidade = \Orgaos::ORGAO_SUPERIOR_SAV;
                } else if (in_array($parametros['vinculada'], $this->__getVinculadasExcetoIphan())
                           || $parametros['vinculada'] == \Orgaos::ORGAO_SUPERIOR_SAV) {
                    if ($parametros['destinatario'] > 0) {
                        $readequacao->siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_ANALISE_TECNICA;
                    } else {
                        $readequacao->siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_UNIDADE_ANALISE;
                    }
                } else {
                     $readequacao->siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_UNIDADE_ANALISE;
                }
            }
            $update = $readequacao->save();
            if (!$update) {
                $errorMessage = "Erro ao distribuir a readequação: ao alterar readequação";
                throw new \Exception($errorMessage);
            }
            if ($parametros['stAtendimento'] == \Readequacao_Model_DbTable_TbReadequacao::ST_ATENDIMENTO_DEFERIDA
                || $parametros['stAtendimento'] == \Readequacao_Model_DbTable_TbReadequacao::ST_ATENDIMENTO_SEM_AVALIACAO 
            ) {
                $tbDistribuirReadequacao = new \Readequacao_Model_tbDistribuirReadequacao();
                $jaDistribuiu = $tbDistribuirReadequacao->buscar(['idReadequacao = ?' => $readequacao->idReadequacao])->current();
                $dtEnvioAvaliador = ($parametros['destinatario'] != null) ? new \Zend_Db_Expr('GETDATE()') : null;
                
                if (empty($jaDistribuiu)) {
                    $dados = [
                        'idReadequacao' => $readequacao->idReadequacao,
                        'idUnidade' => $idUnidade,
                        'DtEncaminhamento' => new \Zend_Db_Expr('GETDATE()'),
                        'idAvaliador' => (null !== $parametros['destinatario']) ? $parametros['destinatario'] : null,
                        'dtEnvioAvaliador' => $dtEnvioAvaliador,
                        'stValidacaoCoordenador' => $stValidacaoCoordenador,
                        'dsOrientacao' => $readequacao->dsAvaliacao
                    ];

                    $inserir = $tbDistribuirReadequacao->inserir($dados);
                    if (!$inserir) {
                        $errorMessage = "Erro ao distribuir a readequação: ao inserir distribuição!";
                        throw new \Exception($errorMessage);
                    }
                } else {
                    $dados = [
                        'idUnidade' => $parametros['vinculada'],
                        'DtEncaminhamento' => new \Zend_Db_Expr('GETDATE()'),
                        'idAvaliador' => (null !== $parametros['destinatario']) ? $parametros['destinatario'] : null,
                        'dtEnvioAvaliador' => $dtEnvioAvaliador,
                        'stValidacaoCoordenador' => $stValidacaoCoordenador,
                        'dsOrientacao' => $readequacao->dsAvaliacao
                    ];
                    $where = [];
                    $where['idReadequacao = ?'] = $readequacao->idReadequacao;
                    $update = $tbDistribuirReadequacao->update($dados, $where);
                    if (!$update) {
                        $errorMessage = "Erro ao distribuir a readequação: ao atualizar distribuição!";
                        throw new \Exception($errorMessage);
                    }
                }
            } else if ($parametros['stAtendimento'] == \Readequacao_Model_DbTable_TbReadequacao::ST_ATENDIMENTO_DEVOLVIDA) {
                $tbDistribuirReadequacao = new \Readequacao_Model_tbDistribuirReadequacao();
                $excluiDistribuicao = $tbDistribuirReadequacao->delete([
                    'idReadequacao = ?' => $readequacao->idReadequacao
                ]);
            }
        }
        return true;
    }
    
    public function devolverAoCoordenador()
    {
        $parametros = $this->request->getParams();

        $idReadequacao = $parametros['idReadequacao'];
        
        $tbReadequacaoModel = new \Readequacao_Model_DbTable_TbReadequacao();
        $readequacao = $tbReadequacaoModel->find(['idReadequacao = ?' => $idReadequacao])->current();

        if (count($readequacao) > 0) {
            $readequacao->siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_MINC;
            $readequacao->save();

            $dados = [
                'idAvaliador' => 0,
                'dsOrientacao' => $parametros['dsOrientacao'],
                'idUnidade' => 0,
            ];
            $where = [];
            $where['idReadequacao = ?'] = $idReadequacao;
            
            $tbDistribuirReadequacao = new \Readequacao_Model_tbDistribuirReadequacao();
            
            $excluiDistribuicao = $tbDistribuirReadequacao->update($dados, $where);
            
            if (!$excluiDistribuicao) {
                $errorMessage = "Erro ao devolver a readequação ao coordenador de acompanhamento!";
                throw new \Exception($errorMessage);
            }
        } else {
            $errorMessage = "Readequação inexistente, não foi possível devolver ao coordenador de acompanhamento!";
            throw new \Exception($errorMessage);
        }
    }
    
    public function redistribuirReadequacao()
    {
        $parametros = $this->request->getParams();
        
        $idReadequacao = $parametros['idReadequacao'];
        $dsOrientacao = $parametros['dsOrientacao'];
        $stValidacaoCoordenador = 0;
        $dataEnvio = null;
        $destinatario = $parametros['destinatario'];
        $idUnidade = $parametros['vinculada'];
        
        $this->__invalidarAssinatura($idReadequacao);
        
        try {
            if (in_array($idUnidade, [
                \Orgaos::ORGAO_SAV_CAP,
                \Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI]
            )) {
                $dados = [
                    'idAvaliador' => $destinatario,
                    'DtEnvioAvaliador' => new \Zend_Db_Expr('GETDATE()'),
                    'dsOrientacao' => $dsOrientacao,
                    'idUnidade' => $idUnidade
                ];
                $tbDistribuirReadequacao = new \Readequacao_Model_tbDistribuirReadequacao();
                $jaDistribuiu = $tbDistribuirReadequacao->buscar(['idReadequacao = ?' => $readequacao->idReadequacao])->current();
                
                if (empty($jaDistribuiu)) {
                    $tbDistribuirReadequacao->inserir($dados);
                } else {
                    $where = [];
                    $where['idReadequacao = ?'] = $idReadequacao;
                    $u = $tbDistribuirReadequacao->update($dados, $where);
                }
                
                $tbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
                $dados = [
                    'siEncaminhamento' => \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_ANALISE_TECNICA
                ];
                $where = [];
                $where['idReadequacao = ?'] = $idReadequacao;
                $tbReadequacao->update($dados, $where);
                
            } else {
                $dtEnvioAvaliador = ($destinatario != '') ? new \Zend_Db_Expr('GETDATE()') : null;
                
                $tbDistribuirReadequacao = new \Readequacao_Model_tbDistribuirReadequacao();
                $dados = [
                    'idUnidade' => $idUnidade,
                    'DtEncaminhamento' => new \Zend_Db_Expr('GETDATE()'),
                    'idAvaliador' => $destinatario,
                    'DtEnvioAvaliador' => $dtEnvioAvaliador,
                    'dsOrientacao' => $dsOrientacao
                ];
                $where = [];
                $where['idReadequacao = ?'] = $idReadequacao;
                
                $tbDistribuirReadequacao->update($dados, $where);

                $tbReadequacaoModel = new \Readequacao_Model_DbTable_TbReadequacao();
                if ($destinatario > 0) {
                    $dadosReadequacao = [
                        'siEncaminhamento' => \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_ANALISE_TECNICA
                    ];
                } else {
                    $dadosReadequacao = [
                        'siEncaminhamento' => \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_UNIDADE_ANALISE
                    ];
                }
                $u = $tbReadequacaoModel->update($dadosReadequacao, $where);
            }
        } catch (Exception $e) {
            return false;
        }
        
        return true;
    }

    public function devolverReadequacao()
    {
        $parametros = $this->request->getParams();
        $idReadequacao = $parametros['idReadequacao'];

        $this->__invalidarAssinatura($idReadequacao);
        $this->distribuirReadequacao();
        
        return true;
    }

    public function declararImpedimento()
    {
        $parametros = $this->request->getParams();
        
        $tbReadequacaoModel = new \Readequacao_Model_DbTable_TbReadequacao();
        $dadosReadequacao = [
            'siEncaminhamento' => \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_UNIDADE_ANALISE,
            'dsAvaliacao' => $parametros['dsOrientacao'],
        ];
        $where = [];
        $where['idReadequacao = ?'] = $parametros['idReadequacao'];
        $updateReadequacao = $tbReadequacaoModel->update($dadosReadequacao, $where);

        if (!$updateReadequacao) {
            $errorMessage = "Erro ao declarar impedimento!";
            throw new \Exception($errorMessage);
        }
        
        $dados = [];
        $dados['idAvaliador'] = 0;
        $dados['DtEnvioAvaliador'] = null;
        
        $tbDistribuirReadequacao = new \Readequacao_Model_tbDistribuirReadequacao();
        $remover = $tbDistribuirReadequacao->update($dados, $where);

        if (!$remover) {
            $errorMessage = "Erro ao declarar impedimento - erro ao remover distribuição!";
            throw new \Exception($errorMessage);
        }
        
        return true;
    }

    public function finalizarCicloAnalise()
    {
        $parametros = $this->request->getParams();

        $grupoAtivo = new \Zend_Session_Namespace('GrupoAtivo');
        $auth = \Zend_Auth::getInstance();
        
        $servicoReadequacaoAssinatura = new ReadequacaoAssinaturaService(
            $grupoAtivo,
            $auth
        );
        
        $retorno = $servicoReadequacaoAssinatura->encaminharOuFinalizarReadequacaoChecklist(
            $parametros['idReadequacao']
        );
        
        if (!$retorno) {
            $errorMessage = "Erro ao finalizar ciclo de análise da readequação!";
            throw new \Exception($errorMessage);
        }
        
        $data['message'] = "Ciclo de avaliação de readequação finalizado.";
        
        return $data;
    }

    public function prazoRespostaDiligencia($idPronac = null, $idTipoDiligencia = null, $idDiligencia = null, $blnPrazoPadrao=false, $blnPrazoResposta=false)
    {
        if (!isset($idPronac) || empty($idPronac)) {
            return;
        }
        
        $arrRetorno = [];
        $arrRetorno['prazoPadrao']   = null;
        $arrRetorno['prazoRespostaCrescente'] = null;
        $arrRetorno['prazoRespostaDecrescente'] = null;
        $arrRetorno['tipoDiligencia'] = "a_diligenciar";
        
        $tbDiligencia = new \tbDiligencia();
        
        $arrBusca = [];
        $arrBusca['IdPRONAC = ?'] = $idPronac;
        if (!empty($idTipoDiligencia)) {
            $arrBusca['idTipoDiligencia = ?'] = $idTipoDiligencia;
        }
        if (!empty($idDiligencia)) {
            $arrBusca['idDiligencia = ?'] = $idDiligencia;
        }

        $rsDiligencia = $tbDiligencia->buscar($arrBusca, ['DtSolicitacao DESC'])->current();
        
        if (!empty($rsDiligencia)) {
            $prazoPadrao = 40;
            
            $prazoResposta = $this->prazoParaResposta($rsDiligencia->DtSolicitacao, $prazoPadrao);
            $prazoRespostaCresc = $this->prazoParaResposta($rsDiligencia->DtSolicitacao, $prazoPadrao);
            $prazoRespostaDesc = $this->prazoParaResposta($rsDiligencia->DtSolicitacao, $prazoPadrao, true);
        
            if ($blnPrazoPadrao) {
                return $prazoPadrao;
            }
        
            if ($blnPrazoResposta) {
                return ($prazoRespostaDesc == '-1') ? $prazoPadrao : $prazoRespostaDesc;
            }
        
            $arrRetorno['prazoPadrao']   = $prazoPadrao;
            $arrRetorno['prazoRespostaCrescente'] = ($prazoRespostaCresc == '-1') ? $prazoPadrao : $prazoRespostaCresc;
            $arrRetorno['prazoRespostaDecrescente'] = ($prazoRespostaDesc == '-1') ? $prazoPadrao : $prazoRespostaDesc;

            $tipoDiligencia = $this->tipoDiligencia($rsDiligencia, $prazoPadrao, $prazoResposta);

            $arrRetorno['tipoDiligencia'] = $tipoDiligencia;
        }
        return $arrRetorno;
    }

    public function prazoParaResposta($dtSolicitacao = null, $prazoPadrao = null, $bln_decrescente=false)
    {
        if (!empty($dtSolicitacao)) {
            $prazo = round(\Data::CompararDatas($dtSolicitacao));
            if ($bln_decrescente) {
                $prazo = ((int)$prazoPadrao)-((int)$prazo);
            }
            if ($prazo > '0') {
                return $prazo;
            } elseif ($prazo <= '0') {
                return '0';
            } else {
                return '-1';
            }
        } else {
            return '0';
        }
    }

    public function tipoDiligencia($rsDiligencia, $prazoPadrao, $prazoResposta)
    {
        if ($rsDiligencia->DtSolicitacao && $rsDiligencia->DtResposta == null && $prazoResposta <= $prazoPadrao && $rsDiligencia->stEnviado == 'S') {
            $tipoDiligencia = "diligenciado";
        } elseif ($rsDiligencia->DtSolicitacao && $rsDiligencia->DtResposta == null && $prazoResposta > $prazoPadrao) {
            $tipoDiligencia = "nao_respondida";
        } elseif ($rsDiligencia->DtSolicitacao && $rsDiligencia->DtResposta != null) {
            if ($rsDiligencia->stEnviado == 'N' && $prazoResposta > $prazoPadrao) {
                $tipoDiligencia = "nao_respondida";
            } elseif ($rsDiligencia->stEnviado == 'N' && $prazoResposta <= $prazoPadrao) {
                $tipoDiligencia = "diligenciado";
            } else {
                $tipoDiligencia  = "diligencia_respondida";
            }
        } else {
            $tipoDiligencia  = "a_diligenciar";
        }
        return $tipoDiligencia;
    }
}

 
