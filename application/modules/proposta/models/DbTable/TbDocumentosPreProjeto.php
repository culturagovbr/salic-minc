<?php

/**
 * Class Proposta_Model_DbTable_TbDocumentosPreProjeto
 *
 * @name Proposta_Model_DbTable_TbDocumentosPreProjeto
 * @package Modules/Agente
 * @subpackage Models/DbTable
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 26/09/2016
 *
 * @link http://salic.cultura.gov.br
 */
class Proposta_Model_DbTable_TbDocumentosPreProjeto extends MinC_Db_Table_Abstract
{
    protected $_banco   = "sac";
    protected $_schema  = "sac";
    protected $_name = 'tbdocumentospreprojeto';
    protected $_primary = 'idDocumentosPreprojetos';


    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscarDocumentos($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array("a"=>$this->_name),
            array("codigodocumento", new Zend_Db_Expr('(2) as tpdoc'), 'idprojeto as codigo',
                'data', 'imdocumento', 'noarquivo', 'taarquivo', 'iddocumentospreprojetos'),
            $this->_schema
        );
        $slct->joinInner(
            array("b"=> "documentosexigidos"),
            "a.codigodocumento = b.codigo",
            array("descricao"),
            $this->getSchema('sac')
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

        $result = $this->fetchAll($slct);
        return $result ? $result->toArray() : array();
    }

    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function abrir($id)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);

        $slct->from(
                array("a"=> $this->_name),
                array("noarquivo", "imdocumento"),
            $this->_schema
        );

        $slct->where("iddocumentospreprojetos = ?", $id);

        //$this->fetchAll("SET TEXTSIZE 10485760;");
        $db = $this->getDefaultAdapter();
        if ($this->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mssql) {
            $db->fetchAll("SET TEXTSIZE 10485760;");
        }

        return $this->fetchAll($slct);
    }

    public function buscarDadosDocumentos($where=array(), $order=array(), $tamanho=-1, $inicio=-1) {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array("a"=>$this->_name),
            array("CodigoDocumento", new Zend_Db_Expr('(2) as tpDoc'), 'idprojeto as Codigo',
                'Data', 'NoArquivo', 'TaArquivo', 'idDocumentosPreProjetos'),
            $this->_schema
        );
        $slct->joinInner(
            array("b"=> "documentosexigidos"), "a.codigodocumento = b.codigo",
            array("Descricao"), $this->getSchema('sac')
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

        $result = $this->fetchAll($slct);
        return $result ? $result->toArray() : array();
    }
}
