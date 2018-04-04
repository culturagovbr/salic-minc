<?php 

class ConfiguracoesController extends MinC_Controller_Action_Abstract
{
    private $idUsuario = 0;
    private $idOrgao = 0;
    private $idPerfil = 0;
    private $intTamPag = 10;

    /**
     * Reescreve o m�todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $this->idUsuario = $auth->getIdentity()->usu_codigo; // usu�rio logado
        
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $this->idOrgao = $GrupoAtivo->codOrgao;
        $this->idPerfil = $GrupoAtivo->codGrupo;
        
        // autentica��o e permiss�es zend (AMBIENTE MINC)
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 128; // T�cnico de Portaria
        parent::perfil(1, $PermissoesGrupo);

        parent::init();
    } // fecha m�todo init()


    public function secretariosAction()
    {
        
        //FUN��O ACESSADA SOMENTE PELOS PERFIS DE TEC. DE PORTARIA
        if ($this->idPerfil != 128) {
            parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal", "ALERT");
        }
        
        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }
        $order = array();

        //==== parametro de ordenacao  ======//
        if ($this->_request->getParam("ordem")) {
            $ordem = $this->_request->getParam("ordem");
            if ($ordem == "ASC") {
                $novaOrdem = "DESC";
            } else {
                $novaOrdem = "ASC";
            }
        } else {
            $ordem = "ASC";
            $novaOrdem = "ASC";
        }

        //==== campo de ordenacao  ======//
        if ($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo = null;
            $order = array(1); //idManterPortaria
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $where = array();
        $where['a.stEstado = ?'] = 1; // 1=Atual; 0=Historico
        
        $tbManterPortaria = new tbManterPortaria();
        $total = $tbManterPortaria->listaSecretarios($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;
        
        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $tbManterPortaria->listaSecretarios($where, $order, $tamanho, $inicio);
        $paginacao = array(
            "pag"=>$pag,
            "qtde"=>$this->intTamPag,
            "campo"=>$campo,
            "ordem"=>$ordem,
            "ordenacao"=>$ordenacao,
            "novaOrdem"=>$novaOrdem,
            "total"=>$total,
            "inicio"=>($inicio+1),
            "fim"=>$fim,
            "totalPag"=>$totalPag,
            "Itenspag"=>$this->intTamPag,
            "tamanho"=>$tamanho
        );
        
        $this->view->paginacao     = $paginacao;
        $this->view->qtdRegistros  = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;
    }
    
    public function imprimirSecretariosAction()
    {
        
        //FUN��O ACESSADA SOMENTE PELOS PERFIS DE TEC. DE PORTARIA
        if ($this->idPerfil != 128) {
            parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal", "ALERT");
        }
        
        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }
        $order = array();

        //==== parametro de ordenacao  ======//
        if ($this->_request->getParam("ordem")) {
            $ordem = $this->_request->getParam("ordem");
            if ($ordem == "ASC") {
                $novaOrdem = "DESC";
            } else {
                $novaOrdem = "ASC";
            }
        } else {
            $ordem = "ASC";
            $novaOrdem = "ASC";
        }

        //==== campo de ordenacao  ======//
        if ($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo = null;
            $order = array(1); //idManterPortaria
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('post');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $where = array();
        $where['a.stEstado = ?'] = 1; // 1=Atual; 0=Historico

        $tbManterPortaria = new tbManterPortaria();
        $total = $tbManterPortaria->listaSecretarios($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $tbManterPortaria->listaSecretarios($where, $order, $tamanho, $inicio);
        
        if (isset($get->xls) && $get->xls) {
            $html = '';
            $html .= '<table style="border: 1px">';
            $html .='<tr><td style="border: 1px dotted black; background-color: #EAF1DD; font-size: 16; font-weight: bold;" colspan="4">Lista de Secret�rios Cadastrados</td></tr>';
            $html .='<tr><td style="border: 1px dotted black; background-color: #EAF1DD; font-size: 10" colspan="4">Data do Arquivo: '. Data::mostraData() .'</td></tr>';
            $html .='<tr><td colspan="4"></td></tr>';
            
            $html .= '<tr>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Nome</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Cargo</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Portaria</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Dt.&nbsp;Cadastro</th>';
            $html .= '</tr>';
            
            foreach ($busca as $d) {
                $html .= '<tr>';
                $html .= '<td style="border: 1px dotted black;">'.$d->dsAssinante.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$d->dsCargo.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$d->dsPortaria.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.Data::tratarDataZend($d->dtPortariaPublicacao, 'Brasileira').'</td>';
                $html .= '</tr>';
            }
            $html .= '</table>';
            
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: inline; filename=Lista_Secretarios.ods;");
            echo $html;
            $this->_helper->viewRenderer->setNoRender(true);
        } else {
            $this->view->qtdRegistros = $total;
            $this->view->dados = $busca;
            $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        }
    }
    
    public function cadastrarSecretarioAction()
    {
    }
    
    public function incluirSecretarioAction()
    {
        
        //FUN��O ACESSADA SOMENTE PELOS PERFIS DE TEC. DE PORTARIA
        if ($this->idPerfil != 128) {
            parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal", "ALERT");
        }
        
        $tbManterPortaria = new tbManterPortaria();
//        $dt = explode('/', $_POST['dataPortaria']);
//        $data = $dt[2].'-'.$dt[1].'-'.$dt[0];
        $dados = array(
            'dsAssinante' => $_POST['nome'],
            'dsCargo' => $_POST['cargo'],
            'dsPortaria' => $_POST['portaria'],
//            'dtPortariaPublicacao' => $data,
            'dtPortariaPublicacao' => new Zend_db_Expr('GETDATE()'),
            'stEstado' => 1
        );
        $tb = $tbManterPortaria->inserir($dados);
        
        if ($tb) {
            parent::message('Dados inseridos com sucesso!', "configuracoes/secretarios", "CONFIRM");
        } else {
            parent::message('Nenhum registro encontrado.', "configuracoes/secretarios", "ERROR");
        }
    }
    
    public function editarSecretarioAction()
    {
        
        //FUN��O ACESSADA SOMENTE PELOS PERFIS DE TEC. DE PORTARIA
        if ($this->idPerfil != 128) {
            parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal", "ALERT");
        }
        
        $tbManterPortaria = new tbManterPortaria();
        $idManterPortaria = $_GET['idmp'];
        $dados = $tbManterPortaria->buscar(array('idManterPortaria = ?'=>$idManterPortaria))->current();
        $this->view->dados = $dados;
    }
    
    public function salvarSecretarioAction()
    {
        
        //FUN��O ACESSADA SOMENTE PELOS PERFIS DE TEC. DE PORTARIA
        if ($this->idPerfil != 128) {
            parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal", "ALERT");
        }
        
//        $dt = explode('/', $_POST['dataPortaria']);
//        $data = $dt[2].'-'.$dt[1].'-'.$dt[0];
        
        $tbManterPortaria = new tbManterPortaria();
        $d = array();
        $d['dsAssinante'] = $_POST['nome'];
        $d['dsCargo'] = $_POST['cargo'];
        $d['dsPortaria'] = $_POST['portaria'];
//        $d['dtPortariaPublicacao'] = $data;
        $d['dtPortariaPublicacao'] = new Zend_db_Expr('GETDATE()');
        $d['stEstado'] = 1;
                
        $where = "idManterPortaria = ".$_POST['idmp'];
        $tb = $tbManterPortaria->update($d, $where);
        
        if ($tb) {
            parent::message('Dados salvos com sucesso!', "configuracoes/secretarios", "CONFIRM");
        } else {
            parent::message('Nenhum registro encontrado.', "configuracoes/secretarios", "ERROR");
        }
    }
    
    public function excluirSecretarioAction()
    {
        
        //FUN��O ACESSADA SOMENTE PELOS PERFIS DE TEC. DE PORTARIA
        if ($this->idPerfil != 128) {
            parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal", "ALERT");
        }
        
        $tbManterPortaria = new tbManterPortaria();
        $idManterPortaria = $_GET['idmp'];
        $dados = array(
            'stEstado' => 0
        );
        $tb = $tbManterPortaria->alterarDados($dados, $idManterPortaria);
            
        if ($tb) {
            parent::message('Dados exclu�dos com sucesso!', "configuracoes/secretarios", "CONFIRM");
        } else {
            parent::message('Nenhum registro encontrado.', "configuracoes/secretarios", "ERROR");
        }
    }
} // fecha class
