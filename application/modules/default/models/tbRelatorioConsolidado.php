<?php
/**
 * DAO tbRelatorioConsolidado
 * @since 16/03/2011
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class tbRelatorioConsolidado extends MinC_Db_Table_Abstract
{
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "tbRelatorioConsolidado";

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



    /**
     * M�todo para alterar
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function alterarDados($dados, $where)
    {
        $where = "idRelatorioConsolidado = " . $where;
        return $this->update($dados, $where);
    } // fecha m�todo alterarDados()


    /**
     * M�todo para buscar o relat�rio consolidado
     * @access public
     * @param array $dados
     * @return array dos dados cadastrados
     */
    public function buscarRelatorioPronac($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array('a' => $this->_name)
            );
        $select->joinInner(
                    array('b' => 'tbRelatorio'),
                    'b.idRelatorio = a.idRelatorio',
                    array('*'),
                    'SAC.dbo'
                    );
        $select->where('b.idPRONAC = ?', $idpronac);

        return $this->fetchAll($select);
    }


    /**
     * M�todo para consultar dados
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros)
     */
    public function consultarDados($idRelatorio)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('a'=>$this->_name),
                array('*','a.stDocumento as FNC')
        );
        $select->where("a.idRelatorio = ?", $idRelatorio);
        $select->joinInner(
                array('b' => 'tbDescricaoRelatorioConsolidado'),
                'b.idRelatorioConsolidado = a.idRelatorioConsolidado',
                array('*', 'b.dsJustificativaAcompanhamento as JustificativaAcompanhamento')
        );
        $select->joinLeft(
                array('c' => 'tbDocumentoAceitacao'),
                'c.idRelatorioConsolidado = a.idRelatorioConsolidado',
                array('*')
        );

        
        return $this->fetchAll($select);
    }


    /**
     * M�todo para consultar dados
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros)
     */
    public function consultarDados2($idRelatorio)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('a'=>$this->_name),
                array('*')
        );
        $select->where("a.idRelatorioConsolidado = ?", $idRelatorio);
        $select->joinInner(
                array('b' => 'tbDescricaoRelatorioConsolidado'),
                'b.idRelatorioConsolidado = a.idRelatorioConsolidado',
                array('*', 'b.dsJustificativaAcompanhamento as JustificativaAcompanhamento')
        );
        $select->joinLeft(
                array('c' => 'tbDocumentoAceitacao'),
                'c.idRelatorioConsolidado = a.idRelatorioConsolidado',
                array('*')
        );

        
        return $this->fetchAll($select);
    }


    public function consultarDadosPronac($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('a'=>$this->_name),
                array('*')
        );
        $select->joinInner(
                array('b' => 'tbRelatorio'),
                'b.idRelatorio = a.idRelatorio',
                array('')
        );
        $select->joinInner(
                array('c' => 'tbDocumentoAceitacao'),
                'c.idRelatorioConsolidado = a.idRelatorioConsolidado',
                array('')
        );
        $select->joinInner(
                array('d' => 'tbDocumento'),
                'd.idDocumento = c.idDocumento',
                array('idArquivo'),
                'BDCORPORATIVO.scCorp'
        );
        $select->joinInner(
                array('e' => 'tbArquivo'),
                'e.idArquivo = d.idArquivo',
                array('nmArquivo'),
                'BDCORPORATIVO.scCorp'
        );
        $select->joinLeft(
                array('f' => 'tbDescricaoRelatorioConsolidado'),
                'f.idRelatorioConsolidado = a.idRelatorioConsolidado',
                array('*')
        );
        $select->where("b.idPRONAC = ?", $idPronac);

        return $this->fetchAll($select);
    }

    public function buscardsrelatorioconsolidado($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        //adicionando linha order ao select
        $slct->order($order);

        $select->joinInner(
                array('d' => 'tb'),
                'd.idDocumento = c.idDocumento',
                array('idArquivo'),
                'BDCORPORATIVO.scCorp'
        );
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

    /**
     * M�todo para buscar o relat�rio trimestral
     * @access public
     * @param array $dados
     * @return array dos dados cadastrados
     */
    public function buscarstRelatorioPronac($idpronac, $status = 1)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array('a'=>$this->_name)
            );
        $select->joinInner(
                    array('b' => 'tbRelatorio'),
                    'b.idRelatorio = a.idRelatorio',
                    array('*'),
                    'SAC.dbo'
                    );
        $select->where('b.idPRONAC = ?', $idpronac);
        $select->where('a.stRelatorioConsolidado = ?', $status);
        $select->where('b.tpRelatorio = ?', 'C');

        return $this->fetchAll($select);
    }


    /**
     * M�todo para buscar o relat�rio trimestral
     * @access public
     * @param array $dados
     * @return array dos dados cadastrados
     */
    public function buscarRelatorioConsolidado($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array('a'=>$this->_name)
            );
        $select->joinInner(
                    array('b' => 'tbRelatorio'),
                    'b.idRelatorio = a.idRelatorio',
                    array('*'),
                    'SAC.dbo'
                    );
        $select->where('b.idPRONAC = ?', $idpronac);
        $select->where('b.tpRelatorio = ?', 'C');

        return $this->fetchAll($select);
    }

    /**
     * M�todo para liberar a finaliza��o do relat�rio consolidado
     * Se estes 3 campos estiverem preenchidos � sinal para que o toda a avalia��o do relatorio consolidado foi feita.
     * @access public
     * @param array $dados
     * @return array dos dados cadastrados
     */
    public function consultarAvaliacaoRelatorioConsolidado($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array('a'=>$this->_name),
                    array('a.stObjetivosMetas','a.stTermoProjeto','a.stProduto')
            );
        $select->joinInner(
                    array('b' => 'tbRelatorio'),
                    'b.idRelatorio = a.idRelatorio',
                    array(),
                    'SAC.dbo'
                    );
        $select->where('b.idPRONAC = ?', $idpronac);

        return $this->fetchAll($select);
    }
} // fecha class
