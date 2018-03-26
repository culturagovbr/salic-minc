<?php
/**
 * @author jefferson.silva
 * @since 16/09/2013
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class tbDistribuirProjeto extends MinC_Db_Table_Abstract
{
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "tbDistribuirProjeto";
    protected $_primary   = "idDistribuirProjeto";

    public function painelRecursos($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $qtdeTotal=false, $idPerfil=0)
    {
        if ($idPerfil == 110) {
            $nome = 'c.usu_nome AS Parecerista';
        } else {
            $nome = 'c.Descricao AS Parecerista';
        }

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr("a.idDistribuirProjeto, a.IdPRONAC, b.AnoProjeto+b.Sequencial AS Pronac, b.NomeProjeto, a.dtEnvio, $nome, a.idAvaliador AS idAgente, CAST(d.dsSolicitacaoRecurso as TEXT) AS dsSolicitacaoRecurso, d.idRecurso")
            )
        );

        $select->joinInner(
            array('b' => 'Projetos'),
            'a.IdPRONAC = b.IdPRONAC',
            array(''),
            'SAC.dbo'
        );

        if ($idPerfil == 110) {
            $select->joinLeft(
                array('c' => 'Usuarios'),
                'a.idAvaliador = c.usu_codigo',
                array(''),
                'TABELAS.dbo'
            );
        } else {
            $select->joinLeft(
                array('c' => 'Nomes'),
                'a.idAvaliador = c.idAgente',
                array(''),
                'AGENTES.dbo'
            );
        }
        $select->joinInner(
            array('d' => 'tbRecurso'),
            'a.IdPRONAC = d.IdPRONAC',
            array(''),
            'SAC.dbo'
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if ($qtdeTotal) {
            return $this->fetchAll($select)->count();
        }

        //adicionando linha order ao select
        $select->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }

        
        return $this->fetchAll($select);
    }
}
