<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Site Helper
| -------------------------------------------------------------------------
| Desenvolvido por Bruno Almeida
|
*/

if ( ! function_exists('formata_preco'))
{
function formata_preco($valor)
{
    $negativo = false;
    $preco = "";
    $valor = intval(trim($valor));

if ($valor < 0) {
    $negativo = true;
    $valor = abs($valor);
}
    $valor = strrev($valor);
        while (strlen($valor) < 3) {
    $valor .= "0";
    }
for ($i = 0; $i < strlen($valor); $i++) {
    if ($i == 2) {
    $preco .= ",";
}
if (($i <> 2) AND (($i+1)%3 == 0)) {
    $preco .= ".";
}
    $preco .= substr($valor, $i , 1);
}
    $preco = strrev($preco);
    return ($negativo ? "-" : "") . $preco;
    }
}



function verificaatrazo($datainicio, $datafim){

                $timeZone = new DateTimeZone('UTC');
                $dataEntrada = data_br($datainicio);
                $dataSaida = data_br($datafim);
                $data1 = DateTime::createFromFormat ('d/m/Y', $dataEntrada, $timeZone);
                $data2 = DateTime::createFromFormat ('d/m/Y', $dataSaida, $timeZone);
                if ($data1 < $data2)  { return "Atrasado";   }
                if ($data1 == $data2) { return "No Prazo";  }
                if ($data1 > $data2)  { return "Em dia";   }
   
}





function url_base64_encode(&$str = "") {
    return strtr(
                    base64_encode($str), array(
                '+' => '.',
                '=' => '-',
                '/' => '~'
                    )
    );
}

function zeroise($number, $threshold) {
  return sprintf('%0'.$threshold.'s', $number);
}

function sh_date_interval($_date1,$_date2, $format = null){

    //Make sure $date1 is ealier
    $date1 = ($_date1 <= $_date2 ? $_date1 : $_date2);
    $date2 = ($_date1 <= $_date2 ? $_date2 : $_date1);

    //Calculate R values
    $R = ($_date1 <= $_date2 ? '+' : '-');
    $r = ($_date1 <= $_date2 ? '' : '-');

    //Calculate total days
    $total_days = round(abs($date1->format('U') - $date2->format('U'))/86400);

    //A leap year work around - consistent with DateInterval
    $leap_year = ( $date1->format('m-d') == '02-29' ? true : false);
    if( $leap_year ){
        $date1->modify('-1 day');
    }

    $periods = array( 'years'=>-1,'months'=>-1,'days'=>-1,'hours'=>-1);

    foreach ($periods as $period => &$i ){

        if($period == 'days' && $leap_year )
            $date1->modify('+1 day');

        while( $date1 <= $date2 ){
            $date1->modify('+1 '.$period);
            $i++;
        }

        //Reset date and record increments
        $date1->modify('-1 '.$period);
    }
    extract($periods);

    //Minutes, seconds
    $seconds = round(abs($date1->format('U') - $date2->format('U')));
    $minutes = floor($seconds/60);
    $seconds = $seconds - $minutes*60;

    $replace = array(
        '/%y/' => $years,
        '/%Y/' => zeroise($years,2),
        '/%m/' => $months,
        '/%M/' => zeroise($months,2),
        '/%d/' => $days,
        '/%D/' => zeroise($days,2),
        '/%a/' => zeroise($total_days,2),
        '/%h/' => $hours,
        '/%H/' => zeroise($hours,2),
        '/%i/' => $minutes,
        '/%I/' => zeroise($minutes,2),
        '/%s/' => $seconds,
        '/%S/' => zeroise($seconds,2),
        '/%r/' => $r,
        '/%R/' => $R
    );

    return preg_replace(array_keys($replace), array_values($replace), $format);
}






 
/**
 * Decodifica uma string base64 que foi codificado por url_base64_encode.
 *
 * @param string $str A seqüência de caracteres base64 para decodificar.
 * @return object
 */
function url_base64_decode(&$str = "") {
    return base64_decode(strtr(
                            $str, array(
                        '.' => '+',
                        '-' => '=',
                        '~' => '/'
                            )
                    ));
}
 

