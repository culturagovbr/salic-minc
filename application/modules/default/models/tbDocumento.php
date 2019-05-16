<?php
/**
 * Description of Vinculo
 *
 * @author tisomar
 */
class tbDocumento extends MinC_Db_Table_Abstract
{
    protected $_banco = "BDCORPORATIVO";
    protected $_schema = 'BDCORPORATIVO.scCorp';
    protected $_name = "tbDocumento";


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
    public function ultimodocumento($where=array())
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

    public function buscardocumentosrelatorio($idnrdocumento = null)
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
                'BDCORPORATIVO.sccorp'
        );
        $select->joinInner(
                array('td' => 'tbTipoDocumento'),
                'td.idTipoDocumento = d.idTipoDocumento',
                array(
                    'td.dsTipoDocumento'
                ),
                'SAC.dbo'
        );
        if ($idnrdocumento) {
            $select->where('d.idDocumento = ?', $idnrdocumento);
        }

        return $this->fetchAll($select);
    }

    public function excluir($where)
    {
        return $this->delete($where);
    }

    public function excluirDocumento($idDocumento)
    {
        try {
            $dadosArquivo = $this->buscar(
                [
                    'idDocumento =?'=>$idDocumento
                ])->current();
            
            if ($dadosArquivo) {
                $this->excluir("idArquivo = {$dadosArquivo->idArquivo} and idDocumento= {$idDocumento} ");
                
                $tbArquivoImagem = new tbArquivoImagem();
                $tbArquivoImagem->excluir("idArquivo = {$dadosArquivo->idArquivo} ");
                
                $tbArquivo = new tbArquivo();
                $tbArquivo->excluir("idArquivo = {$dadosArquivo->idArquivo} ");
                
                $tbDocumentoSAC = new tbDocumentoSAC();
                $tbDocumentoSAC->excluir("idDocumento =  {$idDocumento} ");
                
                return true;
            }
        } catch (Exception $objException) {
            throw $objException;
        }
    }
}
