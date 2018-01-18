<?php
class Fiscalizacao
{
    private $dataIncio;
    private $dataTermino;
    private $observacao;
    
    
    // $dataInicio
    public function setDataIncio($dataIncio)
    {
        $this->dataIncio = $dataIncio;
    }
    
    public function getDataIncio()
    {
        return $this->dataIncio;
    }
    
    // $dataTermino
    public function setDataTermino($dataTermino)
    {
        $this->dataTermino = $dataTermino;
    }
    
    public function getDataTermino()
    {
        return $this->dataTermino;
    }
    
    // $observacao
    public function setObservacao($observacao)
    {
        $this->observacao = $observacao;
    }
    
    public function getObservacao()
    {
        return $this->observacao;
    }
    
    // Esta função tem por objetivo instancia o objeto Projeto
    public function setProjeto(Projeto $projeto)
    {
        $this->projeto = $projeto;
    }

    // Esta função tem por objetivo instancia o objeto Arquivo
    public function setArquivo(Arquivo $arquivo)
    {
        $this->arquivo = $arquivo;
    }
    
    // Esta função tem por objetivo instancia o objeto Agente
    public function setAgente(Agente $agente)
    {
        $this->agente = $agente;
    }
    
    // Esta função tem por objetivo instancia o objeto TipologiaFiscalizacao
    public function setTipologiaFiscalizacao(TipologiaFiscalizacao $tipologiafiscalizacao)
    {
        $this->fipologiafiscalizacao = $tipologiafiscalizacao;
    }
    
    // Esta função tem por objetivo instancia o objeto EstadoFiscalizacao
    public function setEstadoFiscalizacao(EstadoFiscalizacao $estadofiscalizacao)
    {
        $this->estadofiscalizacao = $estadofiscalizacao;
    }
    
    // Esta função tem por objetivo instancia o objeto Orgão
    public function setOrgao(Orgao $orgao)
    {
        $this->orgao = $orgao;
    }
}
