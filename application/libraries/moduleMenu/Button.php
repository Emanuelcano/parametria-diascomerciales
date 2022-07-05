<?php

class Button
{
	private $data;
	
	public function __construct($data)
	{
		$this->data = $data;
	}
	
	/**
	 * Renderiza el html del boton
	 * 
	 * @param $active
	 *
	 * @return string
	 */
	public function render($active = false)
	{
		$rightBudget = $this->renderBudget('right');
		$leftBudget = $this->renderBudget('left');
		
		if ($this->data['estado'] == 0) {
			$disableBudget = $this->renderBudget('disable');
		} else {
			$disableBudget = '';
		}
		
		$activeClass = $active ? ' active' : '';
		
		$html = '<div class="box-body pull-left">
                    <a class="btn btn-app ' . $activeClass . '" data-id="'.$this->data['id'].'" onclick="location.href = \'' . $this->data['path'] . '\'">
                        ' . $rightBudget . '
                        ' . $leftBudget . '
                        ' . $disableBudget . '
                        <i class="fa ' . $this->data['icon_class'] . '" aria-hidden="true"></i> ' . $this->data['title'] . '
                    </a>
                </div>';
		
		return $html;
	}
	
	/**
	 * Renderiza los budget del boton
	 * 
	 * @param $type
	 *
	 * @return string
	 */
	private function renderBudget($type)
	{
		$html = '';
		
		if ($type == 'left') {
			if (!empty($this->data['left_badge_endpoint'])) {
				$endpointResult = $this->curlBadge('GET', $this->data['left_badge_endpoint']);
				$html = '<span class="badge bg-green" id="comentarios" style="left:0; right:auto;">' . $endpointResult . '</span>';
			}
		}
		
		if ($type == 'right') {
			if (!empty($this->data['right_badge_endpoint'])) {
				$endpointResult = $this->curlBadge('GET', $this->data['right_badge_endpoint']);
				$html = '<span class="badge bg-red" id="ciario">' . $endpointResult . '</span>';
			}
		}
		
		if ($type == 'disable') {
			$html = '<span class="badge bg-light" style="top: 50px;right: 3px;opacity: 0.7;">Deshabilitado</span>';
		}
		
		return $html;
	}
	
	/**
	 * CURL para los endpoing de los badges
	 * 
	 * @param $method
	 * @param $url
	 *
	 * @return bool|string|void
	 */
	private function curlBadge($method, $url)
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => base_url() . $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json"
			),
		));
		
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
			echo 'cURL Error #:' . $err;
			die;
		}
		
		return $response;
	}
}
