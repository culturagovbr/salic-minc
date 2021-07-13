<?php

class MantercalendariocnicController extends MinC_Controller_Action_Abstract
{
    private $intTamPag = 10;
    /**
    * Reescreve o m�todo init()
    * @access public
    * @param void
    * @return void
    */

    public function init()
    {
        $auth = Zend_Auth::getInstance(); // pega a autentica�?o
        $this->view->title = "Salic - Sistema de Apoio �s Leis de Incentivo � Cultura"; // t�tulo da página

        // 3 => autentica�?o scriptcase e autentica�?o/permiss?o zend (AMBIENTE PROPONENTE E MINC)
        // utilizar quando a Controller ou a Action for acessada via scriptcase e zend
        // define as permiss?es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 103; // Coordenador de Análise
        $PermissoesGrupo[] = 120; // Coordenador Administrativo CNIC
        parent::perfil(3, $PermissoesGrupo);
        if (isset($auth->getIdentity()->usu_codigo)) {
            $this->getIdUsuario = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
            if ($this->getIdUsuario) {
                $this->getIdUsuario = $this->getIdUsuario["idAgente"];
            } else {
                $this->getIdUsuario = 0;
            }
        } else {
            $this->getIdUsuario = $auth->getIdentity()->IdUsuario;
        }

        parent::init(); // chama o init() do pai GenericControllerNew
    } // fecha m�todo init()

    public function indexAction()
    {
        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        if (isset($get->tamPag)) {
            $this->intTamPag = $get->tamPag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
        $tblreuniao = new tbreuniao();
        $total = $tblreuniao->pegaTotal();
        $tamanho = (($inicio+$this->intTamPag)<=$total) ? $this->intTamPag : $total- ($inicio+$this->intTamPag) ;
        $fim = $inicio + $this->intTamPag;
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;


        $campo = (!empty($_GET['campo'])) ? $_GET['campo'] : "NrReuniao";
        $ordem = (!empty($_GET['ordem'])) ? $_GET['ordem'] : "desc";

        $this->view->ordemlista = $ordem;

        $order = array($campo." ".$ordem);


        $reunioes = $tblreuniao->listar(array(), $order, $tamanho, $inicio);
        $this->view->reunioies = $reunioes;


        if ($fim>$total) {
            $fim = $total;
        }
        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));

        $paginacao = array(
                            "pag"=>$pag,
                            "total"=>$total,
                            "inicio"=>($inicio+1),
                            "fim"=>$fim,
                            "totalPag"=>$totalPag,
                            "Itenspag"=>$this->intTamPag,
                            "tamanho"=>$tamanho
            );

        $this->view->paginacao = $paginacao;


        if (!empty($_POST['colunasFim'])) {
            $ordem = $_POST['colunasFim'];
        } else {
            $ordem = array("N.Reuniao","Dt.Inicio","Dt.Final","Dt.Fechamento","Mecanismo","Status");
        }

        $this->view->ordem = $ordem;
    }

    public function gerarpdfAction()
    {
        $this->_helper->layout->disableLayout();
        $html = $_POST ['html'];
        $formato = $_POST ['formato'];
        $pdf = new PDF($html, $formato);
        xd($pdf->gerarRelatorio());
    }

    public function reuniaoAction()
    {
        if ($this->getRequest()->isPost()) {
            $dados = array(
               'idNrReuniao'  => $_POST['idNrReuniao'],
               'NrReuniao'    => $_POST['NrReuniao'],
               'DtInicio'     => $_POST['DtInicio'],
               'DtFinal'      => $_POST['DtFinal'],
               'DtFechamento' => $_POST['DtFechamento'],
               'Mecanismo'    => $_POST['Mecanismo'],
               'idUsuario'    => $this->getIdUsuario
               );
            $tblReuniao =  new tbreuniao();
            if ($_POST['idNrReuniao'] <= 0 or empty($_POST['idNrReuniao'])) {
                $dados = array(
               'NrReuniao'    => $_POST['NrReuniao'],
               'DtInicio'     => ConverteData($_POST['DtInicio'], 13),
               'DtFinal'      => ConverteData($_POST['DtFinal'], 13),
               'DtFechamento' => ConverteData($_POST['DtFechamento'], 13),
               'Mecanismo'    => $_POST['Mecanismo'],
//               'Mecanismo'    => 1,
               'stEstado'     => 1,
               'idUsuario'    => $this->getIdUsuario,
               'stPlenaria'   => 'N'
               );
                $atualizar = tbreuniao::salvareuniao($dados);
                //inserrir
            } else {
                $atualizar = $tblReuniao->atualizarreuniao($dados);
                //$atualizar = tbreuniao::atualizarreuniao($dados);
            }


            if ($atualizar) {
                parent::message("Altera��o realizada com sucesso!", "mantercalendariocnic/index", "CONFIRM");
            } else {
                throw new Exception("Erro ao efetuar altera��o da reuni�o");
            }
        }
    }
}


