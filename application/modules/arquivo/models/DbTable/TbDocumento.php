<?php

class Arquivo_Model_DbTable_TbDocumento extends MinC_Db_Table_Abstract
{
    protected $_schema = "BDCORPORATIVO.scCorp";
    protected $_name = "tbDocumento";
    protected $_primary = "idDocumento";


    /**
     * Metodo para cadastrar
     * @access public
     * @param array $dados
     * @return integer (retorna o ultimo id cadastrado)
     */
    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    } // fecha metodo cadastrarDados()


    /**
     * Grava registro. Se seja passado um ID ele altera um registro existente
     * @param array $dados - array com dados referentes as colunas da tabela no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @return ID do registro inserido/alterado ou FALSE em caso de erro
     */
    public function ultimodocumento($where = array())
    {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from($this);

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }


        //adicionando linha order ao select
        $slct->order('idDocumento desc');
        $slct->limit('1', '0');


        return $this->fetchRow($slct);
    }

    /**
     * Metodo para abrir um arquivo
     * @param $idDocumento
     * @return array
     */
    public function abrir($idDocumento)
    {
//        $table = Zend_Db_Table::getDefaultAdapter();
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('d' => $this->_name),
            array(
                'd.idDocumento',
                'd.dsDocumento',
                'd.nmTitulo'
            )
        );

        $select->joinInner(
            'tbArquivoImagem',
            'tbArquivoImagem.idArquivo = d.idArquivo',
            array('biArquivo'),
            $this->_schema
        );

        $select->joinInner(
            'tbArquivo',
            'tbArquivo.idArquivo = d.idArquivo',
            array('dsTipoPadronizado', 'nmArquivo'),
            $this->_schema);

        $select->where('d.idDocumento = ?', $idDocumento);

        $db->fetchAll('SET TEXTSIZE 2147483647');
        return $db->fetchAll($select);
    }

    public function buscarDocumento($idDocumento = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('d' => $this->_name),
            array(
                'd.idDocumento',
                'd.dsDocumento',
                'd.nmTitulo'
            )
        );
        $select->joinInner(
            array('a' => 'tbArquivo'),
            'd.idArquivo = a.idArquivo',
            array(
                'a.idArquivo',
                'a.nmArquivo',
                'a.dtEnvio',
                'a.nrTamanho'
            ),
            $this->_schema
        );
        $select->joinInner(
            array('td' => 'tbTipoDocumento'),
            'td.idTipoDocumento = d.idTipoDocumento',
            array(
                'td.dsTipoDocumento'
            ),
            $this->_schema //@todo existe um tbTipoDocumento no esquema sac
        );
        if ($idDocumento) {
            $select->where('d.idDocumento = ?', $idDocumento);
        }

        return $this->fetchRow($select);
    }

    public function excluir($where)
    {
        return $this->delete($where);
    }

    public function inserirDocumento($arquivo, $imagem, $documento)
    {
        $schemaTbArquivo = $this->_schema . '.tbArquivo';

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $idDocumento = 0;

        try {
            $db->insert($schemaTbArquivo, $arquivo);
            $idArquivo = $db->lastInsertId();

            if ($idArquivo) {

                $schemaTbArquivoImagem = $this->_schema . '.tbArquivoImagem';
                $imagem['idArquivo'] = $idArquivo;
                $db->insert($schemaTbArquivoImagem, $imagem);

                $schemaTbDocumento = $this->_schema . '.' . $this->_name;
                $documento['idArquivo'] = $idArquivo;
                $db->insert($schemaTbDocumento, $documento);
                $idDocumento = $db->lastInsertId();
            }
            $db->commit();
            return $idDocumento;
        } catch (Zend_Exception_Db $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function excluirDocumento($idDocumento)
    {

        $tbDocumento = new Arquivo_Model_DbTable_TbDocumento();
        $dadosArquivo = $tbDocumento->buscar(array('idDocumento =?' => $idDocumento))->current();

        if ($dadosArquivo) {

            $tbDocumento->delete("idArquivo = {$dadosArquivo->idArquivo} and idDocumento= {$idDocumento} ");

            $tbArquivoImagem = new Arquivo_Model_DbTable_TbArquivoImagem();
            $tbArquivoImagem->delete("idArquivo =  {$dadosArquivo->idArquivo} ");

            $tbArquivo = new Arquivo_Model_DbTable_TbArquivo();
            $tbArquivo->delete("idArquivo = {$dadosArquivo->idArquivo} ");
        }
    }
}