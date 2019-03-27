<?php

class Readequacao_Model_TbPlanoDistribuicaoMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Readequacao_Model_DbTable_TbPlanoDistribuicao');
    }

    public function save($model)
    {
        return parent::save($model);
    }

    public function criarReadequacaoPlanoDistribuicao(Projeto_Model_TbProjetos $projeto)
    {
        $TbReadequacaoMapper = new Readequacao_Model_TbReadequacaoMapper();
        $readequacaoAtiva = $TbReadequacaoMapper->findBy([
            'idPronac = ?' => $projeto->getIdPRONAC(),
            'idTipoReadequacao = ?' => Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DISTRIBUICAO,
            'stEstado = ?' => 0
        ]);

        if (empty($readequacaoAtiva)) {
            $idReadequacao = $TbReadequacaoMapper->salvarSolicitacaoReadequacao([
                'idPronac' => $projeto->getIdPRONAC(),
                'idTipoReadequacao' => Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DISTRIBUICAO,
                'dsJustificativa' => ' ',
            ]);
        } else {
            $idReadequacao = $readequacaoAtiva['idReadequacao'];
        }

        $tbPlanoDistribuicaoMapper = new Readequacao_Model_TbPlanoDistribuicaoMapper();
        $planosDistribuicao = $tbPlanoDistribuicaoMapper->findBy(
            ['idPronac = ?' => $projeto->getidProjeto(), 'stAtivo = ?' => 'S', 'idReadequacao IS NULL' => '']
        );

        if (empty($planosDistribuicao)) {
            $tbDetalhaPlanoMapper = new Readequacao_Model_TbDetalhaPlanoDistribuicaoReadequacaoMapper();
            $tbDetalhaPlanoMapper->copiarDetalhamentosDaProposta($projeto, $idReadequacao);
            $this->copiarPlanoDistribuicaoDaProposta($projeto, $idReadequacao);
        }

        return true;
    }

    public function obterPlanosDistribuicao(Projeto_Model_TbProjetos $projeto)
    {
        $tbPlanoDistribuicao = new Readequacao_Model_DbTable_TbPlanoDistribuicao();
        $planosDistribuicao = $tbPlanoDistribuicao->obterPlanosDistribuicaoReadequacao($projeto->getIdPRONAC());

        return $planosDistribuicao->toArray();
    }

    public function copiarPlanoDistribuicaoDaProposta(Projeto_Model_TbProjetos $projeto, $idReadequacao = null)
    {
        $tbPlanoDistribuicao = new Readequacao_Model_DbTable_TbPlanoDistribuicao();
        $distribuicaoDaProposta = $tbPlanoDistribuicao->buscarPlanosDistribuicaoReadequacao($projeto->getIdPRONAC());

        if (empty($distribuicaoDaProposta)) {
            throw new Exception("Nenhum plano de distribui&ccedil;&atilde;o encontrado!");
        }

        try {
            $novoPlanoDistribuicao = [];
            $tbDetalhaReadequacao = new Readequacao_Model_DbTable_TbDetalhaPlanoDistribuicaoReadequacao();
            foreach ($distribuicaoDaProposta as $value) {
                $novoPlanoDistribuicao['idReadequacao'] = $idReadequacao;
                $novoPlanoDistribuicao['idPlanoDistribuicaoOriginal'] = $value->idPlanoDistribuicao;
                $novoPlanoDistribuicao['idProduto'] = $value->idProduto;
                $novoPlanoDistribuicao['cdArea'] = $value->idArea;
                $novoPlanoDistribuicao['cdSegmento'] = $value->idSegmento;
                $novoPlanoDistribuicao['idPosicaoLogo'] = $value->idPosicaoDaLogo;
                $novoPlanoDistribuicao['qtProduzida'] = $value->QtdeProduzida;
                $novoPlanoDistribuicao['qtPatrocinador'] = $value->QtdePatrocinador;
                $novoPlanoDistribuicao['qtOutros'] = $value->QtdeOutros;
                $novoPlanoDistribuicao['qtProponente'] = $value->QtdeProponente;
                $novoPlanoDistribuicao['qtVendaNormal'] = $value->QtdeVendaNormal;
                $novoPlanoDistribuicao['qtVendaPromocional'] = $value->QtdeVendaPromocional;
                $novoPlanoDistribuicao['vlUnitarioNormal'] = $value->PrecoUnitarioNormal;
                $novoPlanoDistribuicao['vlUnitarioPromocional'] = $value->PrecoUnitarioPromocional;
                $novoPlanoDistribuicao['stPrincipal'] = $value->stPrincipal;
                $novoPlanoDistribuicao['canalAberto'] = $value->canalAberto;
                $novoPlanoDistribuicao['tpSolicitacao'] = 'N'; # N - nenhuma, I - inclusao, A - alteracao
                $novoPlanoDistribuicao['stAtivo'] = 'S';
                $novoPlanoDistribuicao['idPronac'] = $projeto->getIdPRONAC();

                $idNovoPlanoDistribuicao = $tbPlanoDistribuicao->inserir($novoPlanoDistribuicao);

                # tem que atualizar os detalhamentos do plano antigo para o novo
                $where = [];
                $where[] = $tbDetalhaReadequacao->getAdapter()->quoteInto(
                    'idPlanoDistribuicao = ?', $value->idPlanoDistribuicao
                );

                $where[] = $tbDetalhaReadequacao->getAdapter()->quoteInto(
                    'stAtivo = ?', 'S'
                );

                $tbDetalhaReadequacao->update(
                    ['idPlanoDistribuicao' => $idNovoPlanoDistribuicao],
                    $where
                );
            }
        } catch (Exception $e) {
            throw new $e;
        }

        return true;
    }

    public function incluirIdReadequacaoNasSolicitacoesAtivas($idPronac, $idReadequacao)
    {
        $tbPlanoDistribuicao = new Readequacao_Model_DbTable_TbPlanoDistribuicao();
        $whereDistribuicao = "idPronac = {$idPronac} AND idReadequacao IS NULL";
        return $tbPlanoDistribuicao->update(['idReadequacao' => $idReadequacao], $whereDistribuicao);
    }

    public function excluirReadequacaoPlanoDistribuicaoAtiva($idPronac)
    {
        if (empty($idPronac)) {
            throw new Exception("Pronac é obrigatório");
        }

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $readequacaoAtiva = $tbReadequacao->buscar(array(
            'idPronac = ?' => $idPronac,
            'idTipoReadequacao = ?' => Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DISTRIBUICAO,
            'siEncaminhamento = ?' => Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_CADASTRADA_PROPONENTE,
            'stEstado = ?' => 0,
            'stAtendimento = ?' => 'N'
        ))->current();

        if (empty($readequacaoAtiva)) {
            throw new Exception("Nenhuma readequa&ccedil;&atilde;o encontrada!");
        }

        if (!empty($readequacaoAtiva->idDocumento)) {
            $tbDocumento = new tbDocumento();
            $dadosArquivo = $tbDocumento->buscar(array('idDocumento =?' => $readequacaoAtiva->idDocumento))->current();

            if ($dadosArquivo) {
                $tbDocumento = new tbDocumento();
                $tbDocumento->excluir("idArquivo = {$dadosArquivo->idArquivo} and idDocumento= {$readequacaoAtiva->idDocumento} ");

                $tbArquivoImagem = new tbArquivoImagem();
                $tbArquivoImagem->excluir("idArquivo =  {$dadosArquivo->idArquivo} ");

                $tbArquivo = new tbArquivo();
                $tbArquivo->excluir("idArquivo = {$dadosArquivo->idArquivo} ");
            }
        }

        $tbPlanoDistribuicao = new Readequacao_Model_DbTable_TbPlanoDistribuicao();
        $tbPlanoDistribuicao->delete(array(
            'idReadequacao = ?' => $readequacaoAtiva->idReadequacao, 'idPronac = ?' => $idPronac, 'stAtivo = ?' => 'S'
        ));

        $tbDetalhaPlanoDistribuicao = new Readequacao_Model_DbTable_TbDetalhaPlanoDistribuicaoReadequacao();
        $tbDetalhaPlanoDistribuicao->delete(array(
            'idReadequacao = ?' => $readequacaoAtiva->idReadequacao, 'idPronac = ?' => $idPronac, 'stAtivo = ?' => 'S'
        ));

        return $tbReadequacao->delete(array('idPronac =?' => $idPronac, 'idReadequacao = ?' => $readequacaoAtiva->idReadequacao));
    }

    public function atualizarAnaliseTecnica($idPronac, $idReadequacao, $idPerfil, $parecerReadequacao)
    {
        $data = [];

        $tpAnaliseTecnica = 'I';
        if ($parecerReadequacao == 2) {
            $tpAnaliseTecnica = 'D';
        }

        if ($idPerfil == Autenticacao_Model_Grupos::PARECERISTA || $idPerfil == Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO) {
            $data['tpAnaliseTecnica'] = $tpAnaliseTecnica;
        }

        if ($idPerfil == Autenticacao_Model_Grupos::COMPONENTE_COMISSAO) {
            $data['tpAnaliseComissao'] = $tpAnaliseTecnica;
        }

        $where = ['idPronac = ?' => $idPronac, 'idReadequacao = ?' => $idReadequacao, 'stAtivo = ?' => 'S'];

        $tbPlanoDistribuicao = new Readequacao_Model_DbTable_TbPlanoDistribuicao();
        return $tbPlanoDistribuicao->update($data, $where);
    }

    public function finalizarAnaliseReadequacaoPlanoDistribuicao($idPronac, $idReadequacao, $parecer)
    {
        $auth = Zend_Auth::getInstance();

        $PlanoDistribuicaoProduto = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
        $tbPlanoDistribuicao = new Readequacao_Model_DbTable_TbPlanoDistribuicao();
        $tbDetalhaPlanoDistribuicao = new Proposta_Model_DbTable_TbDetalhaPlanoDistribuicao();
        $tbDetalhaPlanoDistribuicaoReadequacao = new Readequacao_Model_DbTable_TbDetalhaPlanoDistribuicaoReadequacao();

        $planosDistribuicaoReadequados = [];
        if ($parecer == 2) {
            $planosDistribuicaoReadequados = $tbPlanoDistribuicao->buscar([
                'idReadequacao = ?' => $idReadequacao,
                'stAtivo = ?' => 'S',
                'tpSolicitacao <> ?' => 'N',
            ])->toArray();

            $detalhamentosReadequados = $tbDetalhaPlanoDistribuicaoReadequacao->buscar([
                'idReadequacao = ?' => $idReadequacao,
                'tpSolicitacao <> ?' => 'E',
                'stAtivo = ?' => 'S'
            ])->toArray();

            $projetos = new Projetos();
            $projeto = $projetos->buscar(array('IdPRONAC=?' => $idPronac))->current();

            $planosDistribuicaoOriginais = $PlanoDistribuicaoProduto->buscar(array('idProjeto=?' => $projeto->idProjeto));
        }

        foreach ($planosDistribuicaoReadequados as $planoReadequado) {

            $idPlanoDistribuicaoOriginal = $planoReadequado['idPlanoDistribuicaoOrignal'];

            /**
             * if temporario, ate que esse select retorne zero em prod:
             * select * from sac.dbo.tbPlanoDistribuicao
             * where idPlanoDistribuicaoOriginal is null and stAtivo = 'S';
             */
            if (empty($idPlanoDistribuicaoOriginal)) {
                foreach ($planosDistribuicaoOriginais as $planoOriginal) {
                    if ($planoOriginal->idProduto == $planoReadequado['idProduto']
                        && $planoOriginal->Area == $planoReadequado['cdArea']
                        && $planoOriginal->Segmento == $planoReadequado['cdSegmento']
                        && $planoOriginal->idProjeto == $projeto->idProjeto
                    ) {
                        $idPlanoDistribuicaoOriginal = $planoOriginal->idPlanoDistribuicao;
                    }
                }
            }

            $avaliacao = $planoReadequado['tpAnaliseComissao'];
            if ($planoReadequado['tpAnaliseComissao'] == 'N') {
                $avaliacao = $planoReadequado['tpAnaliseTecnica'];
            }

            //Se a avaliação foi deferida, realiza as mudanças necessárias na tabela original.
            if ($avaliacao == 'D' && !empty($idPlanoDistribuicaoOriginal)
            ) {
                // pega dados da tabela temporaria (tbPlanoDistribuicao) e faz update em PlanoDistribuicaoProduto
                $updatePlanoDistr = array();
                $updatePlanoDistr['idProjeto'] = $projeto->idProjeto;
                $updatePlanoDistr['idProduto'] = $planoReadequado['idProduto'];
                $updatePlanoDistr['Area'] = $planoReadequado['cdArea'];
                $updatePlanoDistr['Segmento'] = $planoReadequado['cdSegmento'];
                $updatePlanoDistr['idPosicaoDaLogo'] = $planoReadequado['idPosicaoLogo'];
                $updatePlanoDistr['QtdeProduzida'] = $planoReadequado['qtProduzida'];
                $updatePlanoDistr['QtdePatrocinador'] = $planoReadequado['qtPatrocinador'];
                $updatePlanoDistr['QtdeOutros'] = $planoReadequado['qtOutros'];
                $updatePlanoDistr['QtdeProponente'] = $planoReadequado['qtProponente'];
                $updatePlanoDistr['QtdeVendaNormal'] = $planoReadequado['qtVendaNormal'];
                $updatePlanoDistr['QtdeVendaPromocional'] = $planoReadequado['qtVendaPromocional'];
                $updatePlanoDistr['PrecoUnitarioPromocional'] = $planoReadequado['vlUnitarioPromocional'];
                $updatePlanoDistr['vlReceitaTotalPrevista'] = $planoReadequado['vlReceitaTotalPrevista'];
                $updatePlanoDistr['qtdeVendaPopularNormal'] = $planoReadequado['qtdeVendaPopularNormal'];
                $updatePlanoDistr['qtdeVendaPopularPromocional'] = $planoReadequado['qtdeVendaPopularPromocional'];
                $updatePlanoDistr['vlUnitarioPopularNormal'] = $planoReadequado['vlUnitarioPopularNormal'];
                $updatePlanoDistr['receitaPopularPromocional'] = $planoReadequado['receitaPopularPromocional'];
                $updatePlanoDistr['receitaPopularNormal'] = $planoReadequado['receitaPopularNormal'];
                $updatePlanoDistr['precoUnitarioNormal'] = $planoReadequado['precoUnitarioNormal'];
                $updatePlanoDistr['vlUnitarioNormal'] = $planoReadequado['vlUnitarioNormal'];
                $updatePlanoDistr['stPrincipal'] = $planoReadequado['stPrincipal'];
                $updatePlanoDistr['canalAberto'] = $planoReadequado['canalAberto'];
                $updatePlanoDistr['Usuario'] = $auth->getIdentity()->usu_codigo;

                $wherePlanoDistr = array();
                $wherePlanoDistr['idPlanoDistribuicao = ?'] = $idPlanoDistribuicaoOriginal;

                $PlanoDistribuicaoProduto->update($updatePlanoDistr, $wherePlanoDistr);

                # detalhamentos - remove
                $tbDetalhaPlanoDistribuicao->delete(['idPlanoDistribuicao = ?' => $idPlanoDistribuicaoOriginal]);

                # detalhamentos - adiciona
                foreach ($detalhamentosReadequados as $detalhamento) {
                    if ($detalhamento['idPlanoDistribuicao'] == $planoReadequado['idPlanoDistribuicao']) {
                        $detalhamento['idPlanoDistribuicao'] = $idPlanoDistribuicaoOriginal;
                        unset($detalhamento['tpSolicitacao']);
                        unset($detalhamento['stAtivo']);
                        unset($detalhamento['idPronac']);
                        unset($detalhamento['idDetalhaPlanoDistribuicao']);
                        unset($detalhamento['idReadequacao']);
                        unset($detalhamento['idDetalhaOriginal']);
                        $tbDetalhaPlanoDistribuicao->inserir($detalhamento);
                    }
                }
            }
        }

        $dados = array();
        $where = array();
        $dados['stAtivo'] = 'N';
        $where['idPronac = ? '] = $idPronac;
        $where['idReadequacao = ?'] = $idReadequacao;

        $tbPlanoDistribuicao->update($dados, $where);
        $tbDetalhaPlanoDistribuicaoReadequacao->update($dados, $where);
    }
}
