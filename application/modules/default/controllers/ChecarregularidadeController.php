<?php

class ChecarRegularidadeController extends MinC_Controller_Action_Abstract
{
    /**
     * Reescreve o método init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        ini_set('default_charset', 'iso-8859-1');
        $this->_helper->layout->disableLayout();
        $buscaprojeto = new Regularidade();
        $CgcCpf = $_GET['CgcCpf'];
        if (!empty($CgcCpf)) {
            $CgcCpf = str_replace(".", "", $CgcCpf);
            $CgcCpf = str_replace("-", "", $CgcCpf);
            $CgcCpf = str_replace("/", "", $CgcCpf);
            $valor = strlen($CgcCpf);

            if ($valor == 14) {
                $salic = $buscaprojeto->buscarSalic($CgcCpf);
                if (!empty($salic)) {
                    $datasalic = $salic[0]->Habilitado;
                } else {
                    $datasalic = 'S';
                }

                $cqte = $buscaprojeto->buscarCQTE($CgcCpf);

                if (!empty($cqte)) {
                    $datacqte = $cqte[0]->DtValidade;
                    $datacqte = data::tratarDataZend($datacqte, 'americano');
                } else {
                    $datacqte = 'E';
                }
                $cqtf = $buscaprojeto->buscarCQTF($CgcCpf);
                if (!empty($cqtf)) {
                    $datacqtf = $cqtf[0]->DtValidade;
                    $datacqtf = data::tratarDataZend($datacqtf, 'americano');
                } else {
                    $datacqtf = 'E';
                }

                $fgts = $buscaprojeto->buscarFGTS($CgcCpf);
                if (!empty($fgts)) {
                    $datafgts = $fgts[0]->DtValidade;
                    $datafgts = data::tratarDataZend($datafgts, 'americano');
                } else {
                    $datafgts = 'E';
                }
                $inss = $buscaprojeto->buscarINSS($CgcCpf);
                if (!empty($inss)) {
                    $datainss = $inss[0]->DtValidade;
                    $datainss = data::tratarDataZend($datainss, 'americano');
                } else {
                    $datainss = 'E';
                }

                $cadin = $buscaprojeto->buscarCADIN($CgcCpf);

                if (!empty($cadin)) {
                    $this->view->buscarcadin = $cadin;
                } else {
                    $this->view->buscarcadin = null;
                }

                $this->view->tipoCgcCpf = "CNPJ";
                $this->view->buscarsalic = $datasalic;
                $this->view->buscarcqte = $datacqte;
                $this->view->buscarcqtf = $datacqtf;
                $this->view->buscarfgts = $datafgts;
                $this->view->buscarinss = $datainss;
            } else {
                $salic = $buscaprojeto->buscarSalic($CgcCpf);
                if (!empty($salic)) {
                    $datasalic = $salic[0]->Habilitado;
                } else {
                    $datasalic = 'S';
                }

                $cqtf = $buscaprojeto->buscarCQTF($CgcCpf);
                if (!empty($cqtf)) {
                    $datacqtf = $cqtf[0]->DtValidade;
                    $datacqtf = data::tratarDataZend($datacqtf, 'americano');
                } else {
                    $datacqtf = 'E';
                }
                $cadin = $buscaprojeto->buscarCADIN($CgcCpf);

                if (!empty($cadin)) {
                    $this->view->buscarcadin = $cadin;
                } else {
                    $this->view->buscarcadin = null;
                }

                $this->view->buscarsalic = $datasalic;
                $this->view->buscarcqtf = $datacqtf;
            }
        } else {
            parent::message("CPF ou CNPJ n&atilde;o Informado!", "checarregularidade/index", "ERROR");
        }
    }

    public static function compararData($dataatual, $databanco)
    {
        if ($databanco != 'E') {
            $valor = Data::CompararDatas($dataatual, $databanco);
            if ($valor >= 1) {
                return (int)$valor . "  Dias";
            } else {
                return "Vencida";
            }
        } else {
            return "N&atilde;o Lan&ccedil;ada";
        }
    }

    public static function compararCadin($dtcadin, $situacao, $dataatual)
    {
        $valor = Data::CompararDatas($dataatual, $dtcadin);

        if ($valor == 0) {
            if ($situacao == 0) {
                return "Pendente";
            } else {
                return "N&atilde;o Pendente";
            }
        } else {
            return "Vencida";
        }
    }

    public static function compararSalic($situacao)
    {
        switch ($situacao) {
            case 'N':
                return "Inabilitado";
                break;
            case 'S':
                return "Habilitado";
                break;
        }
    }
}
