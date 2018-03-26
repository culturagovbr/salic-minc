<?php
class GerarRelatorioReuniaoExternoController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));
        parent::init(); // chama o init() do pai GenericControllerNew
    }

    public function gerarrelatorioreuniaoAction()
    {
        $reuniao = new Reuniao();
        $pauta = new Pauta();
        $area = new Area();
        $projetos = new Projetos();
        $aprovacao = new Aprovacao();
        if ($_POST) {
            if (isset($_POST['idReuniao'])) {
                $NrReuniao = $_POST['idReuniao'];
                $buscarReuniao = $reuniao->buscar(array('NrReuniao = ?' => $NrReuniao))->current()->toArray();
                $idReuniao = $buscarReuniao['idNrReuniao'];
                $this->view->NrReuniao = $NrReuniao;
            } else {
                $buscarReuniao = $reuniao->buscarReuniaoAberta();
                if (count($buscarReuniao) > 0) {
                    $idReuniao = $buscarReuniao['idNrReuniao'];
                    $NrReuniao = $buscarReuniao['NrReuniao'];
                    $this->view->NrReuniao = $NrReuniao - 1;
                }
            }
            $idpronac = null;
            if (isset($_POST['pronac']) and $_POST['pronac'] != null) {
                $pronac = $_POST['pronac'];
                $buscarprojeto = $projetos->buscar(array('(AnoProjeto+Sequencial = ?)' => $pronac))->current()->toArray();
                $idpronac = $buscarprojeto['IdPRONAC'];
            }
            $buscarPauta = $pauta->PautaAprovada($idReuniao, $idpronac);
            $projetos = array();
            $num = 0;
            foreach ($buscarPauta as $projetosCNIC) {
                $projetos[$projetosCNIC->Area][$num]['PRONAC'] = $projetosCNIC->pronac;
                $projetos[$projetosCNIC->Area][$num]['NomeProjeto'] = $projetosCNIC->NomeProjeto;
                $projetos[$projetosCNIC->Area][$num]['DtProtocolo'] = Data::tratarDataZend($projetosCNIC->DtProtocolo, 'Brasileira');
                $projetos[$projetosCNIC->Area][$num]['DtAprovacao'] = Data::tratarDataZend($projetosCNIC->DtAprovacao, 'Brasileira');
                $projetos[$projetosCNIC->Area][$num]['stAnaliseConselheiro'] = $projetosCNIC->ParecerFavoravel == 1 ? "Indeferir" : 'Aprovar';
                $projetos[$projetosCNIC->Area][$num]['dsConselheiro'] = $projetosCNIC->ResumoParecer;
                if ($projetosCNIC->stAnalise == 'AS') {
                    $projetos[$projetosCNIC->Area][$num]['stAnalisePlenaria'] = 'Aprovar';
                } elseif ($projetosCNIC->stAnalise == 'IS') {
                    $projetos[$projetosCNIC->Area][$num]['stAnalisePlenaria'] = 'Indeferir';
                } else {
                    $projetos[$projetosCNIC->Area][$num]['stAnalisePlenaria'] = '';
                }
                $projetos[$projetosCNIC->Area][$num]['dsPlenaria'] = $projetosCNIC->dsConsolidacao;
                $projetos[$projetosCNIC->Area][$num]['ValorAprovado'] = $projetosCNIC->AprovadoReal ? $projetosCNIC->AprovadoReal : 0;
                $num++;
            }
            $this->view->projetospauta = $projetos;
        } else {
            $buscarReuniao = $reuniao->buscarReuniaoAberta();
            $NrReuniao = $buscarReuniao['NrReuniao'];
            $this->view->NrReuniao = $NrReuniao - 1;
        }
    }

    public function gerarpdfAction()
    {
        $this->_helper->layout->disableLayout();
        $this->view->dadosprojetos = $_POST['dadospdf'];
    }
}
