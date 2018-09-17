<?php
class Orgaos extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name  = 'Orgaos';
    protected $_primary = 'Codigo';

    const ORGAO_SUPERIOR_SAV = 160;
    const ORGAO_SUPERIOR_SEFIC = 251;
    const ORGAO_SAV_CAP = 166;
    const ORGAO_SAV_CEP = 167;
    const ORGAO_SAV_DAP = 171;
    const ORGAO_SAV_SAL = 179;
    const ORGAO_SAV_DIECI = 632;
    const ORGAO_GEAAP_SUAPI_DIAAPI = 262;
    const ORGAO_SEFIC_ARQ_CGEPC = 303;
    const ORGAO_SEFIC_DIC = 341;
    const ORGAO_GEAR_SACAV = 272;

    const ORGAO_IPHAN_PRONAC = 91;
    const ORGAO_FUNARTE = 92;
    const ORGAO_FBN = 93;
    const ORGAO_FCP = 94;
    const ORGAO_FCRB = 95;
    const ORGAO_IBRAM = 335;

    const SAV_DPAV = 682;
    const SEFIC_DEIPC = 341;

    public function pesquisarTodosOrgaos()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->distinct();
        $select->from(
                array('o'=>$this->_name),
                array(
                'o.Codigo',
                new Zend_Db_Expr('Tabelas.dbo.fnEstruturaOrgao(o.codigo, 0) as Sigla'),
                'org.org_nomeautorizado'
                )
        );
        $select->joinInner(
                array('org'=>'vwUsuariosOrgaosGrupos'),
                'org.uog_orgao = o.Codigo ',
                array('org.org_nomeautorizado'),
                'Tabelas.dbo'
        );
        $select->where('o.Status = ?', 0);
        $select->where(new Zend_Db_Expr('o.idSecretaria IS NOT NULL'));
        $select->order('2');

        return $this->fetchAll($select);
    }

    public function pesquisarUnidades($where = [])
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->distinct();
        $select->from(
            array('o'=>$this->_name),
            array(
                'o.Codigo',
                'o.Sigla',
                'o.Sigla as novaSigla',
                // new Zend_Db_Expr('Tabelas.dbo.fnEstruturaOrgao(o.codigo, 0) as novaSigla'),
            )
        );
        $select->where('o.Status = ?', 0);
        $select->where(new Zend_Db_Expr('o.idSecretaria IS NOT NULL'));

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        $select->order('3');

        return $this->fetchAll($select);
    }

    public function pesquisarNomeOrgao($codOrgao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('o'=>$this->_name),
                        array(
                                'o.Codigo',
                                'o.Sigla as NomeOrgao'
                             )
                     );
        $select->where("o.Codigo = ?", $codOrgao);

        return $this->fetchAll($select);
    }

    public function codigoOrgaoSuperior($codOrgao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('o'=>$this->_name),
                      array('o.Codigo',
                            'o.Sigla',
                            'o.idSecretaria as Superior')
        );

        $select->where("o.Codigo = ?", $codOrgao);

        return $this->fetchAll($select);
    }


    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
//    public function buscarOrgaoPorSegmento($area, $segmento)
//    {
//        $slct = $this->select();
//        $slct->setIntegrityCheck(false);
//        $slct->from(
//                    array("o"=>$this->_name),
//                    array("Codigo", "Sigla")
//                    );
//        $slct->joinInner(
//                        array("se"=>"Segmento"),
//                        "o.Codigo = se.idOrgao",
//                        array(),
//                        "SAC.dbo"
//                        );
//        $slct->joinInner(
//                        array("a"=>"Area"),
//                        "a.Codigo = substring(se.Codigo, 1, 1)",
//                        array(),
//                        "SAC.dbo"
//                        );
//
//        //adiciona quantos filtros foram enviados
//        $slct->where("se.stEstado = ?", 1);
//        $slct->where("se.Codigo = ?", $area);
//        $slct->orWhere("se.Codigo = ?", $segmento);
//
//        return $this->fetchAll($slct);
//    }

    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscarOrgaoPorSegmento($segmento)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                    array("o"=>$this->_name),
                    array("Codigo", "Sigla")
                    );
        $slct->joinInner(
                        array("se"=>"Segmento"),
                        "o.Codigo = se.idOrgao",
                        array(),
                        "SAC.dbo"
                        );

        //adiciona quantos filtros foram enviados
        //$slct->where("se.stEstado = ?", 1);
        $slct->where("se.Codigo = ?", $segmento);
        
        return $this->fetchAll($slct);
    }

    public function obterOrgaoSuperior($codOrgao)
    {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);

        $objQuery->from(
            array('OrgaoSuperior' => $this->_name),
            'OrgaoSuperior.*',
            $this->_schema
        );

        $objQuery->joinInner(
            array('OrgaoFilho' => $this->_name),
            'OrgaoFilho.idSecretaria = OrgaoSuperior.Codigo',
            array(),
            $this->_schema
        );

        $objQuery->where("OrgaoFilho.Codigo = ?", $codOrgao);
        $resultado = $this->fetchRow($objQuery);
        if ($resultado) {
            return $resultado->toArray();
        }
    }


    /*
     * Busca superintendências do IPHAN
     */
    public function buscarSuperintendencias()
    {
        $query = $this->select()
               ->from(
                   $this,
                      array('Codigo', 'Sigla')
               );

        $query->where('Vinculo = 1');
        $query->where('idSecretaria = ' . Orgaos::ORGAO_IPHAN_PRONAC);
        $query->order('Sigla');
        
        return $this->fetchAll($query);
    }

    public function isVinculadaIphan($idOrgao)
    {
        $orgaos = array(
            self::ORGAO_IPHAN_PRONAC,
            self::ORGAO_FUNARTE,
            self::ORGAO_FBN,
            self::ORGAO_FCP,
            self::ORGAO_FCRB,
            self::ORGAO_IBRAM,
            self::ORGAO_SUPERIOR_SAV,
            self::ORGAO_SAV_DAP
        );
        
        return (!in_array($idOrgao, $orgaos)) ? true : false;
    }

    public function obterAreaParaEncaminhamentoPrestacao($codOrgao){
        $idOrgaoDestino = $codOrgao;
        $where = array();

        if ($idOrgaoDestino == '177' || $idOrgaoDestino == '12') {
            $where['Codigo = ?'] = $idOrgaoDestino;
        } else {
            $where['Vinculo = 1 OR Codigo = (' . $idOrgaoDestino . ')'] = '?';
        }
        $where['Codigo in (12,167,177,270,303)'] = '?';

        return $this->buscar($where, array('Sigla'))->current();
    }
}
