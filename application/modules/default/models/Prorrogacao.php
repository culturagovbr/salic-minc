<?php
/**
 * Description of Prorrogacao
 *
 * @author Danilo Lisboa
 */
class Prorrogacao extends MinC_Db_Table_Abstract
{
    protected $_banco   = 'SAC';
    protected $_name    = 'prorrogacao';


    public function buscar($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slctQtdeMeses = "(SELECT DATEDIFF(MONTH,(SELECT max(DtRecibo) FROM SAC.dbo.Captacao WHERE Anoprojeto = pr.AnoProjeto and Sequencial = pr.Sequencial), getdate()) as qtdeMeses WHERE (SELECT max(DtRecibo) FROM SAC.dbo.Captacao WHERE Anoprojeto = pr.AnoProjeto and Sequencial = pr.Sequencial)<>'1900-01-01 00:00:00.000')";
        $slct->from(
                array('pr'=>$this->_name),
                array("idProrrogacao", "AnoProjeto", "Sequencial", "Atendimento", "DtInicio"=>"CONVERT(CHAR(20),DtInicio, 120)", "DtFinal"=>"CONVERT(CHAR(20),DtFinal, 120)", "DtPedido"=>"CONVERT(CHAR(20),DtPedido, 120)", "qtdeMeses"=>new Zend_Db_Expr($slctQtdeMeses), "Percentual"=>new Zend_Db_Expr("SAC.dbo.fnPercentualCaptado(pr.AnoProjeto, pr.Sequencial)"))
        );
        $slct->joinInner(
                array('p'=>'projetos'),
                'pr.AnoProjeto+pr.Sequencial=p.AnoProjeto+p.Sequencial',
                array("NomeProjeto")
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        //adicionando linha order ao select
        $slct->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }

        
        return $this->fetchAll($slct);
    }

    public function pegaTotal($where=array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array('pr'=>$this->_name),
                array("total"=>"count(*)")
        );
        $slct->joinInner(
                array('p'=>'projetos'),
                'pr.AnoProjeto+pr.Sequencial=p.AnoProjeto+p.Sequencial',
                array()
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        
        return $this->fetchAll($slct)->current();
    }

    public function buscarDadosProrrogacao($idProrrogacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array($this->_name)
        );
        $select->where('idProrrogacao = ?', $idProrrogacao);

        
        return $this->fetchRow($select);
    }

    public function buscarProrrogacoes($idPronac)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array('a'=>$this->_name),
                array('DtPedido','DtInicio','DtFinal','Observacao','Atendimento',
                    new Zend_Db_Expr("
                        CASE
                            WHEN Atendimento ='A'
                                THEN 'Em an&aacute;lise'
                            WHEN Atendimento ='N'
                                THEN 'Deferido'
                            WHEN Atendimento ='I'
                                THEN 'Indeferido'
                            WHEN Atendimento ='S'
                                THEN 'Processado'
                            END as Estado
                        ")
                    )
        );
        $slct->joinLeft(
                array('b'=>'Usuarios'),
                'a.Logon = b.usu_codigo',
                array('usu_nome as Usuario'),
            'TABELAS.dbo'
        );
        $slct->where('idPronac = ?', $idPronac);

        
        return $this->fetchAll($slct);
    }
}
