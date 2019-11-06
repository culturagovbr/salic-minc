<?php
/**
 * DAO tbDistribuirReadequacao
 * @author jeffersonassilva@gmail.com - XTI
 * @since 11/03/2014
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Readequacao_Model_tbDistribuirReadequacao extends MinC_Db_Table_Abstract
{
    protected $_schema = "sac";
    protected $_name   = "tbDistribuirReadequacao";
    protected $_primary = "idDistribuirReadequacao";

    public function buscarReadequacaoCoordenadorParecerAguardandoAnalise($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        try {
            $select = $this->select();
            $select->setIntegrityCheck(false);
            $select->from(
                array('tbDistribuirReadequacao' => $this->_name),
                new Zend_Db_Expr("
                    tbDistribuirReadequacao.idDistribuirReadequacao,
                    projetos.idPRONAC,
                    projetos.AnoProjeto+projetos.Sequencial AS PRONAC,
                    projetos.NomeProjeto,
                    projetos.Area,
                    projetos.Segmento,
                    tbTipoReadequacao.dsReadequacao as tpReadequacao,
                    tbDistribuirReadequacao.DtEncaminhamento AS dtEnvio,
                    DATEDIFF(DAY,
                    tbDistribuirReadequacao.dtEncaminhamento,
                    GETDATE()) as qtDiasAguardandoDistribuicao,
                    tbReadequacao.idReadequacao,
                    tbReadequacao.idTipoReadequacao,
                    tbReadequacao.siEncaminhamento,
                    tbReadequacao.stAtendimento,
                    CAST(tbReadequacao.dsSolicitacao AS TEXT) AS dsSolicitacao,
                    CAST(tbReadequacao.dsJustificativa AS TEXT) AS dsJustificativa,
                    tbReadequacao.idAvaliador,
                    CAST(tbReadequacao.dsAvaliacao AS TEXT) AS dsAvaliacao,
                    tbDistribuirReadequacao.idUnidade as idOrgao,
                    dsOrientacao
            ")
            );
            $select->joinInner(
                array('tbReadequacao' => 'tbReadequacao'),
                'tbDistribuirReadequacao.idReadequacao = tbReadequacao.idReadequacao ',
                array(''),
                $this->_schema
            );
            $select->joinInner(
                array('projetos' => 'Projetos'),
                'projetos.IdPRONAC = tbReadequacao.IdPRONAC ',
                array(''),
                $this->_schema
            );
            $select->joinInner(
                ['tbTipoReadequacao' => 'tbTipoReadequacao'],
                'tbTipoReadequacao.idTipoReadequacao = tbReadequacao.idTipoReadequacao',
                [''],
                $this->_schema
            );

            $select->joinInner(
                ['nomes' => 'Nomes'],
                'nomes.idAgente = tbReadequacao.idSolicitante',
                ['Descricao AS dsNomeSolicitante'],
                $this->getSchema('agentes')
            );
        
            $select->joinLeft(
                ['usuarios' => 'Usuarios'],
                'tbReadequacao.idAvaliador = usuarios.usu_codigo',
                ['usu_nome AS dsNomeAvaliador'],
                $this->getSchema('Tabelas')
            );

            
            $select->where('tbReadequacao.stEstado = ? ', 0);
            $select->where('tbDistribuirReadequacao.stValidacaoCoordenador = ? ', 0);
            $select->where('tbReadequacao.siEncaminhamento = ? ', Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_UNIDADE_ANALISE);

            foreach ($where as $coluna => $valor) {
                $select->where($coluna, $valor);
            }
            
            $select->order($order);

            if ($tamanho > -1) {
                $tmpInicio = 0;
                if ($inicio > -1) {
                    $tmpInicio = $inicio;
                }
                $select->limit($tamanho, $tmpInicio);
            }

            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            return $db->fetchAll($select);
        } catch (Exception $objException) {
            xd($objException->getMessage());
            throw new Exception($objException->getMessage(), 0, $objException);
        }
    }


    public function buscarReadequacaoCoordenadorParecerEmAnalise($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        try {
            $select = $this->select();
            $select->setIntegrityCheck(false);
            $select->from(
                array('tbDistribuirReadequacao' => $this->_name),
                new Zend_Db_Expr("
                tbDistribuirReadequacao.idDistribuirReadequacao,
                projetos.idPRONAC as idPronac,
                tbReadequacao.idReadequacao,
                tbReadequacao.idTipoReadequacao,
                CAST(tbReadequacao.dsJustificativa AS TEXT) AS dsJustificativa,
                CAST(tbReadequacao.dsAvaliacao AS TEXT) AS dsAvaliacao,
                CAST(tbReadequacao.dsSolicitacao AS TEXT) AS dsSolicitacao,
                projetos.AnoProjeto+projetos.Sequencial AS PRONAC,
                projetos.NomeProjeto,
                projetos.Area,
                projetos.Segmento,
                tbTipoReadequacao.dsReadequacao as tpReadequacao,
                tbDistribuirReadequacao.dtEnvioAvaliador as dtDistribuicao,
                tbDistribuirReadequacao.dtRetornoAvaliador as dtDevolucao,
                DATEDIFF(DAY, tbDistribuirReadequacao.dtEnvioAvaliador, GETDATE()) as qtDiasEmAnalise,
                tbDistribuirReadequacao.idAvaliador,
                usuarios.usu_nome AS nmParecerista,
                tbDistribuirReadequacao.idUnidade as idOrgao,
                CAST(dsOrientacao AS TEXT) AS dsOrientacao
            ")
            );
            $select->joinInner(
                array('tbReadequacao' => 'tbReadequacao'),
                'tbDistribuirReadequacao.idReadequacao = tbReadequacao.idReadequacao',
                ['siEncaminhamento' => 'tbReadequacao.siEncaminhamento'],
                $this->_schema
            );
            $select->joinInner(
                array('projetos' => 'Projetos'),
                'projetos.IdPRONAC = tbReadequacao.IdPRONAC',
                array(''),
                $this->_schema
            );

            $select->joinInner(
                array('usuarios' => 'Usuarios'),
                'tbDistribuirReadequacao.idAvaliador = usuarios.usu_codigo',
                ['usu_nome AS dsNomeAvaliador'],
                $this->getSchema('Tabelas')
            );

            $select->joinInner(
                ['nomes' => 'Nomes'],
                'nomes.idAgente = tbReadequacao.idSolicitante',
                ['Descricao AS dsNomeSolicitante'],
                $this->getSchema('agentes')
            );
            
            $select->joinInner(
                array('tbTipoReadequacao' => 'tbTipoReadequacao'),
                'tbTipoReadequacao.idTipoReadequacao = tbReadequacao.idTipoReadequacao',
                array(''),
                $this->_schema
            );

            $select->where('tbReadequacao.stEstado = ? ', 0);
            $select->where('tbDistribuirReadequacao.stValidacaoCoordenador = ? ', 0);
            $select->where('tbReadequacao.siEncaminhamento = ? ', Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_ANALISE_TECNICA);

            foreach ($where as $coluna => $valor) {
                $select->where($coluna, $valor);
            }

            $select->order($order);

            if ($tamanho > -1) {
                $tmpInicio = 0;
                if ($inicio > -1) {
                    $tmpInicio = $inicio;
                }
                $select->limit($tamanho, $tmpInicio);
            }

            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            return $db->fetchAll($select);
        } catch (Exception $objException) {
            xd($objException->getMessage());
            throw new Exception($objException->getMessage(), 0, $objException);
        }
    }
    
    
    public function buscarReadequacaoCoordenadorParecerAnalisados($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        try {
            $select = $this->select();
            $select->setIntegrityCheck(false);
            $select->from(
                array('tbDistribuirReadequacao' => $this->_name),
                new Zend_Db_Expr("
                tbDistribuirReadequacao.idDistribuirReadequacao,
                projetos.idPRONAC,
                projetos.AnoProjeto+projetos.Sequencial AS PRONAC,
                projetos.NomeProjeto,
                projetos.Area,
                projetos.Segmento,
                tbReadequacao.idAvaliador,
                CAST(tbReadequacao.dsSolicitacao AS TEXT) AS dsSolicitacao,
                CAST(tbReadequacao.dsJustificativa AS TEXT) AS dsJustificativa,
                CAST(tbReadequacao.dsAvaliacao AS TEXT) AS dsAvaliacao,
                tbTipoReadequacao.dsReadequacao as tpReadequacao,
                tbDistribuirReadequacao.dtEncaminhamento as dtEnvio,
                tbDistribuirReadequacao.dtEnvioAvaliador as dtDistribuicao,
                tbDistribuirReadequacao.dtRetornoAvaliador as dtDevolucao,
                DATEDIFF(DAY,
                tbDistribuirReadequacao.dtEncaminhamento,
                tbDistribuirReadequacao.dtEnvioAvaliador) as qtDiasDistribuir,
                DATEDIFF(DAY,
                tbDistribuirReadequacao.dtEnvioAvaliador,
                tbDistribuirReadequacao.dtRetornoAvaliador) as qtDiasAvaliar,
                DATEDIFF(DAY,
                tbDistribuirReadequacao.dtEncaminhamento,
                tbDistribuirReadequacao.dtEnvioAvaliador) + DATEDIFF(DAY,
                tbDistribuirReadequacao.dtEnvioAvaliador,
                tbDistribuirReadequacao.dtRetornoAvaliador) + DATEDIFF(DAY,
                tbDistribuirReadequacao.dtRetornoAvaliador,
                GETDATE()) AS qtTotalDiasAvaliar,
                tbDistribuirReadequacao.idAvaliador AS idTecnico,
                dsOrientacao,
                tbReadequacao.idReadequacao,
                tbReadequacao.idTipoReadequacao,
                tbDistribuirReadequacao.idUnidade as idOrgao,
                (CASE
                    WHEN tbReadequacao.siEncaminhamento = 24
                        THEN 'Encaminhado para Assinatura do presidente'
                    ELSE
                        usuarios.usu_nome 
                 END) AS nmParecerista,
                (CASE
                    WHEN tbReadequacao.siEncaminhamento = 24
                        THEN 'Encaminhado para Assinatura do presidente'
                    ELSE
                        usuarios.usu_nome 
                 END) AS nmTecnicoParecerista
            ")
            );
            $select->joinInner(
                array('tbReadequacao' => 'tbReadequacao'),
                'tbDistribuirReadequacao.idReadequacao = tbReadequacao.idReadequacao',
                ['siEncaminhamento' => 'tbReadequacao.siEncaminhamento'],
                $this->_schema
            );
            $select->joinInner(
                array('projetos' => 'Projetos'),
                'projetos.IdPRONAC = tbReadequacao.IdPRONAC',
                array(''),
                $this->_schema
            );

            $select->joinInner(
                array('usuarios' => 'Usuarios'),
                'tbDistribuirReadequacao.idAvaliador = usuarios.usu_codigo',
                ['usu_nome AS dsNomeAvaliador'],
                $this->getSchema('Tabelas')
            );

            $select->joinInner(
                ['nomes' => 'Nomes'],
                'nomes.idAgente = tbReadequacao.idSolicitante',
                ['Descricao AS dsNomeSolicitante'],
                $this->getSchema('agentes')
            );
            
            $select->joinInner(
                array('tbTipoReadequacao' => 'tbTipoReadequacao'),
                'tbTipoReadequacao.idTipoReadequacao = tbReadequacao.idTipoReadequacao',
                array(''),
                $this->_schema
            );

            $select->joinLeft(
                ['tbReadequacaoXParecer' => 'tbReadequacaoXParecer'],
                "tbReadequacaoXParecer.idReadequacao = tbReadequacao.idReadequacao",
                [],
                $this->_schema
            );
            
            $select->joinLeft(
                ['tbDocumentoAssinatura' => 'tbDocumentoAssinatura'],
                "tbDocumentoAssinatura.idAtoDeGestao = tbReadequacaoXParecer.idParecer
                AND tbDocumentoAssinatura.cdSituacao = " . \Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA . "
                AND tbDocumentoAssinatura.stEstado = " . \Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO,
                ["idDocumentoAssinatura", "idTipoDoAtoAdministrativo"],
                $this->_schema
            );
            
            $select->where('tbReadequacao.stEstado = ? ', 0);
            $select->where('tbDistribuirReadequacao.stValidacaoCoordenador = ? ', 0);
            $select->where('tbReadequacao.siEncaminhamento IN (?) ', [
                Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_DEVOLVIDO_ANALISE_TECNICA,
                Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_PRESIDENTE_DA_VINCULADA,
                Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_DE_PARECER_PELO_PRESIDENTE,
            ]);

            foreach ($where as $coluna => $valor) {
                $select->where($coluna, $valor);
            }

            $select->order($order);

            if ($tamanho > -1) {
                $tmpInicio = 0;
                if ($inicio > -1) {
                    $tmpInicio = $inicio;
                }
                $select->limit($tamanho, $tmpInicio);
            }

            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            return $db->fetchAll($select);
        } catch (Exception $objException) {
            xd($objException->getMessage());
            throw new Exception($objException->getMessage(), 0, $objException);
        }
    }
}
