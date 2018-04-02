<?php
/**
 * Classe para upload de arquivos
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package library
 * @subpackage library.MinC.Arquivo
 * @copyright © 2010 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 * 
 * MIMETYPE 								 EXTENSÕES
 * =================================================================
 * application/acad 					(dwg)
 * application/andrew-inset 			(ez)
 * application/clariscad 				(ccad)
 * application/drafting 				(drw)
 * application/dsptype 					(tsp)
 * application/dxf 						(dxf)
 * application/excel 					(xls)
 * application/i-deas 					(unv)
 * application/java 					(class)
 * application/java-archive 			(jar)
 * application/java-byte-code 			(class)
 * application/mac-binhex40 			(hqx)
 * application/mac-compactpro 			(cpt)
 * application/msword 					(doc)
 * application/octet-stream 			(bin, class, dms, exe, lha, lzh)
 * application/oda 						(oda)
 * application/ogg 						(ogg, ogm)
 * application/pdf 						(pdf)
 * application/pgp 						(pgp)
 * application/postscript 				(ai, eps, ps)
 * application/pro_eng 					(prt)
 * application/rtf 						(rtf)
 * application/set 						(set)
 * application/SLA 						(stl)
 * application/smil 					(smi, smil)
 * application/solids 					(sol)
 * application/STEP 					(step, stp)
 * application/vda 						(vda)
 * application/vnd.mif 					(mif)
 * application/vnd.ms-excel 			(xlc, xll, xlm, xls, xlw)
 * application/vnd.ms-powerpoint 		(pot, pps, ppt, ppz)
 * application/vnd.rim.cod 				(cod)
 * application/x-arj-compressed 		(arj)
 * application/x-bcpio 					(bcpio)
 * application/x-cdlink 				(vcd)
 * application/x-chess-pgn 				(pgn)
 * application/x-compressed 			(zip)
 * application/x-cpio 					(cpio)
 * application/x-csh 					(csh)
 * application/x-debian-package 		(deb)
 * application/x-director 				(dcr, dir, dxr)
 * application/x-dvi 					(dvi)
 * application/x-freelance 				(pre)
 * application/x-futuresplash 			(spl)
 * application/x-gtar 					(gtar)
 * application/x-gunzip 				(gz)
 * application/x-gzip 					(gz)
 * application/x-hdf 					(hdf)
 * application/x-ipix 					(ipx)
 * application/x-ipscript 				(ips)
 * application/x-java-class 			(class)
 * application/x-javascript 			(js)
 * application/x-koan 					(skd, skm, skp, skt)
 * application/x-latex 					(latex)
 * application/x-lisp 					(lsp)
 * application/x-lotusscreencam 		(scm)
 * application/x-mif 					(mif)
 * application/x-mplayer2 				(asx)
 * application/x-msaccess 				(mdb)
 * application/x-msdos-program 			(bat, com, exe)
 * application/x-netcdf 				(cdf, nc)
 * application/x-perl 					(pl, pm)
 * application/x-rar-compressed 		(rar)
 * application/x-sh 					(sh)
 * application/x-shar 					(shar)
 * application/x-shockwave-flash 		(swf)
 * application/x-stuffit 				(sit)
 * application/x-sv4cpio 				(sv4cpio)
 * application/x-sv4crc 				(sv4crc)
 * application/x-tar-gz 				(tar.gz, tgz)
 * application/x-tar 					(tar)
 * application/x-tcl 					(tcl)
 * application/x-tex 					(tex)
 * application/x-texinfo 				(texi, texinfo)
 * application/x-troff 					(roff, t, tr)
 * application/x-troff-man 				(man)
 * application/x-troff-me 				(me)
 * application/x-troff-ms 				(ms)
 * application/x-ustar 					(ustar)
 * application/x-wais-source 			(src)
 * application/x-zip-compressed 		(zip)
 * application/xml 						(xml)
 * application/zip 						(zip)
 * audio/basic 							(au, snd)
 * audio/mid 							(rmi)
 * audio/midi 							(kar, mid, midi)
 * audio/mpeg 							(mp2, mp3, mpga)
 * audio/TSP-audio 						(tsi)
 * audio/ulaw 							(au)
 * audio/x-aiff 						(aif, aifc, aiff)
 * audio/x-mpegurl 						(m3u)
 * audio/x-ms-wax 						(wax)
 * audio/x-ms-wma 						(wma)
 * audio/x-pn-realaudio 				(ram, rm)
 * audio/x-pn-realaudio-plugin 			(rpm)
 * audio/x-realaudio 					(ra)
 * audio/x-wav 							(wav)
 * chemical/x-pdb 						(pdb, xyz)
 * image/cmu-raster						(ras)
 * image/gif 							(gif)
 * image/ief 							(ief)
 * image/jpeg 							(jpe, jpeg, jpg)
 * image/png 							(png)
 * image/tiff 							(tif, tiff)
 * image/x-cmu-raster 					(ras)
 * image/x-icon 						(ico)
 * image/x-portable-anymap 				(pnm)
 * image/x-portable-bitmap 				(pbm)
 * image/x-portable-graymap 			(pgm)
 * image/x-portable-pixmap 				(ppm)
 * image/x-rgb 							(rgb)
 * image/x-xbitmap 						(xbm)
 * image/x-xpixmap 						(xpm)
 * image/x-xwindowdump 					(xwd)
 * model/iges 							(iges, igs)
 * model/mesh 							(mesh, msh, silo)
 * model/vrml 							(vrml, wrl)
 * multipart/x-zip 						(zip)
 * text/asp 							(asp)
 * text/css 							(css)
 * text/html 							(htm, html)
 * text/plain 							(asc, txt, c, cc, f90, f, h, hh)
 * text/richtext 						(rtx)
 * text/rtf 							(rtf)
 * text/sgml 							(sgm, sgml) 
 * text/tab-separated-values 			(tsv)
 * text/vnd.sun.j2me.app-descriptor 	(jad)
 * text/x-component 					(htc)
 * text/x-setext 						(etx)
 * text/xml 							(xml)
 * video/dl 							(dl)
 * video/fli 							(fli)
 * video/flv 							(flv)
 * video/gl 							(gl)
 * video/mpeg 							(mp2, mpe, mpeg, mpg)
 * video/quicktime 						(mov, qt)
 * video/vnd.vivo 						(viv, vivo)
 * video/x-fli 							(fli)
 * video/x-ms-asf 						(asf)
 * video/x-ms-asx 						(asx)
 * video/x-ms-wmv 						(wmv)
 * video/x-ms-wmx 						(wmx)
 * video/x-ms-wvx 						(wvx)
 * video/x-msvideo 						(avi)
 * video/x-sgi-movie 					(movie)
 * www/mime 							(mime)
 * x-conference/x-cooltalk 				(ice)
 * x-world/x-vrml 						(vrm, vrml)
 * application/vnd.ms-word.document.macroEnabled.12 						 (docm)
 * application/vnd.openxmlformats-officedocument.wordprocessingml.document 	 (docx)
 * application/vnd.ms-word.template.macroEnabled.12 						 (dotm)
 * application/vnd.openxmlformats-officedocument.wordprocessingml.template 	 (dotx)
 * application/vnd.ms-powerpoint.slideshow.macroEnabled.12 					 (ppsm)
 * application/vnd.openxmlformats-officedocument.presentationml.slideshow 	 (ppsx) 
 * application/vnd.ms-powerpoint.presentation.macroEnabled.12 				 (pptm)
 * application/vnd.openxmlformats-officedocument.presentationml.presentation (pptx)
 * application/vnd.ms-excel.sheet.binary.macroEnabled.12 					 (xlsb)
 * application/vnd.ms-excel.sheet.macroEnabled.12 							 (xlsm)
 * application/vnd.openxmlformats-officedocument.spreadsheetml.sheet 		 (xlsx)
 * application/vnd.ms-xpsdocument 											 (xps)
 */

