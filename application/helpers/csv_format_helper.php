<?php


// RULES EXAMPLES

//$rules = [
//	'delimiter' => ';',
//	'columns' =>[
//		[
//			'name' => 'id',
//			'required' => true,
//			'type' => 'number'
//		],
//		[
//			'name' => 'address',
//			'required' => true,
//			'type' => 'string'
//		],
//		[
//			'name' => 'fecha',
//			'required' => true,
//			'type' => 'date',
//			'format' => 'd/m/Y'
//		],
//		[
//			'name' => 'mail',
//			'required' => true,
//			'type' => 'email',
//		],
//		[ 'required' => false ],
//	]
//];


/**
 * Checkea el formato de un CSV
 * 
 * @param $rules | reglas de como procesar el CSV. Mirar ejemplos arriba
 * @param $csvFile | $_FILES['file']['tmp_name'] o url al archivo
 * @param $skipHeader | si es falso salteara la primera linea del CSV 
 *
 * @return array
 * @throws Exception
 */
function checkCsvFormat($rules, $csvFile, $skipHeader = true) {
	$result = true;
	$error = '';
	
	$validTypeRules = ['number', 'string', 'date', 'email', 'any'];
	
	$fh = fopen($csvFile, 'r+');
	$line = 0;
	while( ($row = fgetcsv($fh, 8192,$rules['delimiter'])) !== FALSE ) {
		$line++;

		//check rules
		if (!$skipHeader and $line == 1) {
			foreach ($rules['columns'] as $k => $column) {
				if ($column['required']) {
					if (strtoupper($column['name']) != strtoupper($row[$k])) {
						$error = 'Columna ' . $column['name'] . ' requerida';
						$result = false;
						break;
					} 
				}
			}
		} else {
			if ($skipHeader and $line == 1) {
				continue;
			}
			
			foreach ($rules['columns'] as $k => $column) {
				if ($column['required']) {
					if (!isset($row[$k])) {
						throw new Exception('CSV RULES. Missing Column ' . $column['name']);
					} else {
						$value = $row[$k];
					}
					
					if(trim($value) == '') {
						$error = "Linea $line columna ".$column['name'].": debe contener un valor"  ;
						$result = false;
					}
					
					if (!in_array($column['type'], $validTypeRules)) {
						throw new Exception('CSV RULES. Invalid Type');
					}
					
					//Number check
					if ($column['type'] == 'number') {
						if (!is_numeric($value)) {
							$error = "Linea $line: $value no es un valor numerico valido"  ;
							$result = false;
						}
					}
					
					//string check
					if ($column['type'] == 'string') {
						if (!is_string($value)) {
							$error = "Linea $line: $value no es un string valido"  ;
							$result = false;
						}
					}
					
					//date check
					if ($column['type'] == 'date') {
						if (!isset($column['format'])) {
							throw new Exception('CSV RULES. Date without format');
						}
						$d = DateTime::createFromFormat($column['format'], $value);
						$resultDate = ( $d && $d->format($column['format']) == $value );
						if (!$resultDate) {
							$error = "Linea $line: Fecha " . $value . " no corresponde al formato " . $column['format'];
							$result = false;
						}
					}
					
					//email check
					if ($column['type'] == 'email') {
						if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
							$error = "Linea $line: Email " . $value . " no es un email valido ";
							$result = false;
						}
					}
					
					if  ($column['type'] == 'any') {
						//siempre valido
					}
					
				}
			}
		}
		
		if ($result == false) {
			break;
		}
	}
	
	$response = [
		'result' => $result,
		'error' => $error
	];
	
	return $response;
}
