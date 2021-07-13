<?php
//data
function ConverteData($data, $formato, $locale='pt_BR')
{
	if (!$data)
		return null;
    $data=trim($data);
    setlocale(LC_ALL, $locale);
    $timestamp=parse_data($data, $locale);
    return
        format_data($timestamp, $formato);
}


function DropDownMes($nome, $valor, $locale='pt_BR')
{
	global $nomedomes;
	nome_do_mes(0, $locale);

	echo '<SELECT NAME="'.$nome.'">';
	echo '<OPTION VALUE="0">---  m�s  ---</OPTION>';
	foreach($nomedomes as $n=>$nome)
	{
		echo '<OPTION VALUE="'.$n.'"';
		if($valor==$n) echo ' SELECTED';
		echo '>'.$nome.'</OPTION>'.chr(13);
	}
	echo '</SELECT>';
}


function datadiff($data,$dif, $formato)
{
	$timestamp=ConverteData($data,8)+(86400*$dif);
	return ConverteData($timestamp,$formato);
}


function daysInMonth($timestamp=0)
{
	if(!$timestamp) $timestamp=time();
	for($thisDay=1;$thisDay<=33;$thisDay++)
	{
		if(!checkdate(date('m', $timestamp), $thisDay, date('Y', $timestamp))) break;
	}
	return ($thisDay-1);
}


function periodo($data1, $data2, $locale='pt_BR')
{

	global $nomedomes;
	nome_do_mes(0, $locale);

	$timestamp1=ConverteData($data1, 8);
	$timestamp2=ConverteData($data2, 8);

	if(date("Ymd",$timestamp1)==date("Ymd",$timestamp2))
	{
		$saida='Dia '.date("d ",$timestamp1).' de '.ConverteData($timestamp1, 18).' de '.date("Y",$timestamp2);
	}
		else
	{
		if(date("Y",$timestamp1)==date("Y",$timestamp2))
		{
			if (date("m",$timestamp1)==date("m",$timestamp2))
			{
				$saida='De '.date("d ",$timestamp1).' a '.date("d ",$timestamp2).' de '.ConverteData($timestamp1, 18).' de '.date("Y",$timestamp2);
			}
				else
			{
				$saida='De '.date("d ",$timestamp1).' de '.ConverteData($timestamp1, 18).' a '.date("d ",$timestamp2).' de '.ConverteData($timestamp2, 18).' de '.date("Y",$timestamp2);
			}
		}
			else
		{
			$saida='De '.date("d ",$timestamp1).' de '.ConverteData($timestamp1, 18).' de '.date("Y",$timestamp1).' a '.date("d ",$timestamp2).' de '.ConverteData($timestamp2, 18).' de '.date("Y",$timestamp2);
		}
	}

	return $saida;
}


function nome_do_mes($mes, $locale='pt_BR')
{
	global $nomedomes;
	global $nomediasemana;

	$mes=substr('0'.$mes, -2);
	switch($locale)
	{
		case 'en_US':
			$nomedomes = array('01'=>'january','02'=>'february','03'=>'march','04'=>'april','05'=>'may','06'=>'june','07'=>'july','08'=>'august','09'=>'september','10'=>'october','11'=>'november','12'=>'december');
			$nomediasemana=array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
		break;

		case 'pt_BR':
			$nomedomes=array('01'=>"janeiro",'02'=>"fevereiro",'03'=>"mar&ccedil;o",'04'=>"abril",'05'=>"maio",'06'=>"junho",'07'=>"julho",'08'=>"agosto",'09'=>"setembro",'10'=>"outubro",'11'=>"novembro",'12'=>"dezembro");
			$nomediasemana=array("Domingo","Segunda-feira","Ter�a-feira","Quarta-feira","Quinta-feira","Sexta-feira","S�bado");
		break;
	}

	if(($mes>=1)&&($mes<=12)) return $nomedomes[$mes];
}


function ultimo_dia_mes($mes, $ano)
{
	$lastday=mktime(0,0,0,($mes+1),0,$ano);
	return strftime("%d", $lastday);
}


