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
        $where = [
            'idReadequacao' => $idReadequacao
        ];

        return $modelTbReadequacao->findBy($where);
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
            'dsCampo' => utf8_encode($valorPreCarregado)
        ];

        return $resultArray;
    }

    public function salvar()
    {
        $parametros = $this->request->getParams();

        if (isset($parametros['idReadequacao'])){
            $idReadequacao = $parametros['idReadequacao'];
            $readequacaoModel = new \Readequacao_Model_DbTable_TbReadequacao();
            $readequacao = $readequacaoModel->buscarReadequacao($idReadequacao)->current()->toArray();

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
                $parametros['idDocumento'] = null;
            }
            if (!empty($_FILES['documento'])) {
                try {
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
                } catch(Exception $e) {
                    return $e;
                }
            }
        }

        $mapper = new \Readequacao_Model_TbReadequacaoMapper();
        $idReadequacao = $mapper->salvarSolicitacaoReadequacao($parametros);

        $result = $this->buscar($idReadequacao);
        $result = \TratarArray::utf8EncodeArray($result);

        return $result;
    }

    public function remover()
    {
        $parametros = $this->request->getParams();
        if (isset($parametros['id'])){
            $idReadequacao = $parametros['id'];
            $readequacaoModel = new \Readequacao_Model_DbTable_TbReadequacao();
            $excluir = $readequacaoModel->delete(
                ['idReadequacao = ?' => $idReadequacao]
            );
            return $excluir;
        }
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
                $data['erro'] = true;
                $data['mensagem'] = "Houve um erro e não foi possível finalizar a readequação.";
            }

            $data['mensagem'] = "Readequação enviada para análise.";
        } else {
            $data['mensagem'] = "É preciso especificar idPronac e idReadequação para finalizar uma readequação.";
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

    public function verificarPermissaoNoProjeto() {
        $parametros = $this->request->getParams();

        $permissao = false;
        $auth = \Zend_Auth::getInstance()->getIdentity();
        $arrAuth = array_change_key_case((array)$auth);

        if (!isset($arrAuth['usu_codigo'])) {
            $idUsuarioLogado = $arrAuth['idusuario'];
            $fnVerificarPermissao = new \Autenticacao_Model_FnVerificarPermissao();

            $idPronac = $parametros['idpronac'] ? $parametros['idpronac'] : $parametros['idPronac'];
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
}