class Upload
{
	// extensões autorizadas
	private $ext_aut = array(
		"application/arj",
		"application/excel",
		"application/gnutar",
		"application/mspowerpoint",
		"application/msword",
		"application/octet-stream",
		"application/pdf",
		"application/plain",
		"application/postscript",
		"application/powerpoint",
		"application/rtf",
		"application/vnd.ms-excel",
		"application/vnd.ms-excel.sheet.binary.macroEnabled.12",
		"application/vnd.ms-excel.sheet.macroEnabled.12",
		"application/vnd.ms-powerpoint",
		"application/vnd.ms-powerpoint.presentation.macroEnabled.12",
		"application/vnd.ms-powerpoint.slideshow.macroEnabled.12",
		"application/vnd.ms-word.document.macroEnabled.12",
		"application/vnd.ms-word.template.macroEnabled.12",
		"application/vnd.ms-xpsdocument",
		"application/vnd.openxmlformats-officedocument.presentationml.presentation",
		"application/vnd.openxmlformats-officedocument.presentationml.slideshow",
		"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
		"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
		"application/vnd.openxmlformats-officedocument.wordprocessingml.template",
		"application/vocaltec-media-file",
		"application/wordperfect",
		"application/x-bzip",
		"application/x-bzip2",
		"application/x-compressed",
		"application/x-excel",
		"application/x-gzip",
		"application/x-latex",
		"application/x-midi",
		"application/x-msexcel",
		"application/x-rtf",
		"application/x-shockwave-flash",
		"application/x-sit",
		"application/x-stuffit",
		"application/x-troff-msvideo",
		"application/x-zip-compressed",
		"application/xml",
		"application/zip",
		"audio/aiff",
		"audio/basic",
		"audio/midi",
		"audio/mod",
		"audio/mpeg",
		"audio/mpeg3",
		"audio/wav",
		"audio/x-aiff",
		"audio/x-au",
		"audio/x-mid",
		"audio/x-midi",
		"audio/x-mod",
		"audio/x-mpeg-3",
		"audio/x-ms-wma",
		"audio/x-wav",
		"audio/xm",
		"image/bmp",
		"image/gif",
		"image/jpg",
		"image/jpeg",
		"image/pjpeg",
		"image/png",
		"image/tiff",
		"image/x-png",
		"image/x-tiff",
		"image/x-windows-bmp",
		"multipart/x-gzip",
		"multipart/x-zip",
		"music/crescendo",
		"text/html",
		"text/plain",
		"text/richtext",
		"text/xml",
		"video/avi",
		"video/mpeg",
		"video/msvideo",
		"video/quicktime",
		"video/x-mpeg",
		"video/x-ms-asf",
		"video/x-ms-asf-plugin",
		"video/x-ms-wmv",
		"video/x-msvideo",
		"x-music/x-midi");



