<?php

namespace Application\Modules\agente\service\agente;

class Agente
{
    const DIAS_LIMITE_PARA_ATUALIZACAO = 183;

    /**
     * Metodo para retorno de intervalo de tempo
     * @param $dados
     * @return bool|\DateInterval
     */
    private function obterDiferencaDatas($dados)
    {
        if(empty($dados)){
            return null;
        }

        $dtatual = new \DateTime(); //data atual
        $data = \DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($dados)));
        return $dtatual->diff($data);
    }

    /**
     * Metodo de Cadastro e Atualização de agentes com consulta ao InfoConv
     * @access public
     * @param $cpf (cpf ou cnpj)
     * @return \ArrayObject
     */
    public function cadastrarAgente($cpfCnpj)
    {
        $agenteRetorno = \Agente_Model_ManterAgentesDAO::buscarAgentes($cpfCnpj);
        $agente = (array)current($agenteRetorno);


        if (empty($agente[0])) {
            return $this->consultarServicoReceita($cpfCnpj, '');
        }

        $diferencaEntreDatas = $this->obterDiferencaDatas($agente['dtatualizacao'])->days;
        if ($diferencaEntreDatas < self::DIAS_LIMITE_PARA_ATUALIZACAO) {

            $novoAgente = [];
            $novoAgente['msgCPF'] = 'cadastrado';
            $novoAgente['idAgente'] = $agente['idagente'];
            $novoAgente['Nome'] = $agente['nome'];
            $novoAgente['agente'] = $agente;
            return $novoAgente;
        }

        return $this->consultarServicoReceita($cpfCnpj, $agente);
    }

    /**
     * Metodo consulta Agente Fisico/Juridico no Serviço que consulta Receita Federal
     * chamada -> function cadastrarAgente()
     * @param $cpf, $idAgente
     * @return \ArrayObject
     */

    private function consultarServicoReceita($cpfCnpj, $agente = null)
    {
        $idAgente = $agente ? $agente['idagente'] : '';
        $parametrosDoRetorno = strlen($cpfCnpj) == 11 ? 'nmPessoaFisica' : 'nmRazaoSocial';
        $servico = strlen($cpfCnpj) == 11 ? 'consultarPessoaFisicaReceitaFederal' : 'consultarPessoaJuridicaReceitaFederal';

        #Instancia a Classe de Servico do WebService da Receita Federal
        $wsServico = new \ServicosReceitaFederal();

        $arrResultado = $wsServico->{$servico}($cpfCnpj);
        try {

            $novoAgente = [];
            $novoAgente['msgCPF'] = 'novo';

            if (!empty($arrResultado) && !empty($idAgente)) {

                $dtSituacaoCadastral = !empty($arrResultado['situacaoCadastral']) ?
                                              $arrResultado['situacaoCadastral']['dtSituacaoCadastral'] : '';

                $diferencaEntreDatasServico = $this->obterDiferencaDatas($dtSituacaoCadastral)->days;

                if (empty($dtSituacaoCadastral) || $diferencaEntreDatasServico > self::DIAS_LIMITE_PARA_ATUALIZACAO) {
                    $arrResultado = $wsServico->{$servico}($cpfCnpj, true);
                    if (!empty($arrResultado["erro"])) {
                        throw new \Exception("Inválido");
                    }
                    $novoAgente['msgCPF'] = 'atualizado';
                }

                $novoAgente['idAgente'] = $idAgente;
                $agente['nome'] = utf8_encode($arrResultado[$parametrosDoRetorno]);
                $this->salvarNomeRazaoSocial($idAgente, $agente);

            } else {
                $arrResultado = $wsServico->{$servico}($cpfCnpj, true);
                if (!empty($arrResultado["erro"])) {
                    throw new \Exception("Inválido");
                }
            }
            $novoAgente['Nome'] = $arrResultado[$parametrosDoRetorno];
            $novoAgente['Cep'] = isset($arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep']) &&
                                       $arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep'] ?
                                       $arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep'] : '';
            return $novoAgente;
        } catch (\Exception $exception) {
            return $novoAgente['msgCPF'] = $exception->getMessage();
        }
    }

    /**
     * salvarNomeRazaoSocial
     *
     * @access private
     * @return void
     * @todo refatorar metodo para um generico que possa salvar todas as possibilidades para Agentes
     */
    private function salvarNomeRazaoSocial($idAgente = null, $modelAgente = null)
    {
        try {
            $nome = $modelAgente[0]->nome ? $modelAgente[0]->nome : $modelAgente['nome'];
            $nome = preg_replace('/[\'\"\n\`\´]/', '', $nome);
            $mprNomes = new \Agente_Model_DbTable_Nomes;
            $mprNomes->alterar(['Descricao'=>$nome],['idAgente = ?'=> $idAgente]);
            $mpAgente = new \Agente_Model_DbTable_Agentes;
            $dataAtualizada = new \DateTime();
            $mpAgente->alterar(['DtAtualizacao'=> $dataAtualizada->format('Y-m-d h:i:s') ],['idAgente = ?'=> $idAgente]);

        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar a Raz&atilde;o Social: " . $e->getMessage());
        }
    }

}
