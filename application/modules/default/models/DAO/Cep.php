<?php
/**
 * Modelo Cep
 * @author Equipe RUP - Politec
 * @author wouerner <wouerner@gmail.com>
 * @since 29/03/2010
 * @subpackage application.models
 * @link http://www.cultura.gov.br
 *
 * @todo model nao funciona com o $this, pois o zend_db nao esta reconhecendo a view para mapeamento. Verificar futuramente.
 */
class Cep extends MinC_Db_Table_Abstract
{

//    protected $_schema = 'bddne.scdne';
    protected $_schema = 'bddne.scdne';
    protected $_name = 'vw_endereco';

    public function __construct()
    {
        parent::__construct();
    }

    public function init()
    {
        parent::init();
    }

    /**
     * Buscar o cep no banco de dados
     * @access public
     * @static
     * @param integer $cep
     * @return string $retorno
     */
    public static function buscarCepDB($cep)
    {
        $sql = "SELECT CEP,
            logradouro,
            tipo_logradouro,
            bairro,
            cidade,
            uf,
            idCidadeMunicipios,
            dsCidadeMunicipios,
            idCidadeUF,
            DSCIDADEMUNICIPIOS AS dsCidadeUF
            FROM BDDNE.scDNE.VW_ENDERECO
            WHERE CEP = '$cep'";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);

        return $db->fetchRow($sql);
    }

    /**
     *
     * @name buscarCEP
     * @param $cep
     * @return null|Zend_Db_Table_Row_Abstract
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  ${DATE}
     *
     * @todo verificar por que o zend nao reconhece uma view para o mapeamento.
     */
    public function buscarCEP($cep)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);

        $cols = array(
            'cep',
            'logradouro',
            'tipo_logradouro',
            'bairro',
            'cidade',
            'uf',
            'idcidademunicipios',
            'dscidademunicipios',
            'idcidadeuf',
            'dscidademunicipios as dscidadeuf'
        );

        $sql = $db->select()
            ->from($this->_name, $cols, $this->_schema)
            ->where('cep = ?', $cep);
        return $db->fetchRow($sql);
    }
}