	/**
	 * Método construtor que elimina o limite de tempo de execução
	 * @access public
	 * @param void
	 * @return void
	 */
	public function __construct()
	{
		set_time_limit(0);
	}



	/**
	 * Upload de arquivos em geral
	 * @access public
	 * @param integer $obrig     = o envio do arquivo é obrigatório? (0 = nao ou 1 = sim)
	 * @param integer $limit_ext = limitar as extensões do arquivo? (0 = nao ou 1 = sim)
	 * @param integer $limit_tam = limitar o tamanho do arquivo? (0 ou 1)
	 * @param integer $limit_dim = limitar dimensão da imagem? (0 ou 1)
	 * @param string $caminho    = caminho onde os arquivos serão armazenados
	 * @param integer $sobresc   = se já existir o arquivo, indica se ele deve ser sobrescrito (0 ou 1)
	 * @return void
	 */
	public function uploadArquivo($obrig, $limit_ext, $limit_tam, $limit_dim, $caminho, $sobresc)
	{
		// tamanho máximo autorizado do arquivo (em bytes)
		$tam_aut = "10485760"; // 10 MB

		// extensões autorizadas
		$ext_aut = $this->ext_aut;


		// realiza a validação do(s) arquivo(s) enviado(s)
		for ($i = 0; $i < count($_FILES["arquivo"]["name"]); $i++)
		{
			// pega o nome, tipo, tamanho, nome temporário e erro no envio do arquivo
			$arq_nome      = $_FILES["arquivo"]["name"][$i];
			$arq_tipo      = $_FILES["arquivo"]["type"][$i];
			$arq_tamanho   = $_FILES["arquivo"]["size"][$i];
			$arq_nome_temp = $_FILES["arquivo"]["tmp_name"][$i];
			$arq_erro      = $_FILES["arquivo"]["error"][$i];

			// dimensões do arquivo em pixels (para imagens)
			if ($limit_dim == 1 && $arq_erro !== UPLOAD_ERR_NO_FILE)
			{
				$dimensao[$i] = getimagesize($arq_nome_temp);
				$largura      = 800;
				$altura       = 600;
			}

			try
			{
				// verifica se o envio do arquivo é obrigatório
				if ($obrig == 1 && $arq_erro === UPLOAD_ERR_NO_FILE)
				{
					throw new Exception("O envio do <strong>" . ($i + 1) . "º arquivo</strong> é obrigatório!");
				}
				// verifica se a extensão do arquivo faz parte das extensões autorizadas
				else if ($limit_ext == 1 && array_search($arq_tipo, $ext_aut) === FALSE && $arq_erro !== UPLOAD_ERR_NO_FILE && $arq_erro !== UPLOAD_ERR_INI_SIZE && $arq_erro !== UPLOAD_ERR_FORM_SIZE)
				{
					throw new Exception("A extensão do arquivo <strong>$arq_nome</strong> é inválida para upload!");
				}
				// verifica se o tamanho do arquivo é maior que o autorizado
				else if ($limit_tam == 1 && ($arq_tamanho > $tam_aut || $arq_erro === UPLOAD_ERR_INI_SIZE || $arq_erro === UPLOAD_ERR_FORM_SIZE))
				{
					throw new Exception("O arquivo <strong>$arq_nome</strong> deve ter no máximo " . ($tam_aut / (1024 * 1024)) . " MB!");
				}
				// largura da imagem
				else if ($limit_dim == 1 && $dimensao[0] > $largura)
				{
					throw new Exception("A largura da imagem <strong>$arq_nome</strong> não deve ultrapassar $largura pixels");
				}
				// altura da imagem
				else if ($limit_dim == 1 && $dimensao[1] > $altura)
				{
					throw new Exception("A altura da imagem <strong>$arq_nome</strong> não deve ultrapassar $altura pixels");
				}
				// verifica se já existe um arquivo com o mesmo nome
				else if ($sobresc == 0 && file_exists("$caminho/$arq_nome") && $obrig == 1)
				{
					throw new Exception("O arquivo <strong>$arq_nome</strong> já existe!");
				}
			} // fecha try
			catch (Exception $e)
			{
				echo $e->getMessage();
				exit();
			}
		} // fecha for


		// se não houver erros, tenta enviar o(s) arquivo(s)
		for ($i = 0; $i < count($_FILES["arquivo"]["name"]); $i++)
		{
			// pega o nome, tipo, tamanho, nome temporário e erro no envio do arquivo
			$arq_nome      = $_FILES["arquivo"]["name"][$i];
			$arq_tipo      = $_FILES["arquivo"]["type"][$i];
			$arq_tamanho   = $_FILES["arquivo"]["size"][$i];
			$arq_nome_temp = $_FILES["arquivo"]["tmp_name"][$i];
			$arq_erro      = $_FILES["arquivo"]["error"][$i];

			try
			{
				if ((!move_uploaded_file($arq_nome_temp, "$caminho/$arq_nome") || $arq_erro === UPLOAD_ERR_PARTIAL) && $arq_erro !== UPLOAD_ERR_NO_FILE)
				{
					throw new Exception("Erro ao tentar efetuar upload do arquivo <strong>$arq_nome</strong>!");
				}
			} // fecha try
			catch (Exception $e)
			{
				echo $e->getMessage();
				exit();
			}
		} // fecha for

	} // fecha método uploadArquivo()



