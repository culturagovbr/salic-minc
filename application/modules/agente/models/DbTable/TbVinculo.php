<?php

/**
 * Class Agente_Model_DbTable_TbVinculo
 *
 * @name Agente_Model_DbTable_TbVinculo
 * @package Modules/Agente
 * @subpackage Models/DbTable
 * @version $Id$
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 21/09/2016
 *
 * @link http://salic.cultura.gov.br
 */
class Agente_Model_DbTable_TbVinculo extends MinC_Db_Table_Abstract{

    protected $_banco = 'agentes';
    protected $_schema = 'agentes';
    protected $_name = 'tbvinculo';
    protected $_primary = 'idVinculo';


	public function buscarVinculoProponenteResponsavel($where=array())
    {
    	$slct = $this->select();
        $slct->distinct();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array('vi' => $this->_name),
                $this->_getCols(),
                $this->_schema
        );

        $slct->joinInner(
                array('ag' => 'agentes'), "ag.idagente = vi.idagenteproponente",
                array('ag.cnpjcpf'),
                $this->_schema
        );

        $slct->joinInner(
                array('nm' => 'nomes'), "nm.idagente = ag.idagente",
                array('nm.descricao as nomeproponente'),
                $this->_schema
        );

        $slct
            ->joinLeft(
                array('sga' => 'sgcacesso'), "sga.idusuario = vi.idusuarioresponsavel",
                array('sga.idusuario as idusuarioresponsavel', 'sga.nome as nomeresponsavel', 'sga.cpf as cpfresponsavel'),
                $this->getSchema('controledeacesso')
            );

        $slct->joinLeft(
                array('vp' => 'tbvinculoproposta'), "vp.idvinculo = vi.idvinculo",
                array('vp.idvinculoproposta', 'vp.sivinculoproposta'),
                $this->_schema
        );

