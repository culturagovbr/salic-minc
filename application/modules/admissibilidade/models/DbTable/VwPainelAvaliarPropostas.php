<?php

class Admissibilidade_Model_DbTable_VwPainelAvaliarPropostas extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'vwPainelAvaliarPropostas';
    protected $_primary = 'idProjeto';

    public function propostas($where = array(), $order = array(), $start = 0, $limit = 10, $search = null)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sql = $db->select()
            ->from('vwPainelAvaliarPropostas', '*', $this->_schema);

        foreach ($where as $coluna => $valor) {
            $sql->where($coluna, $valor);
        }

        if (!empty($search['value'])) {
            $sql->where('idProjeto like ? OR NomeProposta like ? OR Tecnico like ?', '%' . $search['value'] . '%');
        }

        $sql->order($order);

        if (!is_null($start) && $limit) {
            $start = (int)$start;
            $limit = (int)$limit;
            $sql->limitPage($start, $limit);
        }

        return $db->fetchAll($sql);
    }

    public function propostasQuery()
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            ['a' => 'preprojeto'],
            [
                'idProjeto ' => 'idPreProjeto',
                'NomeProposta' => 'NomeProjeto',
                'idAgente',
                new Zend_Db_Expr('(SELECT top 1 dtMovimentacao from sac..tbMovimentacao WHERE idprojeto = 280327 and Movimentacao = 96) AS DtEnvio'),
            ]
        );

        $select->joinInner(
            ['b' => 'tbMovimentacao'],
            'b.idProjeto = a.idPreProjeto',
            [
                'b.idMovimentacao',
                'CodSituacao' => 'b.Movimentacao',
            ],
            $this->getSchema('sac')
        );

        $select->joinInner(
            ['c' => 'tbAvaliacaoProposta'],
            'a.idPreProjeto = c.idProjeto',
            [
                new Zend_Db_Expr('CONVERT(CHAR(20), c.DtAvaliacao, 120 ) AS DtAdmissibilidade'),
                new Zend_Db_Expr('DATEDIFF(d, c.DtAvaliacao, GETDATE()) AS diasCorridos'),
                'idUsuario' => 'c.idTecnico',
                'c.DtAvaliacao',
                'c.idAvaliacaoProposta'
            ],
            $this->getSchema('sac')
        );

        $select->joinInner(
            ['d' => 'Agentes'],
            'a.idAgente = d.idAgente',
            [],
            $this->getSchema('agentes')
        );

        $select->joinInner(
            ['e' => 'Verificacao'],
            'b.Movimentacao = e.idVerificacao',
            [],
            $this->getSchema('sac')
        );

        $select->where('b.Movimentacao IN( 96, 97, 127, 128 )');
        $select->where("a.stTipoDemanda = 'NA' ");
        $select->where('a.stEstado = 1');
        $select->where('b.stEstado = 0');
        $select->where('c.stEstado = 0');
        $select->where("NOT EXISTS(SELECT * FROM SAC.dbo.Projetos AS f WHERE a.idPreProjeto = f.idProjeto)");

        return $db->fetchAll($select);
    }

    private function obterDatasDiligencias($idProposta, $dtEnvio, $possuiArquivamento)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sqlInicioDiligencia = "SELECT convert( varchar( 20 ), DtAvaliacao,120 ) as DtInicioDiligencia
                                    FROM SAC.dbo.tbAvaliacaoProposta tbAvaliacaoProposta
                                    WHERE tbAvaliacaoProposta.idProjeto = {$idProposta}
                                    AND conformidadeOk < 9
                                    {$excluiDataArquivado}
                                    ORDER BY DtAvaliacao ASC";

        $sqlFimDiligencia = "SELECT DtMovimentacao as DtFimDiligencia
                                FROM sac..tbMovimentacao tbMovimentacao
                                WHERE tbMovimentacao.Movimentacao = 96
                                AND idprojeto = {$idProposta}
                                AND CONVERT( VARCHAR (20), DtMovimentacao, 120) != '{$dtEnvio}'
                                ORDER BY DtMovimentacao ASC";

        $resultInicioDiligencia = $db->fetchAll($sqlInicioDiligencia);
        $resultFimDiligencia = $db->fetchAll($sqlFimDiligencia);

        $diligencias = [];
        for ($i = 0; $i < count($resultInicioDiligencia); $i++) {
            $diligencias[$i] = new StdClass();
            $diligencias[$i]->DtInicioDiligencia = $resultInicioDiligencia[$i]->DtInicioDiligencia;
            $diligencias[$i]->DtFimDiligencia = '';
        }

        for ($j = 0; $j < count($resultFimDiligencia); $j++) {
            if (isset($diligencias[$j])) {
                $diligencias[$j]->DtFimDiligencia = $resultFimDiligencia[$j]->DtFimDiligencia;
            }
        }

        return $diligencias;
    }

    public function obterDataDesarquivamento($idProposta)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sqlArquivamento = "SELECT dtArquivamento, dtAvaliacao as dtDesarquivamento,DATEDIFF(day, dtArquivamento, dtAvaliacao) AS diasArquivado FROM sac..PreProjetoArquivado WHERE idpreprojeto = {$idProposta} AND stDecisao = 1";
        return $db->fetchAll($sqlArquivamento);
    }

    public function obterDiasArquivado($idProposta)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $diasArquivado = 0;
        $projetoArquivado = $this->obterDataDesarquivamento($idProposta);
        if ($projetoArquivado) {
            $diasArquivado = $projetoArquivado[0]->diasArquivado;
            $dtDesarquivamento = $projetoArquivado[0]->dtDesarquivamento;
            $sqlDescontoDiasAposDesarquivamento = "SELECT DATEDIFF(day,
'{$dtDesarquivamento }',
 (SELECT TOP 1 DtMovimentacao FROM sac..tbMovimentacao where movimentacao = 96 and idprojeto = {$idProposta} AND dtMovimentacao > '{$dtDesarquivamento }'))";
            $descontoDiasAposDesarquivamento = $db->fetchOne($sqlDescontoDiasAposDesarquivamento);
            $diasArquivado = $diasArquivado + $descontoDiasAposDesarquivamento;
        }
        return $diasArquivado;
    }

    public function obterDiasEmAnalise($idProposta)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $sqlDtEnvio = "SELECT TOP 1 DtMovimentacao as DtEnvio
                        FROM sac.dbo.tbMovimentacao
                        WHERE Movimentacao = 96
                        AND idprojeto = {$idProposta}";
        $DtEnvio = $db->fetchOne($sqlDtEnvio);

        $sqlDiasEmAnalise = "SELECT DATEDIFF(day, ?, GETDATE()) as DiasEmAnalise";
        $diasEmAnalise = $db->fetchOne($sqlDiasEmAnalise, $DtEnvio);
        $diasArquivado = $this->obterDiasArquivado($idProposta);
        $diligencias = $this->obterDatasDiligencias($idProposta, $DtEnvio, $diasArquivado);

        $diligenciaAberta = false;
        $diasEmDiligencia = 0;

        foreach ($diligencias as $diligencia) {
            if ($diligencia->DtFimDiligencia == '' || !$diligencia->DtFimDiligencia) {
                if ($diligenciaAberta) {
                    $diasEmAnalise = ($diasEmAnalise - $diasEmDiligencia - $diasArquivado);

                    return $diasEmAnalise;
                }
                $sqlDiligencia = "SELECT DATEDIFF(day, ?, GETDATE())";
                $diligencia->DtFimDiligencia = "GETDATE()";

                $diasEmDiligencia += (int) $db->fetchOne(
                    $sqlDiligencia, $diligencia->DtInicioDiligencia
                );
                $diligenciaAberta = true;
            } else {
                $sqlDiligencia = "SELECT DATEDIFF(day, ?, ?)";

                $diasEmDiligencia += (int) $db->fetchOne(
                    $sqlDiligencia,
                    [
                        $diligencia->DtInicioDiligencia,
                        $diligencia->DtFimDiligencia
                    ]
                );
            }
        }

        $diasEmAnalise = ($diasEmAnalise - $diasEmDiligencia - $diasArquivado);

        return ($diasEmAnalise);
    }

    public function obterPropostasParaAvaliacao(
        $where = [],
        $order = [],
        $start = 0,
        $limit = 10,
        $search = null,
        Admissibilidade_Model_DistribuicaoAvaliacaoProposta $distribuicaoAvaliacaoProposta = null
    )
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $select = $this->obterQueryPropostasParaAvaliacao(
            $where,
            $order,
            $start,
            $limit,
            $search,
            $distribuicaoAvaliacaoProposta
        );

        return $db->fetchAll($select);
    }

    public function obterQuantidadePropostasParaAvaliacao(
        $where = [],
        $order = [],
        $start = 0,
        $limit = 10,
        $search = null,
        Admissibilidade_Model_DistribuicaoAvaliacaoProposta $distribuicaoAvaliacaoProposta = null
    )
    {
        $subSelectPropostaParaAvaliacao = $this->obterQueryPropostasParaAvaliacao(
            $where,
            $order,
            $start,
            $limit,
            $search,
            $distribuicaoAvaliacaoProposta
        );

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->isUseSchema(false);
        $select->from(
            ['total_propostas_para_avaliacao' => new Zend_Db_Expr("({$subSelectPropostaParaAvaliacao})")],
            ['total' => new Zend_Db_Expr('count(*)')]
        );

        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->fetchRow($select);
    }

    private function obterQueryPropostasParaAvaliacao(
        $where = [],
        $order = [],
        $start = 0,
        $limit = 10,
        $search = null,
        Admissibilidade_Model_DistribuicaoAvaliacaoProposta $distribuicaoAvaliacaoProposta = null
    )
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from('vwPainelAvaliarPropostas',
            ['*'],
            $this->_schema);

        if (!is_null($start) && $limit > 0) {
            $start = (int)$start;
            $limit = (int)$limit;
            $select->limitPage($start, $limit);
        }

        $select->joinLeft(
            ['distribuicao_avaliacao_proposta']
            , "distribuicao_avaliacao_proposta.id_preprojeto = vwPainelAvaliarPropostas.idProjeto

                    and distribuicao_avaliacao_proposta.id_orgao_superior = {$distribuicaoAvaliacaoProposta->getIdOrgaoSuperior()}"
            ,
            [
                'avaliacao_atual' => "coalesce(distribuicao_avaliacao_proposta.avaliacao_atual, '0')",
                'quantidade_distribuicoes' => "coalesce(distribuicao_avaliacao_proposta.id_distribuicao_avaliacao_proposta, '0')",
                'dias_corridos_distribuicao' => new Zend_Db_Expr('DATEDIFF(d, distribuicao_avaliacao_proposta.data_distribuicao, GETDATE())')
            ]
            , $this->getSchema('sac')
        );

        $select->joinLeft(
            ['sugestao_enquadramento']
            , "sugestao_enquadramento.id_preprojeto = vwPainelAvaliarPropostas.idProjeto
                and sugestao_enquadramento.ultima_sugestao = " . Admissibilidade_Model_DbTable_SugestaoEnquadramento::ULTIMA_SUGESTAO_ATIVA
            , [
                'sugestao_enquadramento.id_area',
                'sugestao_enquadramento.id_segmento',
                'sugestao_enquadramento.id_sugestao_enquadramento',
            ]
            , $this->getSchema('sac')
        );

        $select->joinLeft(
            ['Segmento'],
            'Segmento.Codigo = sugestao_enquadramento.id_segmento',
            [
                'enquadramento' => new Zend_Db_Expr(
                    "CASE WHEN Segmento.tp_enquadramento = 1 THEN 'Artigo 26' "
                    . " WHEN Segmento.tp_enquadramento = 2 THEN 'Artigo 18' END"
                ),
                'descricao_segmento' => 'Segmento.Descricao'
            ],
            $this->getSchema('sac')
        );
        $select->joinLeft(
            ['Area'],
            'Area.Codigo = sugestao_enquadramento.id_area',
            [
                'descricao_area' => 'Area.Descricao'
            ],
            $this->getSchema('sac')
        );

        $select->joinInner(
            ['PlanoDistribuicaoProduto'],
            'PlanoDistribuicaoProduto.idProjeto = vwPainelAvaliarPropostas.idProjeto and PlanoDistribuicaoProduto.stPrincipal = 1',
            [
                'id_area_inicial' => 'PlanoDistribuicaoProduto.Area',
                'id_segmento_inicial' => 'PlanoDistribuicaoProduto.Segmento'
            ],
            $this->getSchema('sac')
        );

        $select->joinInner(
            ['SegmentoInicial' => 'Segmento'],
            'SegmentoInicial.Codigo = PlanoDistribuicaoProduto.Segmento',
            [
                'enquadramento_inicial' => new Zend_Db_Expr(
                    "CASE WHEN SegmentoInicial.tp_enquadramento = 1 THEN 'Artigo 26' "
                    . " WHEN SegmentoInicial.tp_enquadramento = 2 THEN 'Artigo 18' END"
                ),
                'descricao_segmento_inicial' => 'SegmentoInicial.Descricao'
            ],
            $this->getSchema('sac')
        );
        $select->joinInner(
            ['AreaInicial' => 'Area'],
            'AreaInicial.Codigo = PlanoDistribuicaoProduto.Area',
            [
                'descricao_area_inicial' => 'AreaInicial.Descricao'
            ],
            $this->getSchema('sac')
        );
        $select->joinLeft(
            ['tbRecursoProposta'],
            'tbRecursoProposta.idPreProjeto = vwPainelAvaliarPropostas.idProjeto and tbRecursoProposta.stAtivo = '
            . Recurso_Model_TbRecursoProposta::SITUACAO_RECURSO_ATIVO,
            [
                'tipo_recurso' => new Zend_Db_Expr(
                    "CASE WHEN tbRecursoProposta.tpRecurso = " . Recurso_Model_TbRecursoProposta::TIPO_RECURSO_PEDIDO_DE_RECONSIDERACAO
                    . " THEN '1 - Pedido de Reconsidera&ccedil;&atilde;o' "
                    . " WHEN tbRecursoProposta.tpRecurso = " . Recurso_Model_TbRecursoProposta::TIPO_RECURSO_RECURSO
                    . " THEN '2 - Recurso' "
                    . " ELSE '-' END"
                ),
                'prazo_recursal' => 'tbRecursoProposta.stRascunho'
            ],
            $this->getSchema('sac')
        );
        // Recurso_Model_TbRecursoProposta::tpRecurso

        if ($distribuicaoAvaliacaoProposta->getIdPerfil() == Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE
            || $distribuicaoAvaliacaoProposta->getIdPerfil() == Autenticacao_Model_Grupos::COMPONENTE_COMISSAO) {

            if ($distribuicaoAvaliacaoProposta->getIdPerfil() == Autenticacao_Model_Grupos::COMPONENTE_COMISSAO) {

                $select->isUseSchema(false);
                $selectPenultimaDistribuicao = $this->obterQueryPenultimaSugestaoEnquadramento();
                $select->joinLeft(
                    ['sugestao_distribuida' => 'sugestao_enquadramento']
                    , "sugestao_distribuida.id_preprojeto = vwPainelAvaliarPropostas.idProjeto
                            and sugestao_distribuida.id_sugestao_enquadramento = ({$selectPenultimaDistribuicao})"
                    , []
                    , $this->getSchema('sac')
                );

                $select->isUseSchema(true);

                $auth = Zend_Auth::getInstance();
                $tblAgente = new Agente_Model_DbTable_Agentes();
                $rsAgente = $tblAgente->buscarAgenteENome(
                    ['CNPJCPF = ?' => $auth->getIdentity()->usu_identificacao]
                );
                if ($rsAgente && count($rsAgente->current()->toArray()) > 0) {
                    $select->joinLeft(
                        ['tbtitulacaoconselheiro']
                        , "
                        tbtitulacaoconselheiro.cdArea = sugestao_distribuida.id_area
                        and tbtitulacaoconselheiro.stTitular = 1
                        and tbtitulacaoconselheiro.stConselheiro = 'A'
                    "
                        , []
                        , $this->getSchema('agentes')
                    );
                    $agente = $rsAgente->current()->toArray();
                    $select->where('tbtitulacaoconselheiro.idAgente = ?', $agente['idAgente']);
                }
                $select->where('distribuicao_avaliacao_proposta.avaliacao_atual = ?', Admissibilidade_Model_DistribuicaoAvaliacaoProposta::AVALIACAO_ATUAL_ATIVA);
            }
        }

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if (!empty($search['value'])) {
            $select->where('vwPainelAvaliarPropostas.idProjeto like ? OR vwPainelAvaliarPropostas.NomeProposta like ? OR Tecnico like ?', "%{$search['value']}%");
        }

        $restricaoPropostasParaAvaliacao = $this->obterRestricaoPropostasParaAvaliacao($distribuicaoAvaliacaoProposta);
        if ($restricaoPropostasParaAvaliacao) {
            $select->where($restricaoPropostasParaAvaliacao);
        }

        if ($order) {
            $select->order($order);
        }