function parse_data($data, $locale='pt_BR')
{
	global $nomedomes;
	global $nomediasemana;
	nome_do_mes(0);

	setlocale(LC_ALL, $locale);
	$saida=array(0,0,0,0,0,0,0);

//	echo 'data: '.$data.'<br>';
	do {
		if(preg_match('/\A(\d{10})\Z/is', $data, $results))
		{
			// A data encontrada � provavelmente um UNIX timestamp
			$saida[6]=date('s', $results[1]);
			$saida[5]=date('i', $results[1]);
			$saida[4]=date('H', $results[1]);
			$saida[3]=date('d', $results[1]);
			$saida[2]=date('m', $results[1]);
			$saida[1]=date('Y', $results[1]);
			break;
		}

		if(preg_match('/\A(\d{4})-?(\d{2})-?(\d{2})\Z/is', $data, $saida))
		{
			// A data encontrada est� no formato YYYYMMDD ou YYYY-MM-DD
			$saida[4]=12;
			break;
		}

		if(preg_match('/\A(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})\Z/is', $data, $saida))
		{
			// A data encontrada est� no formato YYYYMMDDHHMMSS
			if (!isset($saida[4])) $saida[4]="00";
			if (!isset($saida[5])) $saida[5]="00";
			if (!isset($saida[6])) $saida[6]="00";
			break;
		}

		if(preg_match('/\A(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})\Z/is', $data, $saida))
		{
			// A data encontrada est� no formato YYYY-MM-DD HH:MM:SS
			if (!isset($saida[4])) $saida[4]="00";
			if (!isset($saida[5])) $saida[5]="00";
			if (!isset($saida[6])) $saida[6]="00";
			break;
		}

		if(preg_match('/\A(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2}):(\d{2})\Z/is', $data, $saida))
		{
			// A data encontrada est� no formato DD/MM/YYYY HH:MM:SS
			$t = $saida[1];
			$saida[1]=$saida[3];
			$saida[3]=$t;
			if (!isset($saida[4])) $saida[4]="00";
			if (!isset($saida[5])) $saida[5]="00";
			if (!isset($saida[6])) $saida[6]="00";
			break;
		}


		$eval_line='$saida=parse_data_'.$locale."('".$data."');";
		//echo "<p>$eval_line</p>";
		eval($eval_line);
		if($saida)
		{
			break;
		}
		else
		{
		//	echo $data;
			return false;
		}
		echo 'A data nao foi encontrada';
	} while(0);
				//checkdate ( int month, int day, int year)
		if(checkdate ($saida[2], $saida[3], $saida[1]))
		{
			if (!isset($saida[4])) $saida[4]="00";
			if (!isset($saida[5])) $saida[5]="00";
			if (!isset($saida[6])) $saida[6]="00";
//			mktime (int hour, int minute, int second, int month, int day, int year, int [ is_dst ] );
			return mktime($saida[4], $saida[5], $saida[6], $saida[2], $saida[3], $saida[1]);
		} else {
			return false;
		}
	}


	function parse_data_pt_BR($data)
	{
		global $nomedomes;
		global $nomediasemana;
		nome_do_mes(0, 'pt_BR');
		setlocale(LC_ALL,'pt_BR');
		$saida[4]=12;

	do {
			if(preg_match('|(\d{1,2})[\/\.\-](\d{1,2})[\/\.\-](\d{2,4})\b|is', $data, $results))
			{
				// A data encontrada est� no formato DD/MM/YYYY
				$saida[1]=$results[3];
				$saida[2]=$results[2];
				$saida[3]=$results[1];
				break;
			}

			foreach($nomedomes as $mes_num=>$mes_comp)
			{
				if(preg_match('#.*?(\d{1,2}).*?('.substr($mes_comp, 0, 3).'(?:'.substr($mes_comp, 4).')?)(.*)#is', $data, $results))
				{
					$saida[3]=substr('0'.$results[1],-2);
					$saida[2]=$mes_num;

					if(preg_match('|(.*?)(\d{1,2})[:h](\d{2})[:]?(\d{2})?\s?([PA]M)?|is',$results[3], $results))
					{
						$saida[4]=$results[2];
						$saida[5]=$results[3];
						$saida[6]=$results[4];

						if(trim(strtoupper($results[5]))=='PM') $saida[4]+=12;

						if(preg_match('|.*?(\d{2,4})|is',$results[1], $results))
						{
							$saida[1]=$results[1];
						} else $saida[1]=date('Y');
					}
						else
					{
						$saida[1]=date('Y');
					}

					break 2;
				}
			}

			if (preg_match('#(\d{2})\|(\d{2})\|(\d{4})#', $data, $results )) {
				// formato incomum: dd|mm|yyyy
				$saida[1]=$results[3];
				$saida[2]=$results[2];
				$saida[3]=$results[1];
				break;
			}

			return false;
		} while(0);


		if(preg_match('|(.*?)(\d{1,2})[:h](\d{2})[:]?(\d{2})?\s?([PA]M)?|is',$data, $results))
		{
			$saida[4]=$results[2];
			$saida[5]=$results[3];
			if (isset($results[4])) $saida[6]=$results[4];
			if (isset($results[5]))
				if(trim(strtoupper($results[5]))=='PM') $saida[4]+=12;
		}

		return $saida;
	}


