<?php
class FornecedorModel extends Minc_Db_Table_Abstract
{
    protected $_banco = 'agentes';
    protected $_name = 'agentes';
    protected $_schema = 'agentes';
    protected $_primary = 'idAgente';
    
    public function pesquisarFornecedorItem($item)
    {
        $select = "SELECT 'COTAÇÃO' as Modalidade, b.idAgente,e.CNPJCPF,F.Descricao AS Fornecedor,c.idPlanilhaAprovacao,d.IdPRONAC
                FROM  BDCORPORATIVO.scSAC.tbCotacao a
                INNER JOIN BDCORPORATIVO.scSAC.tbCotacaoxAgentes b on (a.idCotacao = b.idCotacao)
                INNER JOIN BDCORPORATIVO.scSAC.tbCotacaoxPlanilhaAprovacao c on (b.idCotacaoxAgentes = c.idCotacaoxAgentes)
                INNER JOIN SAC.DBO.tbPlanilhaAprovacao d on (c.idPlanilhaAprovacao = d.idPlanilhaAprovacao)
                INNER JOIN AGENTES.DBO.AGENTES e on (b.idAgente  =e.idAgente)
                INNER JOIN AGENTES.DBO.Nomes f on (e.idAgente = f.idAgente)
                WHERE d.stAtivo = 'S' AND D.idPlanilhaAprovacao = ?
                UNION ALL
                SELECT 'DISPENSA' as Modalidade, a.idAgente,d.CNPJCPF,e.Descricao AS Fornecedor,c.idPlanilhaAprovacao,c.IdPRONAC
                FROM  BDCORPORATIVO.scSAC.tbDispensaLicitacao a
                INNER JOIN BDCORPORATIVO.scSAC.tbDispensaLicitacaoxPlanilhaAprovacao b on (a.idDispensaLicitacao = b.idDispensaLicitacao)
                INNER JOIN SAC.DBO.tbPlanilhaAprovacao c on (b.idPlanilhaAprovacao = c.idPlanilhaAprovacao)
                INNER JOIN AGENTES.DBO.AGENTES d on (a.idAgente  = d.idAgente)
                INNER JOIN AGENTES.DBO.Nomes e on (d.idAgente = e.idAgente)
                WHERE c.stAtivo = 'S' AND c.idPlanilhaAprovacao = ?
                UNION ALL
                SELECT 'LICITAÇAO' as Modalidade, b.idAgente,e.CNPJCPF,F.Descricao AS Fornecedor,c.idPlanilhaAprovacao,d.IdPRONAC
                FROM  BDCORPORATIVO.scSAC.tbLicitacao a
                INNER JOIN BDCORPORATIVO.scSAC.tbLicitacaoxAgentes b on (a.idLicitacao = b.idLicitacao)
                INNER JOIN BDCORPORATIVO.scSAC.tbLicitacaoxPlanilhaAprovacao c on (b.idLicitacao = c.idLicitacao)
                INNER JOIN SAC.DBO.tbPlanilhaAprovacao d on (c.idPlanilhaAprovacao = d.idPlanilhaAprovacao)
                INNER JOIN AGENTES.DBO.AGENTES e on (b.idAgente  =e.idAgente)
                INNER JOIN AGENTES.DBO.Nomes f on (e.idAgente = f.idAgente)
                WHERE b.stVencedor = 1 AND d.stAtivo = 'S' AND D.idPlanilhaAprovacao = ?
                UNION ALL
                SELECT 'CONTRATO' as Modalidade, b.idAgente,e.CNPJCPF,F.Descricao AS Fornecedor,c.idPlanilhaAprovacao,d.IdPRONAC
                FROM  BDCORPORATIVO.scSAC.tbContrato a
                INNER JOIN BDCORPORATIVO.scSAC.tbContratoxAgentes b on (a.idContrato = b.idContrato)
                INNER JOIN BDCORPORATIVO.scSAC.tbContratoxPlanilhaAprovacao c on (b.idContrato = c.idContrato)
                INNER JOIN SAC.DBO.tbPlanilhaAprovacao d on (c.idPlanilhaAprovacao = d.idPlanilhaAprovacao)
                INNER JOIN AGENTES.DBO.AGENTES e on (b.idAgente  =e.idAgente)
                INNER JOIN AGENTES.DBO.Nomes f on (e.idAgente = f.idAgente)
                WHERE d.stAtivo = 'S' AND D.idPlanilhaAprovacao = ?";
        $bind = array(
            $item, // cotacao
            $item, // dispensa
            $item, // licitacao
            $item, // contrato
        );
        $db = Zend_Db_Table::getDefaultAdapter();
        $stmt = $db->query($select, $bind);
        return $stmt->fetch();
    }

    public function pesquisarFornecedor($fornecedor)
    {
        $stmt = $this->getAdapter()->query(
            "SELECT * FROM AGENTES.DBO.AGENTES AS a
                INNER JOIN AGENTES.DBO.Nomes AS b ON a.idAgente = b.idAgente
            WHERE a.idAgente = ?",
            $fornecedor
        );
        return $stmt->fetchObject();
    }
}
