<?php

class Parecer_Model_TbDistribuirParecerMapper extends MinC_Db_Mapper
{

    public function __construct()
    {
        $this->setDbTable('Parecer_Model_DbTable_TbDistribuirParecer');
    }

    public function isValid($model)
    {
        $booStatus = true;
        $arrData = $model->toArray();
        $arrRequired = [
            'idPRONAC',
            'idProduto',
            'TipoAnalise',
            'idOrgao',
            'idUsuario',
            'siAnalise',
            'siEncaminhamento',
        ];
        foreach ($arrRequired as $strValue) {
            if (!isset($arrData[$strValue]) || empty($arrData[$strValue])) {
                $this->setMessage('Campo obrigat&oacute;rio!', $strValue);
                $booStatus = false;
            }
        }

        return $booStatus;
    }

    public function devolverProdutoParaCoordenador(Parecer_Model_TbDistribuirParecer $modelDistribuicao)
    {
        $modelDistribuicao->setDtDevolucao(\MinC_Db_Expr::date());
        $modelDistribuicao->setDtRetorno(null);

        return $this->inserirDistribuicaoProduto($modelDistribuicao);
    }

    public function solicitarReanaliseParecerista(Parecer_Model_TbDistribuirParecer $modelDistribuicao)
    {
        $modelDistribuicao->setDtDevolucao(null);
        $modelDistribuicao->setDtRetorno(null);
//        $modelDistribuicao->setSiEncaminhamento(\TbTipoEncaminhamento::SOLICITACAO_ENCAMINHADA_AO_PARECERISTA);
//        $modelDistribuicao->setSiAnalise(\Parecer_Model_TbDistribuirParecer::SI_ANALISE_EM_ANALISE);
        return $this->inserirDistribuicaoProduto($modelDistribuicao);
    }

    public function devolverProdutoParaSecult(Parecer_Model_TbDistribuirParecer $modelDistribuicao)
    {
        $modelDistribuicao->setDtDevolucao(null);
        $modelDistribuicao->setDtRetorno(null);
        $modelDistribuicao->setSiEncaminhamento(\TbTipoEncaminhamento::SOLICITACAO_DEVOLVIDA_AO_MINC_PELA_UNIDADE);
        $modelDistribuicao->setSiAnalise(\Parecer_Model_TbDistribuirParecer::SI_ANALISE_FINALIZADA);
        $modelDistribuicao->setFecharAnalise(\Parecer_Model_TbDistribuirParecer::FECHAR_ANALISE_EM_VALIDACAO);

        return $this->inserirDistribuicaoProduto($modelDistribuicao);
    }

    public function distribuirProdutoParaParecerista(Parecer_Model_TbDistribuirParecer $modelDistribuicao)
    {
        $modelDistribuicao->setDtDistribuicao(MinC_Db_Expr::date());
//        $modelDistribuicao->setSiEncaminhamento(TbTipoEncaminhamento::SOLICITACAO_ENCAMINHADA_AO_PARECERISTA);
//        $modelDistribuicao->setSiAnalise(Parecer_Model_TbDistribuirParecer::SI_ANALISE_AGUARDANDO_ANALISE);
        return $this->inserirDistribuicaoProduto($modelDistribuicao);
    }

    public function encaminharProdutoParaVinculada(Parecer_Model_TbDistribuirParecer $modelDistribuicao)
    {
        if (empty($modelDistribuicao->getIdOrgao())) {
            throw new Exception("Orgão destino não informado");
        }

        $modelDistribuicao->setDtEnvio(MinC_Db_Expr::date());
        $modelDistribuicao->setDtDistribuicao(null);
        $this->inserirDistribuicaoProduto($modelDistribuicao);
    }

    private function inserirDistribuicaoProduto(Parecer_Model_TbDistribuirParecer $modelDistribuicao)
    {
        try {
            $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
            $tbDistribuirParecer->getAdapter()->beginTransaction();
            $tbDistribuirParecer->alterar(
                ['stEstado' => Parecer_Model_TbDistribuirParecer::ST_ESTADO_INATIVO],
                ['idDistribuirParecer = ?' => $modelDistribuicao->getIdDistribuirParecer()]
            );

            $modelDistribuicao->setStEstado(Parecer_Model_TbDistribuirParecer::ST_ESTADO_ATIVO);
            $modelDistribuicao->setIdDistribuirParecer(null);
            $modelDistribuicao->setStDiligenciado(null);

            $response = $tbDistribuirParecer->inserir($modelDistribuicao->toArray());
            $tbDistribuirParecer->getAdapter()->commit();
            return $response;
        } catch (\Exception $e) {
            $tbDistribuirParecer->getAdapter()->rollBack();
            throw $e;
        }
    }

    public function obterIdPareceristaOriginalProduto($idPronac, $idProduto)
    {
        $whereDistribuicaoAtual = [
            'idPRONAC = ?' => $idPronac,
            'stEstado = ?' => Parecer_Model_TbDistribuirParecer::ST_ESTADO_INATIVO,
            'TipoAnalise = ?' => \Parecer_Model_TbDistribuirParecer::TIPO_ANALISE_PRODUTO_COMPLETO,
            'idProduto = ?' => $idProduto,
            'idAgenteParecerista IS NOT NULL' => ''
        ];

        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        return $tbDistribuirParecer->findBy($whereDistribuicaoAtual)['idAgenteParecerista'];
    }

    public function prepararProjetoParaAnalise($idPronac)
    {
        $this->desabilitarDocumentoAssinatura($idPronac);

        $providenciaTomada = 'Encaminhado ao perito para an&aacute;lise t&eacute;cnica e emiss&atilde;o de parecer.';
        $this->alterarSituacaoDoProjeto(
            $idPronac,
            \Projeto_Model_Situacao::ENCAMINHADO_PARA_ANALISE_TECNICA,
            $providenciaTomada
        );
    }

    private function alterarSituacaoDoProjeto($idPronac, $situacao, $providencia)
    {
        $projetos = new \Projetos();
        $projetos->alterarSituacao($idPronac, null, $situacao, $providencia);
    }

    private function desabilitarDocumentoAssinatura($idPronac)
    {
        $objTbDocumentoAssinatura = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $idDocumentoAssinatura = $objTbDocumentoAssinatura->getIdDocumentoAssinatura(
            $idPronac,
            \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_ANALISE_INICIAL
        );

        if ($idDocumentoAssinatura) {
            $objDocumentoAssinatura = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
            $dadosDocumentoAssinatura = ["stEstado" => \Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_INATIVO];
            $whereDocumentoAssinatura = "idDocumentoAssinatura = $idDocumentoAssinatura";

            $objDocumentoAssinatura->update($dadosDocumentoAssinatura, $whereDocumentoAssinatura);
        }
    }
}