	/**
	 * Upload de imagens
	 * @access public
	 * @param integer $obrig     = o envio do arquivo é obrigatório? (0 = nao ou 1 = sim)
	 * @param integer $limit_ext = limitar as extensões do arquivo? (0 = nao ou 1 = sim)
	 * @param integer $limit_tam = limitar o tamanho do arquivo? (0 ou 1)
	 * @param integer $limit_dim = limitar dimensão da imagem? (0 ou 1)
	 * @param string $caminho    = caminho onde os arquivos serão armazenados
	 * @param integer $sobresc   = se já existir o arquivo, indica se ele deve ser sobrescrito (0 ou 1)
	 * @return void
	 */
	public function uploadImagem($obrig, $limit_ext, $limit_tam, $limit_dim, $caminho, $sobresc)
	{
		$this->ext_aut = array();
		$this->ext_aut = array(
			"image/bmp",
			"image/gif",
			"image/jpg",
			"image/jpeg",
			"image/pjpeg",
			"image/png",
			"image/tiff",
			"image/x-png",
			"image/x-tiff",
			"image/x-windows-bmp");

		$this->uploadArquivo($obrig, $limit_ext, $limit_tam, $limit_dim, $caminho, $sobresc);
	} // fecha método uploadImagem()



	/**
	 * Upload de audios
	 * @access public
	 * @param integer $obrig     = o envio do arquivo é obrigatório? (0 = nao ou 1 = sim)
	 * @param integer $limit_ext = limitar as extensões do arquivo? (0 = nao ou 1 = sim)
	 * @param integer $limit_tam = limitar o tamanho do arquivo? (0 ou 1)
	 * @param integer $limit_dim = limitar dimensão da imagem? (0 ou 1)
	 * @param string $caminho    = caminho onde os arquivos serão armazenados
	 * @param integer $sobresc   = se já existir o arquivo, indica se ele deve ser sobrescrito (0 ou 1)
	 * @return void
	 */
	public function uploadAudio($obrig, $limit_ext, $limit_tam, $limit_dim, $caminho, $sobresc)
	{
		$this->ext_aut = array();
		$this->ext_aut = array(
			"audio/midi",
			"audio/mpeg",
			"audio/mpeg3",
			"audio/wav",
			"audio/x-au",
			"audio/x-mid",
			"audio/x-midi",
			"audio/x-mpeg-3",
			"audio/x-ms-wma",
			"audio/x-wav");

		$this->uploadArquivo($obrig, $limit_ext, $limit_tam, $limit_dim, $caminho, $sobresc);
	} // fecha método uploadAudio()



