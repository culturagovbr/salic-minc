<?php
/**
 * PublicacaoDouController
 * @author Equipe RUP - Politec
 * @since 20/07/2010
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://www.cultura.gov.br
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 */

class RastrearagenteController extends MinC_Controller_Action_Abstract
{
    /**
     * @var integer (vari�vel com o id do usu�rio logado)
     * @access privacte
     */

    public function init()
    {

        // verifica as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 92;
        $PermissoesGrupo[] = 93;
        $PermissoesGrupo[] = 97;
        $PermissoesGrupo[] = 100;
        $PermissoesGrupo[] = 103;
        $PermissoesGrupo[] = 104;
        $PermissoesGrupo[] = 106;
        $PermissoesGrupo[] = 110;
        $PermissoesGrupo[] = 113;
        $PermissoesGrupo[] = 115;
        $PermissoesGrupo[] = 121;
        $PermissoesGrupo[] = 122;
        $PermissoesGrupo[] = 123;
        $PermissoesGrupo[] = 125;
        $PermissoesGrupo[] = 126;
        $PermissoesGrupo[] = 127;
        $PermissoesGrupo[] = 131;
        $PermissoesGrupo[] = 132;
        $PermissoesGrupo[] = 134;
        $PermissoesGrupo[] = 135;
        $PermissoesGrupo[] = 136;
        $PermissoesGrupo[] = 137;
        $PermissoesGrupo[] = 138;
        $PermissoesGrupo[] = 139;
        $PermissoesGrupo[] = 148;
        $PermissoesGrupo[] = 151;

        // defini��o do perfil
        parent::perfil(1, $PermissoesGrupo);
        parent::init(); // chama o init() do pai GenericControllerNew
    } // fecha m�todo init()

    public function indexAction()
    {
    }

    public function consultaAction()
    {
        $get = Zend_Registry::get('get');
        $CpfCnpj = $get->CpfCnpj;
        if (empty($CpfCnpj)) {
            parent::message("Por favor informe o CPF ou CNPJ.", "/Rastrearagente", "ERROR");
        }
        $CpfCnpj = str_replace(array(".", "-", "/"), array("", "", ""), $CpfCnpj); //removendo mascara de CPF e CNPJ
        $agente = Agente_Model_ManterAgentesDAO::buscarAgentes($CpfCnpj);
        if (count($agente)<1) {
            parent::message("Nenhum agente encontrado com o CPF/CNPJ {$get->CpfCnpj}", "/Rastrearagente", "ALERT");
        }
        $visaoTable = new Agente_Model_DbTable_Visao();
        $visoes = $visaoTable->buscarVisao($agente[0]->idAgente);

        $projeto = new Projetos();
        $projetos = null;
        $projetos = $projeto->buscarTodosDadosProjeto(null, $CpfCnpj);
        $projetos2 = null;
        $projetos2 = $projeto->buscarTodosDadosProjeto(null, $CpfCnpj)->toArray();

        $preprojeto = new Proposta_Model_DbTable_PreProjeto();
        $preprojetos = $preprojeto->buscar(array("idAgente = ? " => $agente[0]->idAgente));
        $preprojetos = empty($preprojetos) ? array() : $preprojetos;

        $vinculo = new Agente_Model_DbTable_Vinculacao();
        $vinculos = $vinculo->BuscarVinculos($agente[0]->idAgente);
        $vinculos = empty($vinculos) ? array() : $vinculos;

        $proposta = new Proposta_Model_DbTable_PreProjeto();
        $propostas = $proposta->propostastransformadas($agente[0]->idAgente);
        $propostas = empty($propostas) ? array() : $propostas;

        $inabilitado = new Inabilitado();
        $inabilitados = $inabilitado->listainabilitados($CpfCnpj);
        $inabilitados = empty($inabilitados) ? array() : $inabilitados;

        $capitacaoMEC = new Captacao();
        $captacaoQuotas = new CaptacaoQuotas();
        $captacaoguia = new CaptacaoGuia();
        $captacaoconversao = new CaptacaoConversao();

        for ($i = 0; $i < count($projetos2); $i++) {
            $val1 = null;
            $val2 = null;
            $val3 = null;
            $val4 = 0;

            $where = array(
                "AnoProjeto = ?" => substr($projetos2[$i]['pronac'], 0, 2),
                "Sequencial = ?" => substr($projetos2[$i]['pronac'], 2)
            );

            $val1 = $capitacaoMEC->CapitacaoTotalMEC(substr($projetos2[$i]['pronac'], 0, 2), substr($projetos2[$i]['pronac'], 2))->current();
            $val2 = $captacaoQuotas->CapitacaoArt1(substr($projetos2[$i]['pronac'], 0, 2), substr($projetos2[$i]['pronac'], 2))->current();
            $val3 = $captacaoguia->BuscarTotalCaptacaoGuia(false, $where);
            $val3 = count($val3)>0 ? $val3[0]->Art3 : 0 ;
            $val4 = $captacaoconversao->BuscarTotalCaptacaoConversao(false, $where);
            $val4 = count($val4)>0 ? $val4[0]->Conv : 0 ;

            $projetos2[$i]['TotalCaptado'] = $val1->Mec + $val2->Art1 + $val3 + $val4;
        }

        $this->view->agente                 = $agente;
        $this->view->visoes                 = $visoes;
        $this->view->projetos               = $projetos;
        $this->view->projetos2              = $projetos2;
        $this->view->preprojetos            = $preprojetos;
        $this->view->vinculos               = $vinculos;
        $this->view->propostastrasformadas  = $propostas;
        $this->view->inabilitados           = $inabilitados;
    }
}