        foreach ($where as $coluna => $valor)
        {
            $slct->where($coluna, $valor);
        }
        $result = $this->fetchAll($slct);
        return ($result) ? $result->toArray() : array();
    }

	/* Metodo que lista os vinculos do Proponente ao Responsavel
     *
     * */
    public function buscarProponenteResponsavel($idUsuarioLogado, $mecanismo = false)
    {
        $slct1 = $this->select()
                    ->setIntegrityCheck(false)
                    ->distinct()
                    ->from(
                            array('a' => 'Projetos'),
                            array(new Zend_Db_Expr('0 as Ordem'),'CgcCpf as CNPJCPF'), $this->getSchema('sac')
                    )
                    ->joinInner(
                            array('b' => 'Agentes'), "a.CgcCpf = b.CNPJCPF",
                            array('idAgente', new Zend_Db_Expr('sac.dbo.fnNome(b.idAgente) as NomeProponente')), $this->getSchema('agentes')
                    )
                    ->joinInner(
                            array('c' => 'SGCacesso'), "a.CgcCpf = c.Cpf",
                            array(), $this->getSchema('controledeacesso')
                    )
                    ->where('c.IdUsuario = ?', $idUsuarioLogado);

        if(!empty($mecanismo)){
            $slct1->where('a.Mecanismo = ?', $mecanismo);
        }


        $slct2 = $this->select()
                    ->setIntegrityCheck(false)
                    ->distinct()
                    ->from(
                            array('a' => 'Projetos'),
                            array(new Zend_Db_Expr('1 as Ordem')), $this->getSchema('sac')
                    )
                    ->joinInner(
                            array('b' => 'Agentes'), "a.CgcCpf = b.CNPJCPF",
                            array('CNPJCPF', 'idAgente', new Zend_Db_Expr('sac.dbo.fnNome(b.idAgente) as NomeProponente')), $this->getSchema('agentes')
                    )
                    ->joinInner(
                            array('c' => 'tbProcuradorProjeto'), "a.IdPRONAC = c.idPronac",
                            array(), $this->getSchema('agentes')
                    )
                    ->joinInner(
                            array('d' => 'tbProcuracao'), "c.idProcuracao = d.idProcuracao",
                            array(), $this->getSchema('agentes')
                    )
                    ->joinInner(
                            array('f' => 'Agentes'), "d.idAgente = f.idAgente",
                            array(), $this->getSchema('agentes')
                    )
                    ->joinInner(
                            array('e' => 'SGCacesso'), "f.CNPJCPF = e.Cpf",
                            array(), $this->getSchema('controledeacesso')
                    )
                    ->where('c.siEstado = ?', 2)
                    ->where('e.IdUsuario = ?', $idUsuarioLogado);

        if(!empty($mecanismo)){
            $slct2->where('a.Mecanismo = ?', $mecanismo);
        }

        $slct3 = $this->select()
                    ->setIntegrityCheck(false)
                    ->distinct()
                    ->from(
                            array('a' => 'Projetos'),
                            array(new Zend_Db_Expr('2 as Ordem')), $this->getSchema('sac')
                    )
                    ->joinInner(
                            array('b' => 'Agentes'), "a.CgcCpf = b.CNPJCPF",
                            array('CNPJCPF', 'idAgente', new Zend_Db_Expr('sac.dbo.fnNome(b.idAgente) as NomeProponente')), $this->getSchema('agentes')
                    )
                    ->joinInner(
                            array('c' => 'Vinculacao'), "b.idAgente = c.idVinculoPrincipal",
                            array(), $this->getSchema('agentes')
                    )
                    ->joinInner(
                            array('d' => 'Agentes'), "c.idAgente = d.idAgente",
                            array(), $this->getSchema('agentes')
                    )
                    ->joinInner(
                            array('e' => 'SGCacesso'), "d.CNPJCPF = e.Cpf",
                            array(), $this->getSchema('controledeacesso')
                    )
                    ->where('e.IdUsuario = ?', $idUsuarioLogado);

        if(!empty($mecanismo)){
            $slct3->where('a.Mecanismo = ?', $mecanismo);
        }

        $slctUnion = $this->select()
                            ->union(array('('.$slct1.')', '('.$slct2.')', '('.$slct3.')'))
                            ->order('ordem ASC')
                            ->order('nomeproponente ASC');

        return $this->fetchAll($slctUnion);
    }

	/* Metodo que lista os responsaveis
     *
     * */
    public function buscarResponsaveis($where=array() , $idAgenteProponente)
    {

    	$slct = $this->select();
        $slct->distinct();
        $slct->setIntegrityCheck(false);

        $slct->from(
                array('SGA' => 'SGCacesso'),
                array('SGA.Nome AS nomeresponsavel', 'SGA.Cpf AS cpfresponsavel', 'SGA.IdUsuario AS idresponsavel'), $this->getSchema('controledeacesso')
        );

        $slct->joinLeft(
                array('VI' => $this->_name),'VI.idUsuarioResponsavel = SGA.IdUsuario AND VI.idAgenteProponente = '.$idAgenteProponente,
                $this->_getCols(),  $this->getSchema('agentes')
        );

        $slct->joinLeft(
                array('ag' => 'Agentes'), "SGA.Cpf = ag.CNPJCPF",
                array('ag.CNPJCPF', 'ag.idAgente'), $this->getSchema('agentes')
        );

        $slct->joinLeft(
                array('v' => 'Visao'), "v.idAgente = ag.idAgente AND v.Visao = 146",
                array('v.visao as UsuarioVinculo'), $this->getSchema('agentes')
        );

        foreach ($where as $coluna => $valor)
        {
            $slct->where($coluna, $valor);
        }

        return $this->fetchAll($slct);
    }



	/**
	 * Metodo para buscar os Proponentes vinculados a um determinado Responsavel, bem como, os Projetos desses Proponentes
	 * @access public
	 * @param integer $idResponsavel
	 * @param integer $idPronac
	 * @return object
	 */
	public function buscarProponentesProjetosResponsavel($idResponsavel, $idPronac)
	{
		$select = $this->select();
		$select->setIntegrityCheck(false);
		$select->from(array('v' => $this->_name));

		$select->joinInner(
			array('a' => 'Agentes'),
			'v.idAgenteProponente = a.idAgente',
			array(),
            $this->getSchema('agentes')
		);

		$select->joinInner(
			array('p' => 'Projetos'),
			'a.CNPJCPF = p.CgcCpf',
			array(),
            $this->getSchema('sac')
		);

		$select->where('v.idUsuarioResponsavel = ?', $idResponsavel);
		$select->where('p.IdPRONAC             = ?', $idPronac);

		$select->order('v.idusuarioresponsavel ASC');
		$select->order('v.idagenteproponente ASC');
		$select->order('p.nomeprojeto ASC');

		return $this->fetchAll($select);
	}



        /**
	 * Metodo para buscar os Proponentes vinculados a um determinado Respons�vel
	 * @access public
	 * @param integer $idResponsavel
	 * @param integer $idPronac
	 * @return object
	 */
	public function buscarProponentes($idResponsavel)
	{
            $slctResponsavel = $this->select()
                                ->setIntegrityCheck(false)
                                ->distinct()
                                ->from(
                                        array('a' => 'Agentes'),
                                        array('CNPJCPF', 'idAgente', new Zend_Db_Expr('sac.dbo.fnNome(a.idAgente) AS NomeProponente')), $this->getSchema('agentes')
                                )
                                ->joinInner(
                                        array('c' => 'SGCacesso'), "a.CNPJCPF = c.Cpf",
                                        array(), $this->getSchema('controledeacesso')
                                )

                                ->where('c.IdUsuario = ?', $idResponsavel);


            $slctProponentes = $this->select()
                                ->setIntegrityCheck(false)
                                ->distinct()
                                ->from(
                                        array('a' => 'Agentes'),
                                        array('CNPJCPF', 'idAgente', new Zend_Db_Expr('sac.dbo.fnNome(idAgente) as NomeProponente')), $this->getSchema('agentes')
                                )
                                ->joinInner(
                                        array('v' => 'tbVinculo'), "a.idAgente = v.idAgenteProponente",
                                        array(), $this->getSchema('agentes')
                                )
                                ->joinInner(
                                        array('k' => 'SGCacesso'), "k.IdUsuario = v.idUsuarioResponsavel",
                                        array(), $this->getSchema('controledeacesso')
                                )
                                ->where('k.IdUsuario = ?', $idResponsavel)
                                ->where('v.siVinculo = ?', 2);


            $slctUnion = $this->select()
                            ->union(array('('.$slctResponsavel.')', '('.$slctProponentes.')'))
                            ->order('NomeProponente ASC');

            return $this->fetchAll($slctUnion);

	} // fecha metodo buscarProponentes()


}
