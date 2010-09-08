<?php

final class formElement {
	protected $type;
	protected $value;
	protected $inValue;
	protected $label;
	protected $id;
	protected $cls;
	protected $labelId;
	protected $labelCls;
	protected $name;
	protected $onActv;
	protected $disabl;
	protected $ronly;

	#There should be array of arrays of fields 'value' and 'label'
	#For selects and so.
	protected $options;

	public function __construct($array=false) {
		$this->type='text';
		$this->value='';
		$this->inValue='1';
		$this->ronly=false;
		$this->disabl=false;
		$this->label='';
		$this->id='';
		$this->cls='';
		$this->labelId='';
		$this->labelCls='';
		$this->name='';
		$this->onActv='';
		$this->options=array();
		if ($array) $this->multiset($array);
	}

	//Brakuje bardzo wielu elementów w tej klasie.
	//Potrzebna jest funkcja do generowania tabeli $options.
	//Potrzebna jest funkcja dla plików.
	//Potrzebna jakaś obsługa tablic

	public function set($key,$value='') {
		$t =& $this;
		if     ($key=='disabl')    $t->disabl   = (bool)$value;
		elseif ($key=='ronly')     $t->ronly    = (bool)$value;
		elseif ($key=='id')        $t->id       = htmlspecialchars($value);
		elseif ($key=='class')     $t->cls      = htmlspecialchars($value);
		elseif ($key=='labelId')   $t->labelId  = htmlspecialchars($value);
		elseif ($key=='labelCls')  $t->labelCls = htmlspecialchars($value);
		elseif ($key=='name')      $t->name     = htmlspecialchars($value);
		elseif ($key=='label')     $t->label    = htmlspecialchars($value);
		elseif ($key=='inValue')   $t->inValue  = htmlspecialchars($value);
		elseif ($key=='type')      $t->type     = htmlspecialchars($value);
		elseif ($key=='onActv')    $t->onActv   = htmlspecialchars($value);
		elseif ($key=='rawValue')  $t->value    = $value;
		elseif ($key=='value')     $t->setValue($value);
		elseif ($key=='autoValue') $t->autoValue($value);
		elseif ($key=='multiVals') $t->putValList($value);
		else return true;
		return false;
	}

	public function get($key) {
		$t =& $this;
		if     ($key=='disabl')   return (bool)$t->disabl;
		elseif ($key=='ronly')    return (bool)$t->ronly;
		elseif ($key=='id')       return (htmlspecialchars_decode($t->id));
		elseif ($key=='class')    return (htmlspecialchars_decode($t->cls));
		elseif ($key=='labelId')  return (htmlspecialchars_decode($t->labelId));
		elseif ($key=='labelCls') return (htmlspecialchars_decode($t->labelCls));
		elseif ($key=='name')     return (htmlspecialchars_decode($t->name));
		elseif ($key=='label')    return (htmlspecialchars_decode($t->label));
		elseif ($key=='inValue')  return (htmlspecialchars_decode($t->inValue));
		elseif ($key=='type')     return (htmlspecialchars_decode($t->type));
		elseif ($key=='onActv')   return (htmlspecialchars_decode($t->onActv));
		elseif ($key=='value')    return (htmlspecialchars_decode($t->value));
		else return true;
		return false;
	}

	protected function autoValue($value) {
		if ($this->type=='password') return;
		$tmp = $this->cmpAutoValue();
		if ($tmp !== false) $value = $tmp;
		$this->setValue($value);
	}

	protected function setValue($value) {
		if (is_array($value)):
			$old = $value;
			$value=array();
			foreach ($old as $one):
				$value[]=htmlspecialchars($one);
			endforeach;
		else:
			$value=htmlspecialchars($value);
		endif;
		$this->value=$value;
	}

	public function cmpAutoValue() {
		if (isset($_POST[$this->name])):
			return $_POST[$this->name];
		elseif (isset($_GET[$this->name])):
			return $_GET[$this->name];
		else:
			return false;
		endif;
	}

	public function wasSet() {
		if ($this->type == 'file'):
			if (isset($_FILES[$this->name])):
				$tmp=$_FILES[$this->name]['tmp_name'];
				return(is_uploaded_file($tmp));
			else:
				return false;
			endif;
		endif;
		if (isset($_POST[$this->name])) $val = $_POST[$this->name];
		elseif (isset($_GET[$this->name])) $val = $_GET[$this->name];
		else return false;
		if ($val === '') return false;
		return true;
	}

	public function multiset($a) {
		if (!is_array($a)) return true;
		$tmp = false;
		foreach ($a as $k => $v):
			$tmp = $tmp or $this->set($k,$v);
		endforeach;
		return $tmp;
	}

