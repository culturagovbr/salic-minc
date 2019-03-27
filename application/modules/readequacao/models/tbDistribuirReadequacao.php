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
                    tbDistribuirReadequacao.idUnidade as idOrgao
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
                array('tbTipoReadequacao' => 'tbTipoReadequacao'),
                'tbTipoReadequacao.idTipoReadequacao = tbReadequacao.idTipoReadequacao',
                array(''),
                $this->_schema
            );

            $select->where('tbReadequacao.stEstado = ? ', 0);
            $select->where('tbDistribuirReadequacao.stValidacaoCoordenador = ? ', 0);
            $select->where('tbReadequacao.siEncaminhamento = ? ', 3);

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
                tbTipoReadequacao.dsReadequacao as tpReadequacao,
                tbDistribuirReadequacao.dtEncaminhamento as DtEnvio,
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
                usuarios.usu_nome AS nmParecerista,
                tbReadequacao.idReadequacao,
                tbDistribuirReadequacao.idUnidade as idOrgao
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
                array(''),
                $this->getSchema('Tabelas')
            );

            $select->joinInner(
                array('tbTipoReadequacao' => 'tbTipoReadequacao'),
                'tbTipoReadequacao.idTipoReadequacao = tbReadequacao.idTipoReadequacao',
                array(''),
                $this->_schema
            );

            $select->joinLeft(
                ['tbReadequacaoXParecer' => 'tbReadequacaoXParecer'],
                'tbReadequacaoXParecer.idReadequacao = tbReadequacao.idReadequacao',
                [],
                $this->_schema
            );
            
            $servicoReadequacaoAssinatura = new \Application\Modules\Readequacao\Service\Assinatura\ReadequacaoAssinatura(
                $this->grupoAtivo,
                $this->auth
            );
            
            $select->joinLeft(
                ['tbDocumentoAssinatura' => 'tbDocumentoAssinatura'],
                'tbReadequacaoXParecer.idParecer = tbDocumentoAssinatura.idAtoDeGestao AND
             tbDocumentoAssinatura.idTipoDoAtoAdministrativo IN ('.
                implode(',', $servicoReadequacaoAssinatura->obterAtosAdministativos()) .') AND
            tbDocumentoAssinatura.stEstado = ' . Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO,
                [
                    'tbDocumentoAssinatura.idDocumentoAssinatura',
                    'tbDocumentoAssinatura.idTipoDoAtoAdministrativo',
                ],
                $this->_schema
            );

            $select->where('tbReadequacao.stEstado = ? ', 0);
            $select->where('tbDistribuirReadequacao.stValidacaoCoordenador = ? ', 0);
            $select->where('tbReadequacao.siEncaminhamento = ? ', 5);

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
