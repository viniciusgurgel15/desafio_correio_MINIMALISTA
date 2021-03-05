<?php

/**
 * Param @obj in $_GET ou $_POST
 * Código adaptado de https://github.com/luannsr12
 */

if(isset($_GET) || isset($_POST)) {
	$request = isset($_GET) ? $_GET : $_POST;
	$obj = isset($request['obj']) ? $request['obj'] : false;

	if($obj) {
		$post = array('Objetos' => $obj);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www2.correios.com.br/sistemas/rastreamento/resultado_semcontent.cfm");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($post));
		$output = curl_exec($ch);
		curl_close($ch);

		$out = explode("table class=\"listEvent sro\">",$output);

		if(isset($out[1])) {
			$output = explode("<table class=\"listEvent sro\">",$output);
			$output = explode("</table>",$output[1]);
			$output = str_replace("</td>","",$output[0]);
			$output = str_replace("</tr>","",$output);
			$output = str_replace("<strong>","",$output);
			$output = str_replace("</strong>","",$output);
			$output = str_replace("<tbody>","",$output);
			$output = str_replace("</tbody>","",$output);
			$output = str_replace("<label style=\"text-transform:capitalize;\">","",$output);
			$output = str_replace("</label>","",$output);
			$output = str_replace("&nbsp;","",$output);
			$output = str_replace("<td class=\"sroDtEvent\" valign=\"top\">","",$output);
			$output = explode("<tr>",$output);


			$novo_array = array();
			foreach($output as $texto) {
				$info   = explode("<td class=\"sroLbEvent\">",$texto);
				$dados  = explode("<br />",$info[0]);

				$dia   = trim($dados[0]);
				$hora  = trim(@$dados[1]);
				$local = trim(@$dados[2]);

				$dados = explode("<br />",@$info[1]);
				$acao  = trim($dados[0]);

				$exAction   = explode($acao."<br />",@$info[1]);
				$acrionMsg  = strip_tags(trim(preg_replace('/\s\s+/', ' ',$exAction[0])));

				if("" != $dia) {
					$exploDate = explode('/',$dia);
					$dia1 = $exploDate[2].'-'.$exploDate[1].'-'.$exploDate[0];
					$dia2 = date('Y-m-d');
					$diferenca = strtotime($dia2) - strtotime($dia1);
					$dias = floor($diferenca / (60 * 60 * 24));
					$change = utf8_encode("há {$dias} dias");
					$novo_array[] = array(
						"date"=> $dia,
						"hour"=> $hora,
						"location"=> utf8_encode($local),
						"action"=> utf8_encode($acao),
						"message"=> utf8_encode($acrionMsg),
						"change"=> utf8_decode($change));
				}
			}
			
			echo json_encode(array("code"=> 1, "data"=> $novo_array));
		} 
		else {
			echo json_encode(array("code"=> 0, "msg"=> utf8_encode("Objeto nao encontrado!")));
		}
	} 
	else {
		echo json_encode(array("code"=> 0, "msg"=> utf8_encode("Parametro vazio!")));
	}
}