	/**
	 * Upload de videos
	 * @access public
	 * @param integer $obrig     = o envio do arquivo é obrigatório? (0 = nao ou 1 = sim)
	 * @param integer $limit_ext = limitar as extensões do arquivo? (0 = nao ou 1 = sim)
	 * @param integer $limit_tam = limitar o tamanho do arquivo? (0 ou 1)
	 * @param integer $limit_dim = limitar dimensão da imagem? (0 ou 1)
	 * @param string $caminho    = caminho onde os arquivos serão armazenados
	 * @param integer $sobresc   = se já existir o arquivo, indica se ele deve ser sobrescrito (0 ou 1)
	 * @return void
	 */
	public function uploadVideo($obrig, $limit_ext, $limit_tam, $limit_dim, $caminho, $sobresc)
	{
		$this->ext_aut = array();
		$this->ext_aut = array(
			"application/x-shockwave-flash",
			"video/avi",
			"video/mpeg",
			"video/msvideo",
			"video/quicktime",
			"video/x-mpeg",
			"video/x-ms-asf",
			"video/x-ms-asf-plugin",
			"video/x-ms-wmv",
			"video/x-msvideo",
			"x-music/x-midi");

		$this->uploadArquivo($obrig, $limit_ext, $limit_tam, $limit_dim, $caminho, $sobresc);
	} // fecha método uploadVideo()



