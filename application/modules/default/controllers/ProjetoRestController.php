<?php

/**
 * Dados do proponente via REST
 *
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://www.cultura.gov.br
 * @copyright � 2016 - Minist�rio da Cultura - Todos os direitos reservados.
 */
class ProjetoRestController extends Minc_Controller_AbstractRest
{
    public function init()
    {
        $this->setPublicMethod('get');
        $this->setPublicMethod('index');
        parent::init();
    }

    public function postAction()
    {
    }
    
    public function indexAction()
    {
        $next = $this->_request->getParam('next');
        $offset = $this->_request->getParam('offset');
        $total = $this->_request->getParam('total');
        $idProponente = $this->_request->getParam('proponente');
        $pronac = $this->_request->getParam('pronac');
        $cgcCpf = $this->_request->getParam('cgcCpf');
        $nomeProponente = $this->_request->getParam('nomeProponente');
        $idUsuario = null;
        if ($this->usuario) {
            $idUsuario = $this->usuario->IdUsuario;
        }
        
        $listaProjeto = array();
        $modelProjeto = new Projetos();
        $objParam = (object) array(
            'next' => $next,
            'offset' => $offset,
            'idUsuario' => (int)$idUsuario,
            'idProponente' => $idProponente,
            'pronac' => $pronac,
            'cgcCpf' => $cgcCpf,
            'nomeProponente' => $nomeProponente);
        # Verifica se existe necessidade de buscar o n�mero total de registros da consulta
        if (!$total) {
            $total = $modelProjeto->buscarTotalListarProjetosDeUsuario($objParam);
        }
        # Busca os dados da lista
        $objListaRs = $modelProjeto->listarProjetosDeUsuario($objParam);
        if ($objListaRs) {
            $arrListaRs = $objListaRs->toArray();
            if ($arrListaRs) {
                foreach ($arrListaRs as $projeto) {
                    $projeto['NomeProjeto'] = utf8_encode($projeto['NomeProjeto']);
                    $listaProjeto[] = (object)$projeto;
                }
            }
        }

        # Resposta do servi�o.
        $this->getResponse()->setHttpResponseCode(200)->setBody(json_encode(
            array(
            'list' => $listaProjeto,
            'total' => (int)$total)
        ));
    }

    public function getAction()
    {
        $pronac = $this->_request->getParam('id');
        $modelProjeto = new Projetos();
        $resultado = $modelProjeto->buscarPorPronac($pronac);
        $projeto = (object) $resultado->toArray();

        if ($projeto) {
            # Busca lancamentos no Extrato Banc�rio
            $listaResult = $modelProjeto->buscarAnoExtratoDeProjeto($pronac);
            $listaAno = $listaResult->toArray();
            $numeroLancamentoExtrato = count($listaAno);
            
            # Formatando dados
            $projeto->NomeProjeto = utf8_encode($projeto->NomeProjeto);
            $projeto->CNPJCPF = mascara::addMaskCpfCnpj($projeto->CNPJCPF);
            $projeto->Proponente = utf8_encode($projeto->Proponente);
            $projeto->Area = utf8_encode($projeto->Area);
            $projeto->Segmento = utf8_encode($projeto->Segmento);
            $projeto->Situacao = utf8_encode($projeto->Situacao);
            $projeto->Enquadramento = utf8_encode($projeto->Enquadramento);
            $projeto->Agencia = $this->formatarAgencia($projeto->Agencia);
            $projeto->Conta = $this->formatarContaCorrente($projeto->Conta);
            $projeto->stConta = $this->formatarSituacaoConta($projeto);
            $projeto->dtFimCaptacao = $projeto->dtFimCaptacao? date('d/m/Y', strtotime($projeto->dtFimCaptacao)): null;
            $projeto->DtFimExecucao = $projeto->DtFimExecucao? date('d/m/Y', strtotime($projeto->DtFimExecucao)): null;
            $projeto->ValorAprovado = number_format($projeto->ValorAprovado, 2, ',', '.');
            $projeto->ValorProjeto = number_format($projeto->ValorProjeto, 2, ',', '.');
            $projeto->ValorCaptado = number_format($projeto->ValorCaptado, 2, ',', '.');
            $projeto->VlComprovado = number_format($projeto->VlComprovado, 2, ',', '.');
            $projeto->PercCaptado = number_format($projeto->PercCaptado, 2, ',', '.');
            $projeto->ResumoProjeto = html_entity_decode(utf8_encode($projeto->ResumoProjeto), ENT_COMPAT, 'UTF-8');
            $projeto->nuLancamento = $numeroLancamentoExtrato;
        }

        # Resposta do servi�o.
        $this->getResponse()->setHttpResponseCode(200)->setBody(json_encode($projeto));
    }

    /**
     * Regra de visualiza��o para formatar a descri��o da conta.
     *
     * @param stdClass $projeto
     * @return string
     */
    protected function formatarSituacaoConta($projeto)
    {
        $descricao = '';
        switch ($projeto->stConta) {
            case 'LIBE':
                $descricao = 'Liberada';
            break;
            case 'BLOQ':
                $descricao = 'Bloqueada';
            break;
            default:
                $descricao = 'Conta Inexistente';
        }
        if (!(int)$projeto->Conta) {
            $descricao = '-';
        }
        
        return $descricao;
    }

    /**
     * Regra de visualiza��o para formatar o n�mero da conta corrente.
     *
     * @param string $conta
     * @return string
     */
    protected function formatarContaCorrente($conta)
    {
        $resultado = null;
        if ((int)$conta) {
            # Retira os zeros � esquerda.
            $resultado = (int)$conta;
            # Numero de caracteres.
            $qtdCarecteres = strlen($resultado);
            # Numero principal da CC
            $numeroPrincipal = substr($resultado, 0, ((int)$qtdCarecteres)-1);
            $numero = number_format($numeroPrincipal, 0, '.', '.');
            # Digito da CC
            $digito = substr($resultado, ((int) $qtdCarecteres) -1, $qtdCarecteres);
            # Inserindo tra�o
            $resultado = $numero. '-'. $digito;
        }
        
        return $resultado;
    }
    
    /**
     * Regra de visualiza��o para formatar o n�mero da Ag�ncia.
     *
     * @param string $agencia
     * @return string
     */
    protected function formatarAgencia($agencia)
    {
        $resultado = null;
        if ($agencia) {
            $qtdNumero = strlen($agencia);
            $resultado = substr($agencia, 0, ((int)$qtdNumero)-1). '-'. substr($agencia, ((int) $qtdNumero) -1, $qtdNumero);
        }
        
        return $resultado;
    }

    public function putAction()
    {
    }

    public function deleteAction()
    {
    }
}