function parse_data_en_US($data)
	{
		global $nomedomes;
		global $nomediasemana;
		nome_do_mes(0, 'en_US');

		setlocale(LC_ALL,'en_US');
//		$saida=array(0,0,0,0,0,0,0);

	do {
			if(preg_match('|(\d{1,2})[\/\.](\d{1,2})[\/\.](\d{2,4})\b|is', $data, $results))
			{
				// A data encontrada est� no formato MM/DD/YYYY
				$saida[1]=$results[3];
				$saida[2]=$results[1];
				$saida[3]=$results[2];
				break;
			}

			foreach($nomedomes as $mes_num=>$mes_comp)
			{
				$re = '#('.substr($mes_comp, 0, 3);
				if (strlen($mes_comp)>3) $re.= '(?:' . (substr($mes_comp, 3)).')?';
				$re.= ').*?(\d{1,2})(.*)$#is';
				if(preg_match($re, $data, $results))
				{
					$saida[3]=substr('0'.$results[2],-2);
					$saida[2]=$mes_num;

					//by dan:
					if(preg_match('|^.*?(\d{2,4})|is',$results[3], $res_ano))
						$saida[1]=$res_ano[1];
					else
						$saida[1]=date('Y');
					// fim

					if(preg_match('|(.*?)(\d{1,2})[:h](\d{2})[:]?(\d{2})?\s?([PA]M)?.*|is', $results[3], $results))
					{
						$saida[4]=$results[2];
						$saida[5]=$results[3];
						$saida[6]=$results[4];

						if(trim(strtoupper($results[5]))=='PM') $saida[4]+=12;

						/*
						if(preg_match('|.*?(\d{2,4})|is',$results[1], $results))
						{
							$saida[1]=$results[1];
						} else $saida[1]=date('Y');
						*/

						//break 2;
					}
					break 2;
				}
			}
			return false;
		} while(0);

		if(preg_match('|(.*?)(\d{1,2})[:h](\d{2})[:]?(\d{2})?\s?([PA]M)?|is',$data, $results))
		{
			$saida[4]=$results[2];
			$saida[5]=$results[3];
			$saida[6]=$results[4];
			if(trim(strtoupper($results[5]))=='PM') $saida[4]+=12;
		}

		return $saida;
	}

function format_data_str($data, $formato, $locale='pt_BR')
{
		$data=trim($data);
		setlocale(LC_ALL, $locale);
		$timestamp=parse_data($data, $locale);
		if($timestamp>0)
		{
			if (preg_match('|%|',$formato))
				return strftime($formato,$timestamp);
			else
		 		return date($formato,$timestamp);
		}
		else
			return false;
}


