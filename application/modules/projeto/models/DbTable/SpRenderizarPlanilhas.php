<?php

class Projeto_Model_DbTable_SpRenderizarPlanilhas extends MinC_Db_Table_Abstract
{
    protected $_schema = 'SAC';
    protected $_name = 'spRenderizarPlanilhas';

    const TIPO_PLANILHA_READEQUADA = 'RE';
    const TIPO_PLANILHA_HOMOLOGADA = 'AP';

    public function exec($idPronac, $tipoPlanilha)
    {
        $sql = "exec {$this->_schema}.{$this->_name} {$idPronac}, {$tipoPlanilha}";

        return $this->getAdapter()->fetchAll($sql);
    }

    public function obterPlanilhaPorTipo($idPronac, $tipoPlanilha)
    {

        if (empty($idPronac) || empty($tipoPlanilha)) {
            throw new Exception("Pronac e tipo são obrigatórios");
        }

        $planilha = $this->exec($idPronac, $tipoPlanilha);

        if (empty($planilha)) {
            return false;
        }

        $planilha = $this->montarPlanilha($planilha, $tipoPlanilha);

        return $planilha;
    }


    /**
     * @todo padronizar o nome da coluna VlComprovado
     */
    public function montarPlanilha($planilhaOrcamentaria, $tipo = null)
    {
        if (!is_array($planilhaOrcamentaria)) {
            return [];
        }

        $count = 0;
        $i = 1;
        $planilha = [];

        foreach ($planilhaOrcamentaria as $item) {
            $item = array_map('TratarString::converterParaUTF8', $item);

            $item["Seq"] = $i;
            $item['Produto'] = !empty($item['Produto']) ? $item['Produto'] : html_entity_decode('Administra&ccedil;&atilde;o do Projeto');
            if ($tipo == self::TIPO_PLANILHA_READEQUADA) {
                $item['DescAcao'] = $this->obterNomeAcao($item["tpAcao"]);
                $item['JustProponente'] = $item["dsJustificativa"]; # @todo padronizar o nome
            }

            $planilha[] = $item;

            $count++;
            $i++;
        }

        return $planilha;
    }

    public function obterNomeAcao($tipoAcao)
    {
        switch ($tipoAcao) {
            case 'I':
                return 'Inclu&iacute;do';
                break;
            case 'E':
                return 'Exclu&iacute;do';
                break;
            case 'A':
                return 'Alterado';
                break;
            default:
                return '';
        }
    }


}