/**
* url_amigavel
* 
* Retira acentos, substitui espaço por - e
* deixa tudo minúsculo
* 
*
* @param	string
* @return	string
*/
function url_amigavel($variavel){
	$procurar 	= array('à','ã','â','é','ê','í','ó','ô','õ','ú','ü','ç',);
	$substituir = array('a','a','a','e','e','i','o','o','o','u','u','c',);
	$variavel = strtolower($variavel);
	$variavel	= str_replace($procurar, $substituir, $variavel);
	$variavel = htmlentities($variavel);
  $variavel = preg_replace("/&(.)(acute|cedil|circ|ring|tilde|uml);/", "$1", $variavel);
  $variavel = preg_replace("/([^a-z0-9]+)/", "-", html_entity_decode($variavel));
  return trim($variavel, "-");
}




 function mesextenso($nome_array, $valor_padrao, $separador='|'){
             
        $CI =& get_instance();
        $CI->load->helper('array');
         
        $valores = '';
        $valor_procura = array();
 
        /*
         * Arrays
         */
        $meses = array(1 => 'Janeiro',2 => 'Fevereiro',3 => 'Março',4 => 'Abril',5 => 'Maio',6 => 'Junho',7 => 'Julho',8 => 'Agosto',9 => 'Setembro',10 => 'Outubro',11 =>'Novembro',12 => 'Dezembro');
        $dias_semana = array(0=>'Domingo',1=>'Segunda-Feira',2=>'Terça-Feira',3=>'Quarta-Feira',4=>'Quinta-Feira',5=>'Sexta-Feira',6=>'Sabado');
 
         /*
          * Vamos fazer o explode dos dados que queremos procurar,
          * caso exista mais de um valor.
          */
         if (substr_count($valor_padrao, $separador)>0){
             $valor_procura = explode($separador, $valor_padrao);
         }
         else{
             $valor_procura[] = $valor_padrao;
         }
             
        /*
         * Como passamos o nome do array nos parâmetros,
         * precisamos que o PHP o reconheça como variável
         * e não como string.
         * Para isso colocamos o símbolo de $ a mais no
         * nome do array, para que a variável torne-se
         * dinâmica.
         */
        foreach($$nome_array as $var => $val){
 
            if (in_array($var, $valor_procura)){
                $valores .= ucwords(strtolower($val))."<br />";
            }               
        }
 
        return($valores);
    }


function removeAcentos($var) {
    $var = strtolower($var);
	$var = ereg_replace("[áàâãª]","a",$var);	
	$var = ereg_replace("[éèê]","e",$var);	
	$var = ereg_replace("[óòôõº]","o",$var);	
	$var = ereg_replace("[úùû]","u",$var);	
	$var = str_replace("ç","c",$var);
	
	return $var;
}

/**
* explode_t
* 
* Faz o explode em PHP, usa a função trim em cada índice do array e monta o array novamente
* 
*
* @param	string, string
* @return	array
*/
function explode_t($delimitador,$string){
  $explode = explode($delimitador, $string);
	$array = array();
	foreach ($explode as $item) {
		$array[] = trim($item);	
	}
	return $array;
}



/**
* data_br
* 
* Converte uma data no formato mysql para o formato brasileiro
* 
*
* @param	string
* @return	string
*/
function data_br($data_bd){
  return implode('/',array_reverse(explode('-',$data_bd)));
}



/**
* data_bd
* 
* Converte uma data no formato brasileiro para o formato mysql
* 
*
* @param	string
* @return	string
*/
function data_bd($data_br){
  return implode('-',array_reverse(explode('/',$data_br)));
}



/**
* limitar_texto
* 
* Remove todas as tags HTML e limita os caractéres do texto, adicionando ... se for maior que o limite
* 
*
* @param	string, int
* @return	string
*/
function limitar_texto($texto,$limit){
	$texto = strip_tags($texto);
	if(strlen($texto) > $limit){
		return substr($texto,0,$limit).'...';
	} else {		
	 return substr($texto,0,$limit);
	}
}

function diaatual(){
setlocale( LC_ALL, 'pt_BR', 'pt_BR.iso-8859-1', 'pt_BR.utf-8', 'portuguese' );
date_default_timezone_set( 'America/Sao_Paulo' );
return strftime( '%A, %d de %B de %Y', strtotime( date( 'Y-m-d' ) ) );
}

/**
* enviar_email
* 
* Faz o envio de e-mail
* 
*
* @param	string, string, string/array
* @return	boolean
*/
function enviar_email($destinatarios,$assunto,$corpo){

  $CI =& get_instance();

  $config = array(
    'protocol' => 'smtp', 
    'smtp_host' => 'mail.site.com.br',
    'smtp_port' => 465,
    'smtp_user' => 'noreply@site.com.br',
    'smtp_pass' => 'senha'
  );

  $CI->load->library('email', $config);
  $CI->email->set_newline("\r\n");

  $CI->email->from('noreply@urubici.com.br', 'Portal Urubici.com.br');
  $CI->email->subject($assunto);     
  $CI->email->message($corpo);

  $CI->email->to($destinatario);

  return $CI->email->send();
	
}


/* End of file site_helper.php */
/* Location: ./application/helpers/helper.php */