function format_data($timestamp, $formato)
	{
		global $nomedomes;
		global $nomediasemana;
		nome_do_mes(0);

		switch ($formato)
		{
			case '?':
				// Faz todas as intera��es abaixo com output

			case 1:
				//         01/02/2001 - 12:00
				$return=date('d/m/Y - H:i', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 2:
				//         01 de fevereiro de 2001
				$return= date('d', $timestamp)." de ".nome_do_mes(date('m', $timestamp))." de ".date('Y', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 3:
				//         01 de fevereiro de 2001 - 12:00
				$return= date('d', $timestamp)." de ".nome_do_mes(date('m', $timestamp))." de ".date('Y', $timestamp)." - ".date('H:i', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 4:
				//         01 de fevereiro de 2001 �s 12h00
				$return= date('d', $timestamp)." de ".$nomedomes[date('m', $timestamp)]." de ".date('Y', $timestamp)." �s ".date('H', $timestamp).'h'.date('i', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 5:
				//        01/02/2001
				$return= date('d/m/Y', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 6:
				//         Formato bom para o mysql
				//			2002-12-31 14:23:45
				$return= date('Y-m-d H:i:s', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 7:
				//        Domingo, 01 de fevereiro de 2001
				$return= $nomediasemana[date("w", $timestamp)].", ".date('d', $timestamp)." de ".$nomedomes[date('m', $timestamp)]." de ".date('Y', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 8:
				//        Retorna Timestamp
				$return=$timestamp;
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 9:
				//     dia da semana:   Seg
				$return= substr($nomediasemana[date("w",$timestamp)],0,3);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 10:
				//     dia do m�s
				$return= date('d', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 11:
				//     dia da semana:   Segunda
				$return= $nomediasemana[date("w", $timestamp)];
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 12:
				//     12h00
				$return=date('H', $timestamp).'h'.date('i', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 13:
				//     2001-02-31
				$return= date('Y-m-d', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 14:
				//     Domingo, 21
				$return= $nomediasemana[date("w",$timestamp)].', '.date('d', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 15:
				// 20010231125959
				$return= date('YmdHis', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 16:
				// 20010231
				$return= date('Ymd', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 17:
				// N�mero de dias no m�s
				$return=ultimo_dia_mes(date('m', $timestamp), date('Y', $timestamp));
	//			$return=daysInMonth($timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 18:
				//     Nome do m�s
				$return= $nomedomes[date('m', $timestamp)];
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 19:
				//	Dezembro de 2002
				$return= $nomedomes[date('m', $timestamp)]." de ".date('Y', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 20:
				//	Dia da semana do primeiro dia do m�s
				$return= date('w', $timestamp - ((date('d', $timestamp)-1) * 86400));
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 21:
				//	Dia da semana do �ltimo dia do m�s
				$return= date('w', mktime(0,0,0,date('m', $timestamp)+1,0,date('Y', $timestamp)));
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 22:
				//	Se a data for dia de semana retorna 1 se for final de semana retorna 0
				if((date("w", $timestamp)==0)||(date("w", $timestamp)==6))
					$return=0;
					else $return=1;
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 23:
				//	GMT para meta tag expires
				// Exemplo: '<meta HTTP-EQUIV="Expires" CONTENT="Wed, 26 Mar 2003 23:59:59 GMT">'
				$return=substr(date('r', $timestamp), 0, 25).' GMT';
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 24:
				//         Set.1
				$return= ucfirst(substr(nome_do_mes(date('m', $timestamp)),0,3)).'.'.date('d', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 25:
				//         01/02/2001 - 12:00
				$return=date('d/m/Y - H:i:s', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 26:
				//         DD/MM/YYYY HH:MM:SS
				$return= date('d/m/Y H:i:s', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 27:
				//         YYYYMMDDHHMM00 - Usado no Sistema
				$return= date('YmdHis', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 28:
				//  YYYY/MM/DD - Formato para Paths indexados por data
				$return= date ('Y/m/d', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;
			case 29:
				//  2001
				$return= date('Y', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;

			case 30:
				//     12:00
				$return=date('H', $timestamp).':'.date('i', $timestamp);
				if($formato=='?') { $formato_out++; echo $formato_out.': '.$return.'<BR>'; } else break;
		}


				if($formato=='?')
					exit;
					else if($return) return $return;
	}


function getmicrotime()
	{
	    list($sec, $usec) = explode(" ", microtime());
	    return ($sec + $usec);
    	}


function formatar_duracao($segundos, $detalhe=1)
	{

		$def=array(31536000, 2628000, 604800, 86400, 3600, 60, 1);
		$formatos[0]=array(' ano', ' mes', ' semana', ' dia', ' hora', ' minuto', ' segundo');
		$formatos[1]=array(' ano', ' mes', ' semana', ' dia', ' hora', ' min', ' seg');
		$formatos[2]=array(' ano', ' mes', ' semana', 'D', 'H', '"', "'");

		$plural='s';
		if($detalhe==2) $plural='';
		$segundos_ini=$segundos;

		foreach($def as $k=>$n)
		{
			$var=floor($segundos / $n);
			if($var<10)
				$vartxt='0'.$var;
				else $vartxt=$var;

			if(($var>0)||($segundos_ini>=$n))
			{
				$txt=$formatos[$detalhe][$k];

				if($var>1)
				{
					if($txt=='mes')
						$return.=$vartxt.'meses';
						else $return.=$vartxt.$txt.$plural.' ';
				} else $return.=$vartxt.$txt.' ';

				$segundos-=($var * $n);
			}
		}


		return $return;

	}


function dataset_ok($dataset, $data=false) {
	//Diz se uma $data � contemplada por um $dataset (true) ou Não (false)
	if ($data===false) $data=time();
	$data=trim($data);
	$timestamp=ConverteData($data, 8);
	$cron_items=array(0=>'i', 1=>'H', 2=>'d', 3=>'m', 4=>'w' );

	$ds = preg_split('/\s+/', trim($dataset));
	if (count($ds)==5) {
		$found=array();
		reset($ds);
		while (list($k,$v) = each($ds)) {
			if ($debug) echo  "passando o while de $k e $v ";	flush();
			$found[$k]=false;
			$ranges = explode (',' , trim($v));
			$procurado=intval(date($cron_items[$k], $timestamp));

			while ((list($kr,$r) = each ($ranges)) && $found[$k]==false) {
				if ($r=='*') {
					// asterisco
					if ($debug) echo  '... asterisco';
					$found[$k]=true;
				} elseif (preg_match('/(\d+)\-(\d+)/' , $r , $tmp)) {
					// range
					if ($debug) echo  "... range ";
					if ((intval($tmp[1])<=$procurado) && (intval($tmp[2])>=$procurado)) {
						$found[$k]=true;
					}
				} elseif (preg_match('/^(\d+)$/' , $r , $tmp)) {
					// numero
					if ($debug) echo  '... numero';
					if (intval($tmp[1])==$procurado) $found[$k]=true;

				} elseif (preg_match('#^(\d+)/(\d+)$#' , $r , $tmp)) {
					//itera��o
					if ($debug) echo  '... itera��o';
					$tmpsoma=intval($tmp[1]);
					if (intval($tmp[2])==0) {
						if ($tmpsoma==$procurado) $found[$k]=true;
					} else {
						while ($tmpsoma<=$procurado) {
							if ($debug) echo  "<br>\$tmpsoma=$tmpsoma | \$tmp[2]=" . $tmp[2] . '';
							if ($tmpsoma==$procurado) $found[$k]=true;
							$tmpsoma=$tmpsoma+((intval($tmp[2])));
						}
					}
				}
				if ($debug) echo  " (procurando $r em ".$procurado.") ";
				if ($found[$k]==true) {if ($debug) echo  ' <font color=blue><strong>...ok...</strong></font> ';} else {if ($debug) echo  ' <font color=red>...nops...</font>';}
			}

			if ($debug) echo  '<br>';
		}

		// o dia da semana e o dia do m�s t�m uma rela��o de "OR" entre si.
		// ou seja, se um for achado, Não faz diferen�a se o outro Não o foi.
		// (desde que ambos tenham sido especificados)
		// o if abaixo simula esse comportamento:
		if ($ds[2]!=='*' && $ds[4]!=='*') {
			if ($found[2]==true) $found[4]=true;
			if ($found[4]==true) $found[2]=true;
		}
		// agora, o veredito final:
		$ok=true;
		foreach ($found as $v) {
			if ($v!==true) $ok=false;
		}

		return $ok;
	} else {
		echo  '<p>ERRO: dataset invalido: tem ' . count($ds) . ' �tens (esperados: 5)</p>';
		return false;
	}
}

function mostra_diasdasemana($dataset)
{
	$split=preg_split('/\s+/', $dataset);
	$dias=array('Dom','Seg','Ter','Qua','Qui','Sex','S�b');
	if(count($split)==5)
	{
		if($split[4]=='*')
		{
			$validos=array(0,1,2,3,4,5,6);
		}
			else
		{
			foreach(explode(',', $split[4]) as $val)
			{
				$validos[$val]=true;
			}
		}

		if(count($validos)==7)
		{
			$return.='Di�rio';
		}
			else
		{
			foreach($dias as $key=>$val)
			{
				if($validos[$key])
					$return.=' <font color="red"><U>'.$val.'</U></font>';
					else $return.=' '.$val;
			}
		}
	}
	return $return;
}

?>
