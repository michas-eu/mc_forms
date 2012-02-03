<?php

class form_element_multi extends form_element_ext {
	#Include support for sets of fields.

	protected $subelms;
	protected $multi;

	public function __construct($array=false) {
		$this->subelms = array();
		parent::__construct($array);
	}

	public function gen($pat='auto',$ext_tag='div') {
		$this->gen_subelms();
		if (!$this->multi) return parent::gen($pat);
		if ($pat == 'l_grand') return parent::gen('l');

		$gen = "";
		if ($ext_tag):
			$gen.= "<$ext_tag";
			$tmp = $this->putElementStd_light();
			if ($tmp) $gen.= " $tmp";
			$gen.= ">\n";
		endif;
		foreach ($this->subelms as $one):
			$gen.= $one($pat);
		endforeach;
		if ($ext_tag):
			$gen.= "</$ext_tag>\n";
		endif;
		return $gen;
	}

	protected function putValList($arr) {
		parent::putValList($arr);
		$this->subelms = array();
	}

	protected function gen_subelms() {
		if ($this->subelms) return;
		$this->multi = false;
		if ($this->type == 'multi_radio') $this->multi = true;
		if ($this->type == 'multi_checkbox') $this->multi = true;
		if (!$this->multi) return;

		$i = 0;
		if ($this->type == 'multi_radio') $type = 'radio';
		else $type = 'checkbox';
		foreach ($this->options as $o):
			$this->subelms[] = $this->gen_subelm($o,$i,$type);
			$i++;
		endforeach;
	}

	protected function gen_subelm($opt,$i,$type) {
		$x = new form_element_single();
		$x->set('type',$type);
		if ($this->id) $x->set('id',$this->id."_child_".$i);
		if ($this->cls) $x->set('class',$this->class.'_child');
		if ($this->labelId) $x->set('labelId',$this->labelId.'_child_'.$i);
		if ($this->labelCls) $x->set('labelCls',$this->labelCls.'_child');
		if ($this->ronly) $x->set('ronly',$this->ronly);
		if ($this->disabl) $x->set('disabl',$this->disabl);
		if ($this->name):
			if ($type=='checkbox') $x->set('name',$this->name.'[]');
			else $x->set('name',$this->name);
		endif;
		$x->set('label',$opt['label']);
		$x->set('inValue',$opt['value']);
		$x->set('value',$this->value);
		return $x;
	}

	protected function putElementStd_light() {
		$gen = '';
		if ($this->id):
			$tmp = htmlspecialchars($this->id);
			$gen.= " id='$tmp'";
		endif;
		if ($this->cls):
			$tmp = htmlspecialchars($this->cls);
			$gen.= " class='$tmp'";
		endif;
		return $gen;
	}
}
?>