	#For selects and so.
	public function putVal() {
		$a=func_get_args();
		if (!isset($a[0])) return;
		if (is_array($a[0])):
			if (isset($a[0]['label']) and isset($a[0]['value'])):
				$value = $a[0]['value'];
				$label = $a[0]['label'];
				$reset = (isset($a[1]) and $a[1]);
			else:
				return;
			endif;
		elseif (isset($a[1])):
			$label = $a[0];
			$value = $a[1];
			$reset = (isset($a[2]) and $a[2]);
		else:
			return;
		endif;
		if ($reset) $this->options=array();
		$this->options[]=array('value'=>$value,'label'=>$label);
	}

	public function putValList($arr) {
		$first=true;
		foreach($arr as $one):
			if (!is_array($one)) $this->putVal($one,$one,$first);
			else $this->putVal($one,$first);
			$first=false;
		endforeach;
	}

	public function isValIn($val) {
		if (!is_array($this->value)):
			if ($this->value == $val) return true;
			else return false;
		endif;
		foreach ($this->value as $opt):
			if ($opt == $val) return true;
		endforeach;
		return false;
	}

	public function doActv($f) {
		if (!($f instanceof formElement)):
			trigger_error('F is wrong type',E_USER_ERROR);
		endif;
		$tmp1 = $f->get('inValue');
		$tmp2 = $this->get('onActv');
		return ($tmp1 == $tmp2);	
	}

	public function ech($pat='l_e') {
		echo($this->gen($pat));
	}

	public function gen($pat='l_e') {
		$gen = "";
		if ($pat=='le'):
			$gen.= $this->putLabel('bmc');
			$gen.= $this->putElement();
			$gen.= "</label>\n";
		elseif ($pat=='l_e'):
			$gen.= $this->putLabel('bmc');
			$gen.= ' ';
			$gen.= $this->putElement();
			$gen.= "</label>\n";
		elseif ($pat=='el'):
			$gen.= $this->putLabel('b');
			$gen.= $this->putElement(false);
			$gen.= $this->putLabel('m');
			$gen.= "</label>\n";
		elseif ($pat=='e_l'): 
			$gen.= $this->putLabel('b');
			$gen.= $this->putElement(false);
			$gen.= ' ';
			$gen.= $this->putLabel('m');
			$gen.= "</label>\n";
		elseif ($pat=='lbre'):
			$gen.= $this->putLabel('bmc');
			$gen.= "<br />\n";
			$gen.= $this->putElement();
			$gen.= "</label>\n";
		elseif ($pat=='ebrl'):
			$gen.= $this->putLabel('b');
			$gen.= $this->putElement();
			$gen.= "<br />\n";
			$gen.= $this->putLabel('me');
                elseif ($pat=='le_row'):
                        $gen.= "<tr>\n";
                        $gen.= "<td>";
			$gen.= $this->putLabel('mbe');
                        $gen.= "</td>";
                        $gen.= "<td>";
			$gen.= $this->putElement();
                        $gen.= "</td>";
			$gen.= "</tr>\n";
                elseif ($pat=='le_throw'):
                        $gen.= "<tr>\n";
                        $gen.= "<th>";
			$gen.= $this->putLabel('mbe');
                        $gen.= "</th>";
                        $gen.= "<td>";
			$gen.= $this->putElement();
                        $gen.= "</td>";
			$gen.= "</tr>\n";
		elseif ($pat=='e'):
			$gen.= $this->putElement();
		elseif ($pat=='lc'):
			$gen.= $this->putLabel('bmce');
		elseif ($pat=='l'):
			$gen.= $this->putLabel('bme');
		endif;
		return $gen;
	}

	protected function putLabel($ptr) {
		#Begin Middle Colon End
		$ptr = array_flip(str_split($ptr));
		#No colons for hiddent types
		if ($this->type == 'hidden') unset($ptr['c']);
		$gen = "";
                if (isset($ptr['b'])):
                        $gen.= "<label";
                        if ($this->labelId) $gen.= ' id="'.$this->labelId.'"';
                        if ($this->labelCls) $gen.= ' class="'.$this->labelCls.'"';
                        if ($this->id) $gen.= ' for="'.$this->id.'"';
                        $gen.= ">\n";
                endif;
                if (isset($ptr['m']) and $this->label):
			$gen.= $this->label;
			if (isset($ptr['c'])) $gen.= ':';
			$gen.= "\n";
		endif;
		if (isset($ptr['e'])) $gen.= "</label>";
		return $gen;
	}

	public function canGenChilds() {
		switch ($this->type):
		case 'radiorow':
		case 'radiorows':
                case 'checkboxrow':
                case 'checkboxrows':
			return true;
			break;
		default:
			return false;
		endswitch;
	}

