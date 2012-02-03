<?php

class form_element_single {
	protected $type;
	protected $value;
	protected $inValue;
	protected $label;
	protected $id;
	protected $cls;
	protected $labelId;
	protected $labelCls;
	protected $name;
	protected $disabl;
	protected $ronly;

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
		$this->options=array();
		if ($array) $this->multiset($array);
	}

	public function set($key,$value) {
		$t =& $this;
		if     ($key=='disabl')   $t->disabl   = (bool)$value;
		elseif ($key=='ronly')    $t->ronly    = (bool)$value;
		elseif ($key=='id')       $t->id       = $value;
		elseif ($key=='class')    $t->cls      = $value;
		elseif ($key=='labelId')  $t->labelId  = $value;
		elseif ($key=='labelCls') $t->labelCls = $value;
		elseif ($key=='name')     $t->name     = $value;
		elseif ($key=='label')    $t->label    = $value;
		elseif ($key=='inValue')  $t->inValue  = $value;
		elseif ($key=='type')     $t->type     = $value;
		elseif ($key=='value')    $t->value    = $value;
		else return true;
		return false;
	}

	public function multiset($a) {
		if (!is_array($a)) return true;
		$tmp = false;
		foreach ($a as $k => $v):
			$tmp = $tmp || $this->set($k,$v);
		endforeach;
		return $tmp;
	}

	public function get($key) {
		$t =& $this;
		if     ($key=='disabl')   return $t->disabl;
		elseif ($key=='ronly')    return $t->ronly;
		elseif ($key=='id')       return $t->id;
		elseif ($key=='class')    return $t->cls;
		elseif ($key=='labelId')  return $t->labelId;
		elseif ($key=='labelCls') return $t->labelCls;
		elseif ($key=='name')     return $t->name;
		elseif ($key=='label')    return $t->label;
		elseif ($key=='inValue')  return $t->inValue;
		elseif ($key=='type')     return $t->type;
		elseif ($key=='value')    return $t->value;
		return true;
	}

	public function autoValue($a=array()) {
		if ($this->type=='password') return;
		$tmp = $this->cmpAutoValue($a);
		if ($tmp === false) return;
		$this->value = $tmp;
	}

	public function cmpAutoValue($a=array()) {
		if ($a):
			if (isset($a[$this->name])):
				return $a[$this->name];
			endif;
			return false;
		endif;
		if (isset($_POST[$this->name])):
			return $_POST[$this->name];
		elseif (isset($_GET[$this->name])):
			return $_GET[$this->name];
		else:
			return false;
		endif;
	}

	public function getFromArray($a) {
		if (isset($a[$this->name])):
			$this->value = $a[$this->name];
		endif;
	}

	public function addToArray(&$arr,$empty_too=false) {
		$tmp = $this->cmpAutoValue();
		if ($tmp !== false) $arr[$this->name] = $tmp;
		elseif ($empty_too) $arr[$this->name] = '';
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

	public function __toString() {
		return $this->gen();
	}
	
	public function gen($pat='auto') {
		$gen = "";
		if ($pat == 'auto'):
			switch ($this->type):
			case 'radio':
			case 'checkbox':
				$pat = 'e_l';
				break;
			case 'textarea':
			case 'wysiwyg':
				$pat = 'lbre';
				break;
			default:
				$pat = 'l_e';
			endswitch;
			return $this->gen($pat);
		elseif ($x = strpos($pat,'_li')):
			$gen.= "<li>";
			$gen.= $this->gen(substr($pat,0,$x));
			$gen.= "</li>";
		elseif ($x = strpos($pat,'_br')):
			$gen.= $this->gen(substr($pat,0,$x));
			$gen.= "<br>";
		elseif ($pat=='le'):
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
			$gen.= $this->putElement();
			$gen.= $this->putLabel('m');
			$gen.= "</label>\n";
		elseif ($pat=='e_l'): 
			$gen.= $this->putLabel('b');
			$gen.= $this->putElement();
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
			$gen.= $this->putLabel('bme');
                        $gen.= "</td>";
                        $gen.= "<td>";
			$gen.= $this->putElement();
                        $gen.= "</td>";
			$gen.= "</tr>\n";
                elseif ($pat=='le_throw'):
                        $gen.= "<tr>\n";
                        $gen.= "<th>";
			$gen.= $this->putLabel('bme');
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
		if ($this->type == 'hidden'):
			unset($ptr['c']);
			unset($ptr['m']);
		endif;
		$gen = "";
                if (isset($ptr['b'])):
                        $gen.= "<label";
                        if ($this->labelId):
				$tmp = htmlspecialchars($this->labelId);
				$gen.= ' id="'.$tmp.'"';
			endif;
                        if ($this->labelCls):
				$tmp = htmlspecialchars($this->labelCls);
				$gen.= ' class="'.$tmp.'"';
			endif;
                        if ($this->id):
				$tmp = htmlspecialchars($this->id);
				$gen.= ' for="'.$tmp.'"';
			endif;
                        $gen.= ">";
                endif;
                if (isset($ptr['m']) and $this->label):
			$gen.= $this->label;
			if (isset($ptr['c'])) $gen.= ':';
			$gen.= "\n";
		endif;
		if (isset($ptr['e'])) $gen.= "</label>\n";
		return $gen;
	}

	protected function putElement() {
		$gen = '';
                switch ($this->type):
		case 'textarea':
			$gen.= '<textarea';
			$gen.= $this->putElementStd();
			$gen.= '>';
			if ($this->value):
				$tmp = htmlspecialchars($this->value);
				$gen.= $tmp;
			endif;
                        $gen.= "</textarea>\n";
                        break;
		case 'wysiwyg':
			$tmp=$this->cls;
			if ($this->cls) $this->cls = $this->cls." wysiwyg";
			else $this->cls = "wysiwyg";
			$gen.= '<textarea';
			$gen.= $this->putElementStd();
			$gen.= '>';
                        $this->cls=$tmp;
			if ($this->value):
				$tmp = htmlspecialchars($this->value);
				$gen.= $value;
			endif;
			$gen.= "</textarea>\n";
			#Revert to standard class
                        break;
		case 'radio':
			$gen.= "<input type='radio'";
			$gen.= $this->putElementStd();
			if ($this->inValue==$this->value) $slt=' checked="checked"';
			else $slt='';
			$tmp = htmlspecialchars($this->inValue);
			$gen.= " value=\"$tmp\"$slt />\n";
                        break;
                case 'checkbox':
			$tmp = htmlspecialchars($this->inValue);
                        $gen.= '<input type="checkbox" value="'.$tmp.'"';
                        $gen.= $this->putElementStd();
			if ($this->inValue==$this->value) $gen.=' checked="checked"';
                        $gen.= " />\n";
                        break;
		case '':
			$gen.= "<input";
			$gen.= $this->putElementStd();
			if ($this->value):
				$tmp = htmlspecialchars($this->value);
				$gen.= " value='$tmp'";
			endif;
			$gen.= " />\n";
			break;
		default:
			$tmp = htmlspecialchars($this->type);
			$gen.= "<input type='$tmp'";
			$gen.= $this->putElementStd();
			if ($this->value):
				$tmp = htmlspecialchars($this->value);
				$gen.= " value='$tmp'";
			endif;
			$gen.= " />\n";
		endswitch;
		return $gen;
	}

	protected function putElementStd() {
		$gen = '';
		if ($this->name):
			$tmp = htmlspecialchars($this->name);
			$gen.= " name='$tmp'";
		endif;
		if ($this->id):
			$tmp = htmlspecialchars($this->id);
			$gen.= " id='$tmp'";
		endif;
		if ($this->cls):
			$tmp = htmlspecialchars($this->cls);
			$gen.= " class='$tmp'";
		endif;
		if ($this->disabl) $gen.= " disabled='disabled'";
		if ($this->ronly)  $gen.= " readonly='readonly'";
		return $gen;
	}

}

?>
