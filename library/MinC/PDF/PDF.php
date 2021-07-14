<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PDF
 *
 * @author 01373930160
 */
class PDF {

    private $entrada;
    private $tipoRelatorio;
    private $nomeArquivo;

    public function __construct($entrada, $tipoRelatorio, $nome_arquivo = '') {
        $this->entrada = $entrada;
        $this->nomeArquivo = $nome_arquivo;
        $this->tipoRelatorio = $tipoRelatorio;
    }

    public function gerarRelatorio($orientacao_papel = null) {

        switch ($this->tipoRelatorio) {
            case 'pdf':
                return $this->gerarRelatorioPDF($orientacao_papel);
                break;
            case 'excel':
                return $this->gerarRelatorioExcel();
                break;
            case 'html':
                return $this->gerarRelatorioHTML();
                break;
            case 'rtf':
                return $this->gerarRelatorioRTF();
                break;
            default:
                return false;
                break;
        }
    }

    private function gerarRelatorioPDF($orientacao_papel) {

        if (!$this->nomeArquivo) {
            $this->nomeArquivo = 'relatorio_pdf';
        }
        try {
            require_once('dompdf/dompdf_config.inc.php');
            $autoloader = Zend_Loader_Autoloader::getInstance();
            $autoloader->pushAutoloader('DOMPDF_autoload', '');

            $dompdf = new DOMPDF();

            //$papel = array(0,0,355.00,866.20);


     		// se a vari�vel Não for definida, seta para vertical, caso contr�rio, seta para horizontal
     		$orientacao = (empty($orientacao_papel) || $orientacao_papel == null) ? 'portrait' : 'landscape';
     		$dompdf->set_paper("letter", $orientacao);

            $dompdf->load_html($this->entrada);

            $dompdf->render();
            $dompdf->stream($this->nomeArquivo . '.pdf');
        } catch (Exception $e) {
            die('ERRO:' . $e->getMessage() . 'Line:' . $e->getLine());
        }
    }

    private function gerarRelatorioExcel() {

        if (!$this->nomeArquivo) {
            $this->nomeArquivo = 'relatorio_excel';
        }
        header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header("Pragma: no-cache");
        header("Content-type: application/xls; name=" . $this->nomeArquivo . ".ods");
        header("Content-Disposition: attachment; filename=" . $this->nomeArquivo . ".ods");
        header("Content-Description: MID Gera excel");
        return $this->entrada;
        exit;
    }

    private function gerarRelatorioRTF() {


        if (!$this->nomeArquivo) {
            $this->nomeArquivo = 'relatorio_rtf';
        }

        header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header("Pragma: no-cache");
        header("Content-type: application/rtf; name=" . $this->nomeArquivo . ".rtf");
        header("Content-Disposition: attachment; filename=" . $this->nomeArquivo . ".rtf");
        header("Content-Description: MID Gera rtf");
        return $this->entrada;
        exit;
    }

    private function gerarRelatorioHTML() {

        header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header("Pragma: no-cache");
        header("Content-type: text/html");

        echo  $this->entrada;
        exit;
    }

}

?>