	public function genChildren() {
		switch ($this->type):
		case 'radios':
                case 'checkboxes':
			if ($this->type == 'checkboxes') $type = 'checkbox';
			else $type = 'radio';
			$isradio = true;
			if (!$isradio) $isbox = true;	
			$a = array();
			$i = 1;
			foreach ($this->options as $opt):
				$a[] = $this->genOneChild($opt,$i,$type);
				$i++;
			endforeach;
			break;
		default:
			trigger_error("Not working for this type.",E_USER_ERROR);
		endswitch;
		return $a;
	}

	protected function genOneChild($opt,$i,$type) {
		$x = new formElement();
		$x->set('type',$type);
		if ($this->id) $x->set('id',$this->id."_child_".$i);
		if ($this->cls) $x->set('class',$this->class);
		if ($this->labelId) $x->set('labelId',$this->labelId."_child_".$i);
		if ($this->labelCls) $x->set('labelCls',$this->labelCls);
		if ($this->ronly) $x->set('ronly',$this->ronly);
		if ($this->disabl) $x->set('disabl',$this->disabl);
		if ($type=='checkbox' and $this->name) $x->set('name',$this->name.'[]');
		elseif ($this->name) $x->set('name',$this->name);
		$x->set('label',$opt['label']);
		$x->set('inValue',$opt['value']);
		if ($type=='checkbox' and $this->isValIn($opt['value'])):
			$x->set('value',$opt['value']);
		elseif ($type!='checkbox'):
			$x->set('value',$this->value);
		else:
		endif;
		return $x;
	}

	protected function putElement() {
		$gen = '';
                switch ($this->type):
		case 'radios':
                case 'checkboxes':
			trigger_error("Not working for this type.",E_USER_ERROR);
			break;
		case 'textarea':
			$gen.= '<textarea';
			$gen.= $this->putElementStd();
			$gen.= '>';
			$gen.= $this->value;
                        $gen.= "</textarea>\n";
                        break;
		case 'wysiwyg':
			//Elementy wysiwyg oznaczamy przez specyficzną klasę.
			//Na czas generowania tagu trzeba więc ją nadpisać.
			$tmp=$this->cls;
			if ($this->cls) $this->cls = $this->cls." wysiwyg";
			else $this->cls = "wysiwyg";
			$gen.= '<textarea';
			$gen.= $this->putElementStd();
			$gen.= '>';
			$gen.= $this->value;
			$gen.= "</textarea>\n";
			//Przywrócenie zwykłej klasy.
                        $this->cls=$tmp;
                        break;
		case 'select':
			$gen.= '<select';
			$gen.= $this->putElementStd();
			$gen.= ">\n";
			foreach ($this->options as $opt):
				if ($opt['value']==$this->value) $slt=' selected="selected"';
				else $slt='';
                                $gen.= "<option value=\"{$opt['value']}\"$slt>{$opt['label']}</option>\n";
			endforeach;
                        $gen.= "</select>\n";
                        break;
		case 'radio':
			$gen.= "<input type='radio'";
			$gen.= $this->putElementStd();
			if ($this->inValue==$this->value) $slt=' checked="checked"';
			else $slt='';
			$gen.= ' value="'.$this->inValue.'"'.$slt." />\n";
                        break;
                case 'checkbox':
                        $gen.= '<input type="checkbox" value="'.$this->inValue.'"';
                        $gen.= $this->putElementStd();
			if ($this->inValue==$this->value) $gen.=' checked="checked"';
                        $gen.= ' />';
                        break;
		case 'password':
			$gen.= '<input type="password"';
			$gen.= $this->putElementStd();
                        $gen.= ' />';
                        break;
		case 'file':
			$gen.= '<input type="file"';
			$gen.= $this->putElementStd();
                        $gen.= ' />';
                        break;
		default:
			//Nie wiem, czy to dobry pomysł. Może wszystkim nieznanym wymusić tekst.
			//Względnie ich nie wyświetlać?
			if ($this->type) $tmp=$this->type;
			else $tmp = 'text';
			$gen.= "<input type=\"$tmp\"";
			$gen.= $this->putElementStd();
			if ($this->value) $gen.= ' value="'.$this->value.'"';
			$gen.= ' />';
		endswitch;
		return $gen;
	}

	private function putElementStd() {
		$gen = '';
		if ($this->name) $gen.= " name=\"{$this->name}\"";
		if ($this->id)   $gen.= " id=\"{$this->id}\"";
		if ($this->cls)  $gen.= " class=\"{$this->cls}\"";
		if ($this->disabl) $gen.= " disabled=\"disabled\"";
		if ($this->ronly)  $gen.= " readonly=\"readonly\"";
		return $gen;
	}

}

?>
