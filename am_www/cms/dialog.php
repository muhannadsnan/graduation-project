<?php

class DialogBox {
	public $TitleText="";
	public $TitleIcon="";
	public $BodyHTML="";
	public $PageLidOpacity="";
	public $Top="";
	
	public $id="";
	public $CssClass="msgbox";
	public $DialogStyle="";
	public $TitleStyle="";
	public $BodyStyle="";
	
	public $btns=array();
	private $DgResult="";
	protected $DialogShown=false;
	
	public $SaveURL="";
	public $onSave="";
	
	function __construct($id, $_TitleText="", $_TitleIcon="", $_BodyHTML="", $_Top="", $_PageLidOpacity="") {
		$this->TitleText=$_TitleText;
		$this->TitleIcon=$_TitleIcon;
		$this->BodyHTML=$_BodyHTML;
		$this->Top=$_Top;
		$this->PageLidOpacity=$_PageLidOpacity;
		$this->id=$id;
	}
	
	function AddButtons($BtnsArr) {
		
	}
	
	function Create() {
		if (!$this->DialogShown){
			?>
<script type="text/javascript">
$(document).ready (function (){
<?php
	$this->BodyHTML=addslashes(RemCrLf($this->BodyHTML));
	$this->onSave=addslashes((RemCrLf($this->onSave)));
	$_mydata="TitleText:'{$this->TitleText}',TitleIcon:'{$this->TitleIcon}',BodyHTML:'{$this->BodyHTML}',PageLidOpacity:'{$this->PageLidOpacity}',Top:'top:{$this->Top}',";
	$_mydata.="CssClass:'{$this->CssClass}',DialogStyle:'{$this->DialogStyle}',TitleStyle:'{$this->TitleStyle}',BodyStyle:'{$this->BodyStyle}',SaveURL:'{$this->SaveURL}',onSave:'{$this->onSave}'";	
?>
	var mydata={<?=$_mydata ?>};
	dlg_<?=$this->id ?>=new Dialog("<?=$this->id ?>", mydata);
	//dlg_<?=$this->id ?>.onSave=<?=$this->onSave ?>;
	dlg_<?=$this->id ?>.Display();
});
</script>
<?php
		}
		$this->DialogShown=true;
	}
	
	function Show($title="", $get_url="") {
		if ($title!=""){
		echo '$("#' . $this->id . ' .msgbox_title_text").text(' . $title . ');';
		}
		if ($get_url!="")
		{
		echo '$("#' . $this->id . ' .msgbox_body").load(' . $get_url . ', function () {dlg_'.$this->id.'.ShowDialog();});';
		}else {
			echo "dlg_{$this->id}.ShowDialog();";
		}
	}
	
	function OnResult($DialogResult, $ScriptURL="", $OnComplete="") {
		
	}
	
	function OnErr($OnErr) {
		
	}
	
	function AjaxErrMsg($msg) {
		
	}
	
}

class DialogResult
{
	const OK = "ok";
	const Cancel = "cancel";
	const Ignore = "ignore";
	const Yes = "yes";
	const No = "no";
	const Retry = "Retry";
}

?>
