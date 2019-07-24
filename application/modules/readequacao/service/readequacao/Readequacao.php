<?php

namespace Application\Modules\Readequacao\Service\Readequacao;

use MinC\Servico\IServicoRestZend;
use \Application\Modules\Documento\Service\Documento\Documento as DocumentoService;

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
        
        return $return;
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
            case 'analise':
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
        
    }
    
    public function removerPlanilhaOrcamentaria($readequacao) {
        
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
    
    public function finalizar()
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

    public function verificarPermissaoNoProjeto($idPronac = '') {
        $parametros = $this->request->getParams();

        $permissao = false;
        $auth = \Zend_Auth::getInstance()->getIdentity();
        $arrAuth = array_change_key_case((array)$auth);

        if (!isset($arrAuth['usu_codigo'])) {
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
            $permissao = $consulta->Permissao;
        }
        return $permissao;
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

    public function obterPlanilha() {
        $parametros = $this->request->getParams();

        $idPronac = $parametros['idPronac'];
        $idTipoReadequacao = $parametros['idTipoReadequacao'];

        $tipos = [
            \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_REMANEJAMENTO_PARCIAL => \spPlanilhaOrcamentaria::TIPO_PLANILHA_REMANEJAMENTO,
            \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA => \spPlanilhaOrcamentaria::TIPO_PLANILHA_READEQUACAO,
            \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SALDO_APLICACAO => \spPlanilhaOrcamentaria::TIPO_PLANILHA_SALDO_APLICACAO
        ];

        $tipoPlanilha = ($idTipoReadequacao) ? $tipoPlanilha[$idTipoReadequacao] : \spPlanilhaOrcamentaria::TIPO_PLANILHA_APROVADA_ATIVA;
        $planilhaOrcamentariaAtiva = [];
        
        if ($idTipoReadequacao) {
            $spPlanilhaOrcamentaria = new \spPlanilhaOrcamentaria();
            $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($idPronac, $tipos[$idTipoReadequacao]);
            $tbPlanilhaAprovacao = new \tbPlanilhaAprovacao();
            $planilhaOrcamentariaAtiva = $tbPlanilhaAprovacao->obterPlanilhaAtiva($idPronac);
        } else {
            $tbPlanilhaAprovacao = new \tbPlanilhaAprovacao();
            $planilhaOrcamentaria = $tbPlanilhaAprovacao->obterPlanilhaAtiva($idPronac);
        }
        
        $planilha = [];
        foreach ($planilhaOrcamentaria as $item) {
            if ($item->idPlanilhaAprovacaoPai != null) {
                $planilha[$item->idPlanilhaAprovacaoPai] = $item;
            } else {
                $planilha[] = $item;
            }
        };
        
        if (!empty($planilhaOrcamentariaAtiva)) {
            $planilhaAtiva = [];
            foreach ($planilhaOrcamentariaAtiva as $item) {
                $itemAtivo = new \StdClass();
                if (array_key_exists($item->idPlanilhaAprovacao, $planilha)) {
                    $planilha[$item->idPlanilhaAprovacao]->idUnidadeAtivo = $item->idUnidade;
                    $planilha[$item->idPlanilhaAprovacao]->OcorrenciaAtivo = $item->Ocorrencia;
                    $planilha[$item->idPlanilhaAprovacao]->QuantidadeAtivo = $item->Quantidade;
                    $planilha[$item->idPlanilhaAprovacao]->QtdeDiasAtivo = $item->QtdeDias;
                    $planilha[$item->idPlanilhaAprovacao]->vlUnitarioAtivo = $item->vlUnitario;
                } else {
                    $planilha[] = $item;
                }
            }

            $planilhaOrcamentaria = $planilha;
        }
        $result = [];
        foreach ($planilhaOrcamentaria as $item) {
            $item->Produto = utf8_encode($item->Produto);
            $item->NomeProjeto = utf8_encode($item->NomeProjeto);
            $item->Etapa = utf8_encode($item->Etapa);
            $item->Municipio = utf8_encode($item->Municipio);
            $item->Item = utf8_encode($item->Item);
            $item->dsJustificativa = utf8_encode($item->dsJustificativa);
            $item->FonteRecurso = utf8_encode($item->FonteRecurso);
            $item->Unidade = utf8_encode($item->Unidade);
            $item->vlComprovado = (!is_null($item->vlComprovado)) ? $item->vlComprovado : 0;
            
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
                
                throw new Exception($atualizarCustosVinculados['mensagem']);
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
}

