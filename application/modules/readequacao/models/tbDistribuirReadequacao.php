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
                array('a' => $this->_name),
                new Zend_Db_Expr("
                    a.idDistribuirReadequacao,
                    c.idPRONAC,
                    c.AnoProjeto+c.Sequencial AS PRONAC,
                    c.NomeProjeto,
                    c.Area,
                    c.Segmento,
                    e.dsReadequacao as tpReadequacao,
                    a.DtEncaminhamento AS dtEnvio,
                    DATEDIFF(DAY,
                    a.dtEncaminhamento,
                    GETDATE()) as qtDiasAguardandoDistribuicao,
                    b.idReadequacao,
                    a.idUnidade as idOrgao
            ")
            );
            $select->joinInner(
                array('b' => 'tbReadequacao'),
                'a.idReadequacao = b.idReadequacao ',
                array(''),
                $this->_schema
            );
            $select->joinInner(
                array('c' => 'Projetos'),
                'c.IdPRONAC = b.IdPRONAC ',
                array(''),
                $this->_schema
            );
            $select->joinInner(
                array('e' => 'tbTipoReadequacao'),
                'e.idTipoReadequacao = b.idTipoReadequacao',
                array(''),
                $this->_schema
            );

            $select->where('b.stEstado = ? ', 0);
            $select->where('a.stValidacaoCoordenador = ? ', 0);
            $select->where('b.siEncaminhamento = ? ', 3);

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
                array('a' => $this->_name),
                new Zend_Db_Expr("
                a.idDistribuirReadequacao,
                c.idPRONAC,
                c.AnoProjeto+c.Sequencial AS PRONAC,
                c.NomeProjeto,
                c.Area,
                c.Segmento,
                e.dsReadequacao as tpReadequacao,
                a.dtEncaminhamento as DtEnvio,
                a.dtEnvioAvaliador as dtDistribuicao,
                a.dtRetornoAvaliador as dtDevolucao,
                DATEDIFF(DAY,
                a.dtEncaminhamento,
                a.dtEnvioAvaliador) as qtDiasDistribuir,
                DATEDIFF(DAY,
                a.dtEnvioAvaliador,
                a.dtRetornoAvaliador) as qtDiasAvaliar,
                DATEDIFF(DAY,
                a.dtEncaminhamento,
                a.dtEnvioAvaliador) + DATEDIFF(DAY,
                a.dtEnvioAvaliador,
                a.dtRetornoAvaliador) + DATEDIFF(DAY,
                a.dtRetornoAvaliador,
                GETDATE()) AS qtTotalDiasAvaliar,
                a.idAvaliador AS idTecnico,
                d.usu_nome AS nmParecerista,
                b.idReadequacao,
                a.idUnidade as idOrgao
            ")
            );
            $select->joinInner(
                array('b' => 'tbReadequacao'),
                'a.idReadequacao = b.idReadequacao',
                array(''),
                $this->_schema
            );
            $select->joinInner(
                array('c' => 'Projetos'),
                'c.IdPRONAC = b.IdPRONAC',
                array(''),
                $this->_schema
            );

            $select->joinInner(
                array('d' => 'Usuarios'),
                'a.idAvaliador = d.usu_codigo',
                array(''),
                $this->getSchema('Tabelas')
            );

            $select->joinInner(
                array('e' => 'tbTipoReadequacao'),
                'e.idTipoReadequacao = b.idTipoReadequacao',
                array(''),
                $this->_schema
            );

            $select->where('b.stEstado = ? ', 0);
            $select->where('a.stValidacaoCoordenador = ? ', 0);
            $select->where('b.siEncaminhamento = ? ', 5);

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
