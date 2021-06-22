<?php
/**
 * Created by IntelliJ IDEA.
 * User: voltAir
 * Date: 25/07/18
 * Time: 15:58
 */

class PrestacaoContas_Model_DbTable_Enderecos extends MinC_Db_Table_Abstract
{

    protected $_schema = 'bddne.scdne';
    /** @var array
     *  @_names(array) = Tabelas de interação com a base CEP dos correios
     *  para localidades via CEP, UF, CIDADE, LOGRADOURO, BAIRRO...
     */
    protected $_name = 'tbFaixaCepUf';

    public function ufRetorno(){

        $objQuery = $this->select()
         ->from($this);

        return $this->_db->fetchAll($objQuery);
    }

    public function cidadesRetorno(){
        $this->setName('tbLocalidade') ;
        $objQuery = $this->select()
            ->from($this);

        return $this->_db->fetchAll($objQuery);

    }

    public function buscarCep($cep){

        $objQuery = $this->select()
                ->setIntegrityCheck(false)
                ->from(['logradouro'=>'tbLogradouroUf'], '*')
                ->joinLeft(['bairro' =>'tbBairro'],'logradouro.nrInicioBairro = bairro.nrBairro', '*',$this->_schema)
                ->joinLeft(['cidade' => 'tbLocalidade'], 'logradouro.nrLocalidade = cidade.nrLocalidade','*',$this->_schema)
                ->where('logradouro.cdCep = ?', $cep);


        return $this->_db->fetchAll($objQuery);
    }
}