//xdnb($select->assemble());
        return $select;
    }

    private function obterQueryPenultimaSugestaoEnquadramento()
    {
        $selectPenultimaDistribuicao = $this->select();
        $selectPenultimaDistribuicao->setIntegrityCheck(false);
        $selectPenultimaDistribuicao->from(
            ['sub_select_sugestao_enquadramento' => 'sugestao_enquadramento'],
            [new Zend_Db_Expr('id_sugestao_enquadramento')],
            $this->getSchema('sac')
        );
        $selectPenultimaDistribuicao->limit(1);
        $selectPenultimaDistribuicao->order('data_avaliacao desc');
        $selectPenultimaDistribuicao->where('id_preprojeto = vwPainelAvaliarPropostas.idProjeto');
        $selectPenultimaDistribuicao->where(
            'sub_select_sugestao_enquadramento.id_distribuicao_avaliacao_proposta
                   <> distribuicao_avaliacao_proposta.id_distribuicao_avaliacao_proposta
               OR sub_select_sugestao_enquadramento.id_distribuicao_avaliacao_proposta is null'
        );

        return $selectPenultimaDistribuicao;
    }

    private function obterRestricaoPropostasParaAvaliacao(Admissibilidade_Model_DistribuicaoAvaliacaoProposta $distribuicaoAvaliacaoProposta)
    {
        if ($distribuicaoAvaliacaoProposta->getIdPerfil()) {
            $restricaoPropostasParaAvaliacao = '( ';
            if ($distribuicaoAvaliacaoProposta->getIdPerfil() == Autenticacao_Model_Grupos::TECNICO_ADMISSIBILIDADE
                || $distribuicaoAvaliacaoProposta->getIdPerfil() == Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE) {
                $restricaoPropostasParaAvaliacao .= ' distribuicao_avaliacao_proposta.avaliacao_atual is null ';
                $restricaoPropostasParaAvaliacao .= ' AND distribuicao_avaliacao_proposta.id_distribuicao_avaliacao_proposta is null ';
//                $restricaoPropostasParaAvaliacao .= ' AND sugestao_enquadramento.id_area is null';
//                $restricaoPropostasParaAvaliacao .= ' AND sugestao_enquadramento.id_sugestao_enquadramento is null ';
            }
            if ($distribuicaoAvaliacaoProposta->getIdPerfil() != Autenticacao_Model_Grupos::TECNICO_ADMISSIBILIDADE) {
                if (!empty($restricaoPropostasParaAvaliacao) && $restricaoPropostasParaAvaliacao != '( ') {
                    $restricaoPropostasParaAvaliacao .= ' OR ';
                }

                $restricaoPropostasParaAvaliacao .= ' distribuicao_avaliacao_proposta.avaliacao_atual = 1';
                $restricaoPropostasParaAvaliacao .= ' AND distribuicao_avaliacao_proposta.id_distribuicao_avaliacao_proposta > 0 ';
                $perfisDistribuicao = $this->obterPerfisDistribuicao($distribuicaoAvaliacaoProposta);
                if ($perfisDistribuicao) {
                    $restricaoPropostasParaAvaliacao .= " AND distribuicao_avaliacao_proposta.id_perfil IN ({$perfisDistribuicao})";
                }
            }
            $restricaoPropostasParaAvaliacao .= ' )';
            return $restricaoPropostasParaAvaliacao;
        }
    }


    private function obterPerfisDistribuicao(Admissibilidade_Model_DistribuicaoAvaliacaoProposta $distribuicaoAvaliacaoProposta)
    {
        if ($distribuicaoAvaliacaoProposta->getIdPerfil() != Autenticacao_Model_Grupos::TECNICO_ADMISSIBILIDADE) {
            $perfis = [
                $distribuicaoAvaliacaoProposta->getIdPerfil()
            ];

            if ($distribuicaoAvaliacaoProposta->getIdPerfil() == Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE) {
                $perfis[] = Autenticacao_Model_Grupos::TECNICO_ADMISSIBILIDADE;
                $perfis[] = Autenticacao_Model_Grupos::COORDENADOR_GERAL_ADMISSIBILIDADE;
            }
            return implode(',', $perfis);
        }
    }

    public function propostasTotal($where = array(), $order = array(), $start = null, $limit = null, $search = null)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sql = $db->select()
            ->from('vwPainelAvaliarPropostas', 'count(*) as total', $this->_schema);

        foreach ($where as $coluna => $valor) {
            $sql->where($coluna, $valor);
        }

        if (!empty($search['value'])) {
            $sql->where('idProjeto like ? OR NomeProposta like ? OR Tecnico like ?', '%' . $search['value'] . '%');
        }

        $sql->order($order);

        if (!is_null($start) && $limit) {
            $start = (int)$start;
            $limit = (int)$limit;
            $sql->limitPage($start, $limit);
        }

        return $db->fetchRow($sql);
    }
}
