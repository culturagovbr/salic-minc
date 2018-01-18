<?php

/**
 * Modelo que representa a tabela SAC.dbo.tbComunicados
 *
 * @author PEDRO GOMES
 */
class tbAgentesxVerificacao extends MinC_Db_Table_Abstract
{
    protected $_banco  = 'AGENTES';
    protected $_schema = 'AGENTES';
    protected $_name   = 'tbAgentesxVerificacao';

    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function listarMandato($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array("c"=>$this->_name),
                array(  "idAgentexVerificacao",
                        "idVerificacao",
                        "dsNumeroDocumento",
                        "dtInicioMandato",
                        "dtFimMandato",
                        "stMandato",
                        "idDirigente",
                        "idEmpresa",
                        "idArquivo")
                    );
        $slct->joinInner(
                array('d' => 'verificacao'),
              'c.idVerificacao = d.idVerificacao',
                array('d.Descricao'),
              'AGENTES.dbo'
        );
        $slct->joinInner(
                array('e' => 'tbArquivo'),
              'c.idArquivo = e.idArquivo',
                array('e.nmArquivo'),
              'BDCORPORATIVO.scCorp'
        );
        $slct->joinInner(
                array('f' => 'tbArquivoImagem'),
              'e.idArquivo = f.idArquivo',
                array('*'),
              'BDCORPORATIVO.scCorp'
        );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        if ($count) {
            $slct2 = $this->select();
            $slct2->setIntegrityCheck(false);
            $slct2->from(
                            array("c"=>$this->_name),
                            array('total'=>"count(*)")
                         );
            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slct2->where($coluna, $valor);
            }

            $rs = $this->fetchAll($slct2)->current();
            if ($rs) {
                return $rs->total;
            } else {
                return 0;
            }
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

    public function mandatoRepetido($idAgente, $dtInicio, $dtFim)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array("c"=>$this->_name),
                array(  "idAgentexVerificacao",
                        "idVerificacao",
                        "dsNumeroDocumento",
                        "dtInicioMandato",
                        "dtFimMandato",
                        "stMandato",
                        "idDirigente",
                        "idArquivo")
        );

        $slct->where('idDirigente = ?', $idAgente);
        $slct->where("'{$dtInicio}' BETWEEN dtInicioMandato AND dtFimMandato");
        //$slct->orWhere('idAgente = ?',$idAgente);
        $slct->where("'{$dtFim}' BETWEEN dtInicioMandato AND dtFimMandato");
        $slct->where('stMandato = ?', '0');


        $rs = $this->fetchAll($slct)->current();

        return $this->fetchAll($slct);
    }
}
