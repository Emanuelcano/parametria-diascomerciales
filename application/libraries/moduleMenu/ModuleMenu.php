<?php

require_once 'application/libraries/moduleMenu/Autoloader.php';

class ModuleMenu
{
	private $CI;
	private $moduleId;
	private $module;
	private $currentRelativePath;
	
	public function __construct($moduleId = 0, $currentRelativePath = 0)
	{
		$this->CI =& get_instance();
		$this->moduleId = $moduleId;
		$this->currentRelativePath = $currentRelativePath;
	}
	
	/**
	 * Renderiza el html del menu con los botones
	 * 
	 * @return string
	 */
	public function getMenuRender($onlyEnabled = true)
	{
		if ($this->moduleId == 0) {
			return '';
		}
		
		$this->module = new Module($this->moduleId, $this->currentRelativePath);
		$buttons = $this->module->getButtons($onlyEnabled);
		
		$html = '<div class="col-lg-12" id="cuerpoCreditosBuscar" style="display: block">';
		foreach ($buttons as $button) {
			$html .= $button;
		}
		$html .= '</div>';
		
		return $html;
	}
}
