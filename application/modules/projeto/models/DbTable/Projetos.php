<?php

/**
 * @todo Mover todas os métodos e alterar todas as referências para da antiga classe para essa.
 */
class Projeto_Model_DbTable_Projetos extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'Projetos';
    protected $_primary = 'IdPRONAC';

    public function alterarOrgao($orgao, $idPronac)
    {
        $this->update(
            array(
                'Orgao' => $orgao
            ),
            array('IdPRONAC = ?' => $idPronac)
        );
    }

    public function obterValoresProjeto($idPronac)
    {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            array(
                'projetos' => $this->_name
            ),
            array(
                "ValorProposta" => new Zend_Db_Expr("sac.dbo.fnValorSolicitado(projetos.AnoProjeto,projetos.Sequencial)"),
                "ValorSolicitado" => new Zend_Db_Expr("sac.dbo.fnValorSolicitado(projetos.AnoProjeto,projetos.Sequencial)") ,
                "OutrasFontes" => new Zend_Db_Expr("sac.dbo.fnOutrasFontes(projetos.idPronac)"),
                "ValorAprovado" => new Zend_Db_Expr(
                    "case when projetos.Mecanismo ='2' or projetos.Mecanismo ='6'
                        then sac.dbo.fnValorAprovadoConvenio(projetos.AnoProjeto,projetos.Sequencial)
                     else
                        sac.dbo.fnValorAprovado(projetos.AnoProjeto,projetos.Sequencial)
                     end"
                ),
                "ValorProjeto" => new Zend_Db_Expr(
                    "case when projetos.Mecanismo ='2' or projetos.Mecanismo ='6'
                     then sac.dbo.fnValorAprovadoConvenio(projetos.AnoProjeto,projetos.Sequencial)
                     else sac.dbo.fnValorAprovado(projetos.AnoProjeto,projetos.Sequencial) + sac.dbo.fnOutrasFontes(projetos.idPronac)
                      end "
                ),
                "ValorCaptado" => new Zend_Db_Expr("sac.dbo.fnCustoProjeto (projetos.AnoProjeto,projetos.Sequencial)"),
            )
        );
        $objQuery->where('projetos.IdPRONAC = ?', $idPronac);

        return $this->_db->fetchRow($objQuery);
    }

    /**
     * @param $idPronac
     * @return array
     * @deprecated Utilizar a model fnVerificarProjetoAprovadoIN2017, metodo verificar
     */
    public function verificarIN2017($idPronac)
    {
        $retorno = 0;

        $projetoAprovado = $this->select();
        $projetoAprovado->setIntegrityCheck(false);
        $projetoAprovado->from(
            array('a' => $this->_name),
            'idPRONAC',
            $this->_schema
        );

        $projetoAprovado->joinInner(
            array('b' => 'preprojeto'),
            'b.idPreProjeto = a.idProjeto',
            array(),
            $this->_schema
        );

        $projetoAprovado->joinInner(
            array('c' => 'tbDocumentoAssinatura'),
            'a.IdPRONAC = c.IdPRONAC',
            array(),
            $this->_schema
        );

        $projetoAprovado->where("CONVERT(CHAR(10), (a.DtProtocolo),112) >= '20170410'");
        $projetoAprovado->where("CONVERT(CHAR(10), sac.dbo.fnDtEnvioAvaliacao(b.idPreProjeto),112) >= '20170410'");
        $projetoAprovado->where("a.idPronac = ?", $idPronac);

        $resultadoProjetoAprovado = $this->_db->fetchRow($projetoAprovado);

        $projetoTransformado = $this->select();
        $projetoTransformado->setIntegrityCheck(false);
        $projetoTransformado->from(
            array('a' => $this->_name),
            'idPRONAC',
            $this->_schema
        );

        $projetoTransformado->joinInner(
            array('b' => 'preprojeto'),
            'b.idPreProjeto = a.idProjeto',
            array(),
            $this->_schema
        );

        $projetoTransformado->where("CONVERT(CHAR(10), (a.DtProtocolo),112) >= '20170512'");
        $projetoTransformado->where("CONVERT(CHAR(10), sac.dbo.fnDtEnvioAvaliacao(b.idPreProjeto),112) >= '20170512'");
        $projetoTransformado->where("a.idPronac = ?", $idPronac);

        $resultadoProjetoTransformado = $this->_db->fetchRow($projetoTransformado);

        if (!empty($resultadoProjetoAprovado) || !empty($resultadoProjetoTransformado)) {
            $retorno = 1;
        }

        return $retorno;
    }

    /*
    * Verifica se existe uma proposta relacionada ao projeto
    * Projetos mais antigos eram impressos e não possuiam propostas
    *
    * Substitui a fnVerificarExistenciaDaProposta
    */
    public function obterIdPreProjetoDoProjeto($idPronac)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
            ->from(array('p' => $this->_name), array(), $this->_schema)
            ->join(array('pre' => 'preprojeto'), 'pre.idPreProjeto = p.idProjeto', array('pre.idPreProjeto'), $this->_schema)
            ->where('p.idPronac = ?', $idPronac);

        return $db->fetchOne($sql);
    }

    public function fnChecarLiberacaoDaAdequacaoDoProjeto($idPronac)
    {
        $exec = new Zend_Db_Expr("SELECT dbo.fnChecarLiberacaoDaAdequacaoDoProjeto({$idPronac})");

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchOne($exec);
    }

    public function spClonarProjeto($idPronac, $usuarioLogado)
    {
        $exec = new Zend_Db_Expr("EXEC SAC.dbo.spClonarProjeto {$idPronac}, {$usuarioLogado}");

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchRow($exec);
    }

    public function atualizarProjetoEnquadrado($projeto, $id_usuario_logado) {
        $situacaoFinalProjeto = 'B02';
        $orgaoDestino = null;
        $providenciaTomada = 'Projeto enquadrado ap&oacute;s avalia&ccedil;&atilde;o t&eacute;cnica.';
        if ($projeto['Situacao'] == 'B03') {
            $situacaoFinalProjeto = Projeto_Model_Situacao::PROJETO_ENQUADRADO_COM_RECURSO;
            $objOrgaos = new Orgaos();
            $dadosOrgaoSuperior = $objOrgaos->obterOrgaoSuperior($projeto['Orgao']);
            $orgaoDestino = Orgaos::ORGAO_SAV_DAP;
            if ($dadosOrgaoSuperior['Codigo'] == Orgaos::ORGAO_SUPERIOR_SEFIC) {
                $orgaoDestino = Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI;
            }
            $providenciaTomada = 'Projeto enquadrado ap&oacute;s avalia&ccedil;&atilde;o t&eacute;cnica do recurso.';
        }

        $objPlanoDistribuicaoProduto = new PlanoDistribuicao();
        $objPlanoDistribuicaoProduto->atualizarAreaESegmento(
            $projeto['Area'],
            $projeto['Segmento'],
            $projeto['idProjeto']
        );

        $objProjeto = new Projetos();
        $arrayDadosProjeto = array(
            'Situacao' => $situacaoFinalProjeto,
            'DtSituacao' => $objProjeto->getExpressionDate(),
            'ProvidenciaTomada' => $providenciaTomada,
            'Area' => $projeto['Area'],
            'Segmento' => $projeto['Segmento'],
            'logon' => $id_usuario_logado
        );

        if ($orgaoDestino) {
            $arrayDadosProjeto['Orgao'] = $orgaoDestino;
        }

        $arrayWhere = array('IdPRONAC  = ?' => $projeto['IdPRONAC']);
        $objProjeto->update($arrayDadosProjeto, $arrayWhere);

        if ($projeto['Situacao'] == 'B03') {
            $tbRecurso = new tbRecurso();
            $tbRecurso->finalizarRecurso($projeto['IdPRONAC']);
        }

        $objVerificacao = new Verificacao();
        $verificacao = $objVerificacao->findBy(array(
            'idVerificacao = ?' => 620
        ));

        $tbTextoEmailDAO = new tbTextoEmail();
        $textoEmail = $tbTextoEmailDAO->findBy(array(
            'idTextoEmail = ?' => 23
        ));

        $objInternet = new Agente_Model_DbTable_Internet();
        $arrayEmails = $objInternet->obterEmailProponentesPorPreProjeto($projeto['idProjeto']);

        foreach ($arrayEmails as $email) {
            EmailDAO::enviarEmail($email->Descricao, $verificacao['Descricao'], $textoEmail['dsTexto']);
        }
    }
}