	/**
	 * Upload de audios e videos
	 * @access public
	 * @param integer $obrig     = o envio do arquivo é obrigatório? (0 = nao ou 1 = sim)
	 * @param integer $limit_ext = limitar as extensões do arquivo? (0 = nao ou 1 = sim)
	 * @param integer $limit_tam = limitar o tamanho do arquivo? (0 ou 1)
	 * @param integer $limit_dim = limitar dimensão da imagem? (0 ou 1)
	 * @param string $caminho    = caminho onde os arquivos serão armazenados
	 * @param integer $sobresc   = se já existir o arquivo, indica se ele deve ser sobrescrito (0 ou 1)
	 * @return void
	 */
	public function uploadAudioVideo($obrig, $limit_ext, $limit_tam, $limit_dim, $caminho, $sobresc)
	{
		$this->ext_aut = array();
		$this->ext_aut = array(
			"audio/midi",
			"audio/mpeg",
			"audio/mpeg3",
			"audio/wav",
			"audio/x-au",
			"audio/x-mid",
			"audio/x-midi",
			"audio/x-mpeg-3",
			"audio/x-ms-wma",
			"audio/x-wav",
			"application/x-shockwave-flash",
			"video/avi",
			"video/mpeg",
			"video/msvideo",
			"video/quicktime",
			"video/x-mpeg",
			"video/x-ms-asf",
			"video/x-ms-asf-plugin",
			"video/x-ms-wmv",
			"video/x-msvideo",
			"x-music/x-midi");

		$this->uploadArquivo($obrig, $limit_ext, $limit_tam, $limit_dim, $caminho, $sobresc);
	} // fecha método uploadAudioVideo()



	/**
	 * Upload de documentos em geral
	 * @access public
	 * @param integer $obrig     = o envio do arquivo é obrigatório? (0 = nao ou 1 = sim)
	 * @param integer $limit_ext = limitar as extensões do arquivo? (0 = nao ou 1 = sim)
	 * @param integer $limit_tam = limitar o tamanho do arquivo? (0 ou 1)
	 * @param integer $limit_dim = limitar dimensão da imagem? (0 ou 1)
	 * @param string $caminho    = caminho onde os arquivos serão armazenados
	 * @param integer $sobresc   = se já existir o arquivo, indica se ele deve ser sobrescrito (0 ou 1)
	 * @return void
	 */
	public function uploadDocumento($obrig, $limit_ext, $limit_tam, $limit_dim, $caminho, $sobresc)
	{
		$this->ext_aut = array();
		$this->ext_aut = array(
			"application/excel",
			"application/mspowerpoint",
			"application/msword",
			"application/pdf",
			"application/plain",
			"application/powerpoint",
			"application/rtf",
			"application/vnd.ms-excel",
			"application/vnd.ms-excel.sheet.binary.macroEnabled.12",
			"application/vnd.ms-excel.sheet.macroEnabled.12",
			"application/vnd.ms-powerpoint",
			"application/vnd.ms-powerpoint.presentation.macroEnabled.12",
			"application/vnd.ms-powerpoint.slideshow.macroEnabled.12",
			"application/vnd.ms-word.document.macroEnabled.12",
			"application/vnd.ms-word.template.macroEnabled.12",
			"application/vnd.ms-xpsdocument",
			"application/vnd.openxmlformats-officedocument.presentationml.presentation",
			"application/vnd.openxmlformats-officedocument.presentationml.slideshow",
			"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
			"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
			"application/vnd.openxmlformats-officedocument.wordprocessingml.template",
			"application/wordperfect",
			"application/x-excel",
			"application/x-msexcel",
			"application/x-rtf",
			"application/xml",
			"text/html",
			"text/plain",
			"text/richtext",
			"text/xml");

		$this->uploadArquivo($obrig, $limit_ext, $limit_tam, $limit_dim, $caminho, $sobresc);
	} // fecha método uploadDocumento()



