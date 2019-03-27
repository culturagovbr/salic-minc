<?php

namespace Application\Modules\AvaliacaoResultados\Service\ParecerTecnico;

use phpDocumentor\Reflection\Types\Object_;

class Diligencia
{
    /** modules/proposta/controllers/DiligenciarController -> listardiligenciaanalistaAction */
        public function listaDiligenciaPainel ($params) {
             $Projetosdao = new \Projetos();
             $PreProjetodao = new \Proposta_Model_DbTable_PreProjeto();

              $idPronac = $params['idPronac'];
              $idPreProjeto = $params['idPreProjeto'];
              $situacao = $params['situacao'];
              $idProduto = $params['idProduto'];
              $tpDiligencia = $params['tpDiligencia'];
              $dao = '';

               if ($idPronac) {
                   if ($idProduto) {
                       $diligencias = $Projetosdao->listarDiligencias(
                            array(
                                'pro.IdPRONAC = ?' => $idPronac,
                                'dil.idProduto = ?' => $idProduto,
                                'dil.stEnviado = ?' => 'S'
                            )
                        );
                       return $diligencias;
                   } else {
                         $projeto = $Projetosdao->buscar(array('IdPRONAC = ?' => $idPronac));
                         $_idProjeto = isset($projeto[0]->idProjeto) && !empty($projeto[0]->idProjeto) ? $projeto[0]->idProjeto : 0;
                         $diligenciasProposta = $PreProjetodao->listarDiligenciasPreProjeto(
                                array(
                                    'pre.idPreProjeto = ?' => $_idProjeto,
                                    'aval.ConformidadeOK = ?' => 0
                                )
                         );

                         $diligencias = $Projetosdao->listarDiligencias(array('pro.IdPRONAC = ?' => $idPronac))->toArray();

                         $lista = $diligencias;

                         foreach ( $lista as $key => $value){

                             $value['Arquivos'] = $this->obterAnexosDiligencias($value);

                             $lista[$key] = $value;
                         }
                   return $lista;
                   }
               } else {
                    if ($idPreProjeto) {
                     $diligenciasProposta = $dao->listarDiligenciasPreProjeto(array('pre.idPreProjeto = ?' => $this->idPreProjeto, 'aval.ConformidadeOK = ? ' => 0));
                     return $diligenciasProposta;
                    }
               }
        }

        /** module/default/models/projetos.php */
    public function listarDiligencias($consulta = array(), $retornaSelect = false)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pro' => $this->_name),
            array('nomeProjeto' => 'pro.NomeProjeto', 'pronac' => new Zend_Db_Expr('pro.AnoProjeto+pro.Sequencial'))
        );

        $select->joinInner(
            array('dil' => 'tbDiligencia'),
            'dil.idPronac = pro.IdPRONAC',
            array(
                'dil.stProrrogacao',
                'idDiligencia' => 'dil.idDiligencia',
                'dataSolicitacao' => 'dil.DtSolicitacao',
                'dataResposta' => 'dil.DtResposta',
                'Solicitacao' => 'dil.Solicitacao',
                'Resposta' => new Zend_Db_Expr('CAST(dil.Resposta AS TEXT)'),
                'dil.idCodigoDocumentosExigidos',
                'dil.idTipoDiligencia',
                'dil.stEnviado'
            )
        );
        $select->joinInner(array('ver' => 'Verificacao'), 'ver.idVerificacao = dil.idTipoDiligencia', array('tipoDiligencia' => 'ver.Descricao'));
        $select->joinLeft(array('prod' => 'Produto'), 'prod.Codigo = dil.idProduto', array('produto' => 'prod.Descricao'));


        foreach ($consulta as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if ($retornaSelect) {
            return $select;
        } else {
            return $this->fetchAll($select);
        }
    }

    /** modules/proposta/models/DbTable/preprojeto.php */
    public function listarDiligenciasPreProjeto($consulta = array(), $retornaSelect = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pre' => $this->_name),
            array('nomeProjeto' => 'pre.nomeprojeto', 'pronac' => 'pre.idpreprojeto'),
            $this->_schema
        );

        $select->joinInner(
            array('aval' => 'tbAvaliacaoProposta'),
            'aval.idProjeto = pre.idPreProjeto',
            array(
                'aval.stProrrogacao',
                'idDiligencia' => 'aval.idAvaliacaoProposta',
                'idAvaliacaoProposta' => 'aval.idAvaliacaoProposta',
                'dataSolicitacao' => new Zend_Db_Expr('CONVERT(VARCHAR,aval.DtAvaliacao,120)'),
                'dataResposta' => new Zend_Db_Expr('CONVERT(VARCHAR,aval.dtResposta,120)'),
                'Solicitacao' => 'aval.Avaliacao',
                'Resposta' => 'aval.dsResposta',
                'aval.idCodigoDocumentosExigidos',
                'aval.stEnviado'
            ),
            $this->_schema
        );

        $select->joinLeft(
            array('arq' => 'tbArquivo'),
            'arq.idArquivo = aval.idArquivo',
            array(
                'arq.nmArquivo',
                'arq.idArquivo'
            ),
            $this->getSchema('bdcorporativo', true, 'sccorp')
        );

        $select->joinLeft(
            array('a' => 'AGENTES'),
            'pre.idAgente = a.idAgente',
            array(
                'a.idAgente'
            ),
            $this->getSchema('agentes')
        );

        $select->joinLeft(
            array('n' => 'NOMES'),
            'a.idAgente = n.idAgente',
            array(
                'n.Descricao'
            ),
            $this->getSchema('agentes')
        );

        foreach ($consulta as $coluna => $valor) {
            $select->where($coluna, $valor);
        }


        if ($retornaSelect) {
            return $select;
        } else {
            return $this->fetchAll($select);
        }
    }

    private function obterAnexosDiligencias($diligencia)
    {
        $arquivo = new \Arquivo();
        $arquivos = $arquivo->buscarAnexosDiligencias($diligencia['idDiligencia']);
        $arquivoArray = [];
        foreach ($arquivos as $arquivo) {
            $objdtEnvio = new \DateTime($arquivo->dtEnvio);
            $arquivoArray[] = [
                'idArquivo' => $arquivo->idArquivo,
                'nmArquivo' => $arquivo->nmArquivo,
                'dtEnvio' => $objdtEnvio->format('d/m/Y H:i:s'),
                'idDiligencia' => $arquivo->idDiligencia,
            ];
        }

        return $arquivoArray;
    }
}
