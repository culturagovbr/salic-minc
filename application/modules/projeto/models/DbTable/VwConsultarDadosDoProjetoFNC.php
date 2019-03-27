<?php

class Projeto_Model_DbTable_VwConsultarDadosDoProjetoFNC extends MinC_Db_Table_Abstract
{
    protected $_schema = 'SAC';
    protected $_name = 'vwConsultarDadosDoProjetoFNC';
    protected $_primary = 'IdPRONAC';

    public function init()
    {
        parent::init();
    }

    public function obterDadosFnc($idPronac, array $arrayWhere = array())
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $objQuery = $db->select();

        $objQuery->from(
            array('a' => $this->_name),
            [
                'idPronac',
                'Pronac',
                'NomeProjeto',
                'CNPJ_CPF',
                'Proponente',
                'UfProjeto',
                'idMecanismo',
                'Mecanismo',
                'Area',
                'Segmento',
                'Processo',
                'DtConvenioPrimeiraVigencia',
                'DtConvenioUltimaVigencia',
                'NrConvenio',
                'DtConvenio',
                'DtConvenioPublicacao',
                'ResumoProjeto',
                'Objeto',
                'DtSituacao',
                'Situacao',
                'ProvidenciaTomada',
                'LocalizacaoAtual',
                'DtArquivamento',
                'CaixaInicio',
                'CaixaFinal',
                'SolicitadoCusteio',
                'SolicitadoCapital',
                'vlTotalSolicitado',
                'ConcedidoCusteio',
                'ConcedidoCapital',
                'Contrapartida',
                'vlTotalAprovado',
                'ValorConvenio'
            ],
            $this->_schema
        );

        $objQuery->where('idPronac = ?', $idPronac);

        if (count($arrayWhere) > 0) {
            foreach ($arrayWhere as $condicao => $valor) {
                $objQuery->where($condicao, $valor);
            }
        }

        $db->setFetchMode(Zend_DB::FETCH_ASSOC);

        return $db->fetchRow($objQuery);
    }
}