	/**
	 * Upload de arquivos .txt
	 * @access public
	 * @param integer $obrig     = o envio do arquivo é obrigatório? (0 = nao ou 1 = sim)
	 * @param integer $limit_ext = limitar as extensões do arquivo? (0 = nao ou 1 = sim)
	 * @param integer $limit_tam = limitar o tamanho do arquivo? (0 ou 1)
	 * @param integer $limit_dim = limitar dimensão da imagem? (0 ou 1)
	 * @param string $caminho    = caminho onde os arquivos serão armazenados
	 * @param integer $sobresc   = se já existir o arquivo, indica se ele deve ser sobrescrito (0 ou 1)
	 * @return void
	 */
	public function uploadTXT($obrig, $limit_ext, $limit_tam, $limit_dim, $caminho, $sobresc)
	{
		$this->ext_aut = array("text/plain");

		$this->uploadArquivo($obrig, $limit_ext, $limit_tam, $limit_dim, $caminho, $sobresc);
	} // fecha método uploadTXT()



	/**
	 * Pega a extensão de um arquivo
	 * @access public
	 * @static
	 * @param string $arquivo
	 * @return string $arquivo
	 */
	public static function getExtensao($arquivo)
	{
		$extensao = explode(".", $arquivo);
		$extensao = array_reverse($extensao);
		$extensao = str_replace('\'', '', $extensao[0]);

		return $extensao;
	}



	/**
	 * Transforma o arquivo em binário
	 * @access public
	 * @static
	 * @param string $arquivoTemp
	 * @return string $arquivo
	 */
	public static function setBinario($arquivoTemp)
	{
		$arquivoString = file_get_contents($arquivoTemp);
		$arquivoData   = unpack("H*hex", $arquivoString);
		$arquivo       = "0x".$arquivoData['hex'];

		return $arquivo;
	}



	/**
	 * Transforma o arquivo em hash
	 * @access public
	 * @static
	 * @param string $arquivoTemp
	 * @return string $arquivo
	 */
	public static function setHash($arquivoTemp)
	{
		$arquivo = md5_file($arquivoTemp);

		return $arquivo;
	} // fecha setHash()

        public static function cadastrarArquivosMult($files = array()) {
        	if (empty($files)) {
        		$files = $_FILES;
        	}
            if(!empty($files)) {
            for ($i = 0; $i < count($files["arquivo"]["name"]); $i++)
            {
                // pega as informações do arquivo
                $arquivoNome     = $files['arquivo']['name'][$i]; // nome
                $arquivoTemp     = $files['arquivo']['tmp_name'][$i]; // nome temporário
                $arquivoTipo     = $files['arquivo']['type'][$i]; // tipo
                $arquivoTamanho  = $files['arquivo']['size'][$i]; // tamanho
                if (!empty($arquivoNome) && !empty($arquivoTemp))
                {
                    $arquivoExtensao = Upload::getExtensao($arquivoNome); // extensão
                    $arquivoBinario  = Upload::setBinario($arquivoTemp); // binário
                    $arquivoHash     = Upload::setHash($arquivoTemp); // hash
                }

                // cadastra dados do arquivo
                $dadosArquivo = array(
                        'nmArquivo'  => $arquivoNome,
                        'sgExtensao' => $arquivoExtensao,
                        'dsTipo'     => $arquivoTipo,
                        'nrTamanho'  => $arquivoTamanho,
                        'dtEnvio'    => new Zend_Db_Expr('GETDATE()'),
                        'dsHash'     => $arquivoHash,
                        'stAtivo'    => 'A');
                $cadastrarArquivo = ArquivoDAO::cadastrar($dadosArquivo);

                // pega o id do último arquivo cadastrado
                $idUltimoArquivo = ArquivoDAO::buscarIdArquivo();
                $idUltimoArquivo = (int) $idUltimoArquivo[0]->id;

                // cadastra o binário do arquivo
                $dadosBinario = array(
                        'idArquivo' => $idUltimoArquivo,
                        'biArquivo' => $arquivoBinario);
                $cadastrarBinario = ArquivoImagemDAO::cadastrar($dadosBinario);
            }} // fecha for
        }

        public function getMimeType($ext){
            for($i=0; $i<count($this->ext_aut); $i++){
                $auxExt = explode("/", $this->ext_aut[$i]);
                if(is_array($auxExt)){
                    $pos = strpos(strtolower($auxExt[1]), strtolower($ext));
                    if($pos !== false){
                        return $this->ext_aut[$i];
                    }
                }
            }

            return false;
        }

        
} // fecha class