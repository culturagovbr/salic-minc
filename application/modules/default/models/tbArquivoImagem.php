<?php
/**
 * DAO tbArquivoImagem
 * @author emanuel.sampaio - Politec
 * @since 19/02/2011
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class tbArquivoImagem extends MinC_Db_Table_Abstract
{
    protected $_banco  = "BDCORPORATIVO";
    protected $_schema = "BDCORPORATIVO.scCorp";
    protected $_name   = "tbArquivoImagem";


    /**
     * M�todo para buscar um arquivo bin�rio pelo seu id
     * @access public
     * @param integer $idArquivo
     * @return array
     */
    public function buscarDados($idArquivo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this);

        $select->where("idArquivo = ?", $idArquivo);

        return $this->fetchRow($select);
    } // fecha m�todo buscarDados()


    /**
     * M�todo para cadastrar
     * @access public
     * @param array $dados
     * @return integer (retorna o �ltimo id cadastrado)
     */
    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    } // fecha m�todo cadastrarDados()

    
    
    public function salvarDados($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $db->query($dados);
        return $db->lastInsertId();
    }


    /**
     * M�todo para alterar
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function alterarDados($dados, $where)
    {
        $where = "idArquivo = " . $where;
        return $this->update($dados, $where);
    } // fecha m�todo alterarDados()


    /**
     * M�todo para excluir
     * @access public
     * @param integer $where
     * @return integer (quantidade de registros exclu�dos)
     */
    public function excluirDados($where)
    {
        $where = "idArquivo = " . $where;
        return $this->delete($where);
    } // fecha m�todo excluirDados()


    public function buscarArquivoMarca($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();
            
        $slct->setIntegrityCheck(false);

        $slct->from(
                        array('ai' => $this->_name),
                        array("dtEnvioForm"=>"CONVERT(CHAR(10),dtEnvio,103)"),
                              "BDCORPORATIVO.scCorp"
            );

        $slct->joinInner(
                            array('a'=>'tbArquivo'),
                            "ai.idArquivo = a.idArquivo",
                            array("a.idArquivo",
                                   "a.nmArquivo",
                                   "a.dtEnvio",
                                   "a.nrTamanho",
                                   "a.stAtivo",
                                   "a.sgExtensao"),
                            "BDCORPORATIVO.scCorp"
                          );

        $slct->joinInner(
                            array('d'=>'tbDocumento'),
                            "a.idArquivo = d.idArquivo",
                            array("d.idDocumento",
                                  "CAST(d.dsDocumento AS TEXT) AS dsDocumento"),
                            "BDCORPORATIVO.scCorp"
                          );

        $slct->joinLeft(
                            array('dp'=>'tbDocumentoProjeto'),
                            "d.idTipoDocumento = dp.idTipoDocumento AND d.idDocumento = dp.idDocumento ",
                            array("dp.idPronac", "dp.stAtivoDocumentoProjeto"),
                            "BDCORPORATIVO.scCorp"
                          );

        $slct->joinLeft(
                            array('dpp'=>'tbDocumentoProposta'),
                            "d.idTipoDocumento = dpp.idTipoDocumento AND d.idDocumento = dpp.idDocumento ",
                            array("dpp.idProposta"),
                            "BDCORPORATIVO.scCorp"
                          );

        $slct->joinLeft(
                            array('proj'=>'Projetos'),
                            "proj.IdPRONAC = dp.idPronac OR proj.idProjeto = dpp.idProposta",
                            array("proj.AnoProjeto",
                                  "proj.Sequencial"),
                            "SAC.dbo"
                          );
            
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna=>$valor) {
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

    public function excluir($where)
    {
        return $this->delete($where);
    }


    public function listarMarcasAcompanhamento($orgao)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                        array('ai' => $this->_name),
                        array(),
                "BDCORPORATIVO.scCorp"
                    );
        $slct->joinInner(
                            array('a'=>'tbArquivo'),
                "ai.idArquivo = a.idArquivo",
                            array('idArquivo', 'nmArquivo', 'dtEnvio', 'nrTamanho'),
                            "BDCORPORATIVO.scCorp"
                          );
        $slct->joinInner(
                            array('d'=>'tbDocumento'),
                "a.idArquivo = d.idArquivo",
                            array('idDocumento', 'CAST(dsDocumento AS TEXT) AS dsDocumento'),
                "BDCORPORATIVO.scCorp"
                          );
        $slct->joinInner(
                            array('dp'=>'tbDocumentoProjeto'),
                "dp.idDocumento = d.idDocumento",
                            array(),
                "BDCORPORATIVO.scCorp"
                          );
        $slct->joinInner(
                            array('p'=>'Projetos'),
                "dp.idPronac = p.IdPRONAC",
                            array('idPronac', new Zend_Db_Expr('p.AnoProjeto + p.Sequencial as Pronac'), 'NomeProjeto'),
                "SAC.dbo"
                          );

        $slct->where('dp.stAtivoDocumentoProjeto = ?', 'E');
        $slct->where('p.Orgao = ?', $orgao);
        $slct->order(new Zend_Db_Expr('p.AnoProjeto + p.Sequencial'));
            

        return $this->fetchAll($slct);
    }

    public function listarMarcasAcompanhamentoArea($whereArea)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                        array('ai' => $this->_name),
                        array(),
                "BDCORPORATIVO.scCorp"
                    );
        $slct->joinInner(
                            array('a'=>'tbArquivo'),
                "ai.idArquivo = a.idArquivo",
                            array('idArquivo', 'nmArquivo', 'dtEnvio', 'nrTamanho'),
                            "BDCORPORATIVO.scCorp"
                          );
        $slct->joinInner(
                            array('d'=>'tbDocumento'),
                "a.idArquivo = d.idArquivo",
                            array('idDocumento', 'CAST(dsDocumento AS TEXT) AS dsDocumento'),
                "BDCORPORATIVO.scCorp"
                          );
        $slct->joinInner(
                            array('dp'=>'tbDocumentoProjeto'),
                "dp.idDocumento = d.idDocumento",
                            array(),
                "BDCORPORATIVO.scCorp"
                          );
        $slct->joinInner(
                            array('p'=>'Projetos'),
                "dp.idPronac = p.IdPRONAC",
                            array('idPronac', new Zend_Db_Expr('p.AnoProjeto + p.Sequencial as Pronac'), 'NomeProjeto'),
                "SAC.dbo"
                          );

        $slct->where('dp.stAtivoDocumentoProjeto = ?', 'E');
        $slct->where('dp.idTipoDocumento = ?', 1); // 1 = Marcas
        $slct->where("$whereArea");
        $slct->order(new Zend_Db_Expr('p.AnoProjeto + p.Sequencial'));


        return $this->fetchAll($slct);
    }

    public function marcasAnexadas($pronac)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                        array('ai' => $this->_name),
                        array(),
                "BDCORPORATIVO.scCorp"
                    );
        $slct->joinInner(
                            array('a'=>'tbArquivo'),
                "ai.idArquivo = a.idArquivo",
                            array('idArquivo', 'nmArquivo', 'dtEnvio', 'nrTamanho'),
                            "BDCORPORATIVO.scCorp"
                          );
        $slct->joinInner(
                            array('d'=>'tbDocumento'),
                "a.idArquivo = d.idArquivo",
                            array('idDocumento', 'CAST(dsDocumento AS TEXT) AS dsDocumento'),
                "BDCORPORATIVO.scCorp"
                          );
        $slct->joinInner(
                            array('dp'=>'tbDocumentoProjeto'),
                "dp.idDocumento = d.idDocumento",
                            array('stAtivoDocumentoProjeto'),
                "BDCORPORATIVO.scCorp"
                          );
        $slct->joinInner(
                            array('p'=>'Projetos'),
                "dp.idPronac = p.IdPRONAC",
                            array('idPronac', new Zend_Db_Expr('p.AnoProjeto + p.Sequencial as Pronac'), 'NomeProjeto'),
                "SAC.dbo"
                          );

        $slct->where('dp.idTipoDocumento = ?', 1);
        $slct->where('p.AnoProjeto + p.Sequencial = ?', $pronac);

        return $this->fetchAll($slct);
    }
} // fecha class
