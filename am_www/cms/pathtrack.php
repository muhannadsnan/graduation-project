<script type="text/javascript" src="../cms/pathtrack.js"></script>
<?php
class PathTrack
{
	public $PathTabs;
	
	function AddTabs($TabA)
	{
		foreach (func_get_args() as $prm)
		{
			$this->PathTabs[] = $prm;
		}
	}
	
	function Display($DisplayIn="")
	{
		if ($DisplayIn=="") {echo "<div id='div_path'></div>"; $DisplayIn="div_path";}
?>
<script type="text/javascript">
$(document).ready (function (){
<?php
		foreach ($this->PathTabs as $mTab)
		{
			$mytabs[]='{'."pt_sep:'{$mTab->pt_sep}', pt_arrow:'{$mTab->pt_arrow}', pt_icon:'{$mTab->pt_icon}', pt_text:'{$mTab->pt_text}'"
						. ", pt_link:'{$mTab->pt_link}', pt_class:'{$mTab->pt_class}' " . '}';
		}
		$thetabs="tabs:[".join(",", $mytabs)."]";
?>
	var mydata={<?=$thetabs ?>};
	var pt=new PathTrack("ptx", "<?=$DisplayIn ?>", mydata);
	pt.Display();
});
</script>
<?php
	}
}
class PathTab
{
	public $pt_sep=false;
	public $pt_arrow=true;
	public $pt_icon="";
	public $pt_text="";
	public $pt_link="";
	public $pt_class="";
	
	function __construct($_pt_text="",$_pt_link="",$_pt_icon="",$_pt_arrow=true,$_pt_sep=false,$_pt_class="")
	{
		$this->pt_arrow=$_pt_arrow;
		$this->pt_icon=$_pt_icon;
		$this->pt_sep=$_pt_sep;
		$this->pt_text=$_pt_text;
		$this->pt_link=$_pt_link;
		$this->pt_class=$_pt_class;
	}
}
?>