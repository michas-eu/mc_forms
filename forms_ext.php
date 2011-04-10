<?php

class form_element_ext extends form_element_single {
	#Mainly for selects.

	protected $options;

	public function __construct($array=false) {
		$this->options = array();
		parent::__construct($array);
	}

	public function set($key,$value) {
		if ($key=='optval') $this->putValList($value);
		else return parent::set($key,$value);
	}

	public function get($key) {
		$t =& $this;
		if ($key =='optval') return $t->options;
		else return parent::get($key);
	}

	protected function putValList($arr) {
		$this->options = array();
		$ret = false;
		foreach($arr as $one):
			if (!is_array($one)):
				$lab = $one;
				$val = $one;
			elseif (isset($one['label']) and isset($one['value'])):
				$lab = $one['label'];
				$val = $one['value'];
			elseif (isset($one[0]) and isset($one[1])):
				$lab = $one[0];
				$val = $one[1];
			else:
				$ret = true;
				continue;
			endif; 
			$this->options[]=array('label'=>$lab,'value'=>$val);
		endforeach;
		return $ret;
	}

	protected function putElement() {
		if ($this->type != 'select') return parent::putElement();
		$gen = ''; 
		$gen.= '<select';
		$gen.= $this->putElementStd();
		$gen.= ">\n";
		foreach ($this->options as $opt):
			if ($opt['value']==$this->value) $slt=" selected='selected'";
			else $slt='';
			$tmp = htmlspecialchars($opt['value']);
			$gen.= "<option value='$tmp'$slt>\n";
			$tmp = htmlspecialchars($opt['label']);
			$gen.= $tmp;
			$gen.="\n</option>\n";
		endforeach;
		$gen.= "</select>\n";
		return $gen;
	}
}

?>
