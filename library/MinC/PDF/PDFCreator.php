<?php

class PDFCreator {

    /**
     *
     * @var type
     */
    private $_entrada;
    private $_orientacaoPapel;

    /**
     *
     * @param type $entrada
     */
    public function __construct($entrada , $orientacao = 'P') {

        $this->_entrada = $entrada;
        $this->_orientacaoPapel = $orientacao;
    }

    /**
     * @param $quebra_linha
     * auto: � o valor padr�o. S� coloca a quebra de página se for necess�rio;
     * always: coloca sempre uma quebra de linha, depois ou antes do elemento;
     * avoid: evita colocar uma quebra de linha antes ou depois;
     * left: insere uma ou duas quebras de página, de modo que se possa assegurar que a seguinte página seja uma página esquerda (page-break-after)
     * 		ou para assegurar que a página onde se come�a o elemento seja uma página esquerda (page-break-before).
     * 		Imaginemos um livro aberto, que tem páginas � esquerda e � direita para saber ao que se refere uma página esquerda.
	 * right: insere uma ou duas quebras de página, para assegurar que se possa inserir o elemento ao princ�pio de uma página de direita (page-break-before)
	 * 		ou para assegurar que depois do elemento comece uma página direita (page-break-after).
     */
    public function gerarPdf($quebra_linha = 'avoid') {

        ini_set("memory_limit", "2048M");
        set_time_limit(-1);
        error_reporting(0);


        $patterns = array();
        $patterns[] = '/<thead>/is';
        $patterns[] = '/<\/thead>/is';
        $patterns[] = '/<tbody>/is';
        $patterns[] = '/<\/tbody>/is';
        $patterns[] = '/<a.*?>/is';
        $patterns[] = '/�/is';

        $replaces = array();
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '&ndash;';



        $this->_entrada = preg_replace($patterns, $replaces, $this->_entrada);


        $output = '
          <html>
            <head>
                <style type="text/css">
        ';
        if($this->_orientacaoPapel == 'L'){
            $output .= '
                @page {
                    size:landscape;
                }
                ';
        }
        $output .= '
                *, p {
                    font-family:"Lucida Grande", Verdana, Arial, sans-serif;
                    font-size:10px;
                }
                .texto-pequeno {
                    font-size:8px;
                }
                table { border: 1px solid #666666;  border-collapse:collapse; border-color:#ccc; }

                td, th {
                    border: 1px solid #666666;
                    line-height:13px;
                    vertical-align:top;
                    padding:4px;
                    }

                th {
                    background:#D0D0D0;
                    text-transform:uppercase;
                    font-weight: bold;

                }

                td.titulo {
                    padding: 3px;
                    font-weight:bold;
                    background:#E5E5E5;
                }
                .pagebreak {
                    page-break-after: '.$quebra_linha.';
                }
                .zebrar {
                    background:#F3F3F3;
                }
                table, tr, th, td {
                    page-break-before: '.$quebra_linha.';
                    page-break-after: '.$quebra_linha.';
                    page-break-inside: '.$quebra_linha.';
                }
              </style>
            </head>
            <body>
        ';

        $output .= $this->_entrada;


        $output .= '
            </body>
          </html>
          ';


        $mpdf = new mPDF();

        $output = utf8_encode($output);

        $mpdf->WriteHTML($output);

        $mpdf->Output();

    }

}
?>
