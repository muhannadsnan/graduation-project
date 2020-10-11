<?php
require_once '../db/mysqlcon.php';
require_once '../cms/navigator.php';
class DataGrid
{
	public $Name;
	public $Columns;
	public $Rows;
	public $sql;
	public $DataSet;
	public $myTable;
	
	function __construct($DataSet=null, $_name="")
	{
		$_name=="" ? $this->name=uniqid("GRID") : $this->Name=$_name;
		$this->Columns=new DataGridCols();
		$this->Rows=new DataGridRows();
		if ($DataSet!=null) $this->DataSet=$DataSet;
	}
	
	function Fill($sql="")
	{
		if ($this->DataSet==null) return; 
		if ($sql!="") $this->sql=$sql;

		$this->myTable=new Navigator($sql,$_GET['cur_page'],30);
		while ($myRow=mysql_fetch_array($this->myTable->result))
		{
			$this->DataSet->FillIn($myRow);
			$rn=new DataGridRow();
			$rn->BuildRow($this->Columns->Items, $this->DataSet);
			$this->Rows->Add($rn);
		}
	}
	
	function AddColumns($colA)
	{
		foreach (func_get_args() as $prm)
		{
			$this->Columns->Add($prm);
		}
	}
	
	function Display($DisplayIn)
	{
?>
<script type="text/javascript">
$(document).ready (function (){
<?php
		foreach ($this->Columns->Items as $mCol)
		{
			if (user_has_permission($mCol->Permission)) {
			$mycols[]='{'."HeaderText:'{$mCol->HeaderText}', FooterText:'{$mCol->FooterText}', width:{$mCol->Width}, Type:'{$mCol->getType()}'"
						. ", Style:'{$mCol->Style}' {$mCol->getAttrs()}" . '}';
			}
		}
		$thecols="cols:[".join(",", $mycols)."]";
		if ($this->Rows->Count()!=0){
		foreach ($this->Rows->Items as $mRow)
		{
			unset($mycells);
			$myrow = "[";
			foreach ($mRow->Cells->Items as $mCell)
			{
				if (user_has_permission($this->Columns->Item($mCell->ColName)->Permission))
				{
				$mycells[]=''.$mCell->Value.'';
				}	
			}
			$myrow .= join(",",$mycells);
			$myrow .= "]";
			$myrows[]=$myrow;
		}
		}
?>
	var mydesc={<?=$thecols ?>};
	var mydata=[<? if (count($myrows)) {echo join(",",$myrows);} ?>];
	var dg=new DataGrid("gdx", "<?=$DisplayIn ?>", mydata, mydesc);
	dg.Display();
});
</script>
<?php
	}
	
	function SetAttributes()
	{
		
	}
	
	function GetAttribute()
	{
		
	}
}
//C - CELLS___________________________________________________________________________________________________________________________________________
class DataGridCell
{
	public $Name;
	public $Style;
	public $Value;
	public $ColName;
	
	function __construct($Name, $Value, $ColName, $Style="")
	{
		$this->Name=$Name;
		$this->Value=$Value;
		$this->ColName=$ColName;
		$this->Style=$Style;
	}
}

class DataGridCells extends Collection
{
	
}
//R - ROWS___________________________________________________________________________________________________________________________________________
class DataGridRow
{
	public $Name;
	public $Cells;
	
	function __construct()
	{
		$this->Name=uniqid("ROW");
		$this->Cells=new DataGridCells();
	}
	
	function BuildRow($Cols, $DataSet)
	{
		foreach ($Cols as $Col)
		{
			$myvalue=$Col->getDataValue($DataSet);
			$cn=new DataGridCell($Col->Name . "_" . $this->Name, $myvalue, $Col->Name);
			$this->Cells->Add($cn);
		}
	}
}

class DataGridRows extends Collection
{

}
//L - COLS___________________________________________________________________________________________________________________________________________
class col
{
	const Name = "Name";
	const HeaderText = "HeaderText"; 
	const Mode = "Mode";	 				// 0 ViewMode - 1 EditMode
	const ReadOnly = "ReadOnly";
	const Visible = "Visible";
	const Sortable = "Sortable";
	const Resizable = "Resizable";
	const AutoSize = "AutoSize";
	const Width = "Width";
	const MinWidth = "MinWidth";
	const FooterText = "FooterText";
	const Permission = "Permission";
	const Style = "Style";
}

class DataGridCol
{
	private $DataField = "none";		// object none		unBound, object $DataSet->FieldName Bound  
	public $Name;
	public $HeaderText = "New Field"; 
	protected $Type;						// FieldType Enum
	public $Mode = 0;	 				// 0 ViewMode - 1 EditMode
	public $ReadOnly = false;
	public $Visible = true;
	public $Sortable = true;
	public $Resizable = true;
	public $AutoSize = false;
	public $Width = 100;
	public $MinWidth = 10;
	public $FooterText = "";
	public $Permission = array('N');
	public $Style = "";
	
	public function getType()
	{
		return $this->Type;
	}
	
	function BindTo($DataField)
	{
		$this->DataField=$DataField['name'];
		$this->HeaderText=constant($DataField['caption']);
		if (is_array($DataField['permission'])){
			$this->Permission=$DataField['permission'];
		} 
	}
	
	function getDataFieldName()
	{
		return $this->DataField;
	}
	
	function IsBounded()
	{
		 if ($this->DataField == "none") return false;
		 else return true;
	}
	
	public function SetAttributes($Description)
	{
		if (is_array($Description))
		{
			foreach ($Description as $k=>$v)
			{
				$this->$k=$v;
			}
		}
	}

	function getDataValue($DataSet)
	{
		if ($this->IsBounded()){
			$value=$DataSet->{$this->getDataFieldName()}['value']; return $value;
		} else {
			$myvalue=""; return $value; //HERE ADD OTHER UNBOUNDED VALUES ACCORDING TO getDataValue() FUNCTION
		}
	}
	
	function getFldValue($DataSet)
	{
		$thecol=$DataSet->{$this->getDataFieldName()};
			
				if (in_array($thecol['type'], array('varchar', 'char', 'text', 'ID', 'int', 'datetime', 'float')))
				{
					if (in_array($thecol['control'], array('fkey'))){
						//if ($thecol['showkey']){$thefkey=$tblrow[$col]." - ";} ???
						$fldval = get_data_in("select {$thecol['fTitle']} from {$thecol['ftbl']} where {$thecol['fID']} like '{$thecol['value']}' ", $thecol['fTitle']);
						return $fldval;
					}elseif (in_array($thecol['control'], array('list'))){
						$fldval = constant($thecol['options'][$thecol['value']]);
						return $fldval;
					}else{
						$fldval = $thecol['value'];
						return $fldval;
					}
				} elseif (in_array($thecol['type'], array('file')) ){
						$fldval = $thecol['ftype'];
						return $fldval;
				}
				/* elseif (in_array($thecol['type'], array('file')) && in_array($thecol['view'], array('image'))){
					if ($thecol['resize']==true)
					{
						echo '<td><img src="'.$DataSetTmp->thumbs_folder.$thecol['sizes']['thumb']['p'].$thecol['prefix'].$tblrow[$DataSetTmp->NID['name']].".".$tblrow[$col].'" /></td>';
					}else {
						echo "<td><img src=\"{$DataSetTmp->documents_folder}{$thecol['prefix']}{$tblrow[$DataSetTmp->NID['name']]}.{$tblrow[$col]}\" /></td>";
					}
				}elseif (in_array($thecol['type'], array('file')) && $thecol['view']=='link'){
					echo "<td><a href=\"{$DataSetTmp->documents_folder}{$thecol['prefix']}{$tblrow[$DataSetTmp->NID['name']]}.{$tblrow[$col]}\">".View."</a></td>";
				} */
	}
	
	function getAttrs()
	{
		return "";
	}
}

class DataGridTextCol extends DataGridCol
{
	public $WordWrap = false;
	
	function __construct($DataField = null, $Attributes = null, $WordWrap=false)
	{
		$this->Name=uniqid("COL");
		$this->Type=FieldType::Text;
		if ($DataField != null) $this->BindTo($DataField);
		if (is_array($Attributes)) $this->SetAttributes($Attributes);
		$this->WordWrap=$WordWrap;
	}
	
	function getDataValue($DataSet)
	{
		if ($this->IsBounded()){
			$value="'" . $this->getFldValue($DataSet) . "'"; return $value;
		} else {
			$myvalue=""; return $value; //HERE ADD OTHER UNBOUNDED VALUES ACCORDING TO getDataValue() FUNCTION
		}
	}
}

class DataGridHyperLinkCol extends DataGridCol
{	
	public $NavigateURL;
	public $NavigateURLFields;
	public $ValueText;
	
	function __construct($DataField = null, $NavigateURL = "", $NavigateURLFields = array(), $Attributes = null, $ValueText = "{0}")
	{
		$this->Name=uniqid("COL");
		$this->Type=FieldType::HyperLink;
		if ($DataField != null) $this->BindTo($DataField);
		if (is_array($Attributes)) $this->SetAttributes($Attributes);
		$this->NavigateURL=$NavigateURL;
		$this->NavigateURLFields=$NavigateURLFields;
		$this->ValueText=$ValueText;
	}
	
	function getDataValue($DataSet)
	{
		if ($this->IsBounded()){
			$text = str_ireplace('{0}', $this->getFldValue($DataSet),$this->ValueText);
			foreach ($this->NavigateURLFields as $k=>$fldnm)
			{
				$valarr[$k]=$DataSet->{$fldnm}['value'];
			}
			$link = gen_str($this->NavigateURL, $valarr);
			$value="['" . $text . "','" . $link . "']"; return $value;
		} else {
			$myvalue=""; return $value; //HERE ADD OTHER UNBOUNDED VALUES ACCORDING TO getDataValue() FUNCTION
		}
	}
}


class DataGridCheckBoxCol extends DataGridCol
{
	public $DefaultValue = false;
	
	function __construct()
	{
		$this->Type=FieldType::CheckBox;
	}
}


class DataGridImageCol extends DataGridCol
{
	public $ImgURL = "auto";	// auto		detect getFilePath() function in Dataset if Bound else URL should be specified
	public $ImgURLFields;
	public $SizeName = "thumb";	// Size name as defined in DataSet, if unBound field width and height determine size
	public $Width = 0;			// 0	Auto
	public $Height = 0;			// 0	Auto
	
	function __construct()
	{
		$this->Type=FieldType::Image;
	}
	
	
}

class DataGridButtonCol extends DataGridCol
{	
	public $ButtonType = 0;		// 0	Link, 1		Button, 2	Image
	public $ImgURL;
	public $btnText;
	public $TargetURL;
	public $TargetURLFields;
	public $ImgURLFields;
	public $TargetWindow;
	public $DefLink;
	
	function __construct($ButtonType = 0, $DataField = null, $btnText = "{0}", $ImgURL = "", $TargetURL = "", $TargetURLFields = "", $Attributes = null, $ImgURLFields = "", $TargetWindow = "", $DefLink = "")
	{
		$this->Name=uniqid("COL");
		$this->Type=FieldType::Button;
		if ($DataField != null) $this->BindTo($DataField);
		if (is_array($Attributes)) $this->SetAttributes($Attributes);
		$this->ButtonType=$ButtonType;
		$this->btnText=$btnText;
		$this->ImgURL=$ImgURL;
		$this->ImgURLFields=$ImgURLFields;
		$this->TargetURL=$TargetURL;
		$this->TargetURLFields=$TargetURLFields;
		$this->TargetWindow=$TargetWindow;
		$this->DefLink=$DefLink;
		
	}
	
	function getDataValue($DataSet)
	{
		if ($this->IsBounded()){
			$text = str_ireplace('{0}', $this->getFldValue($DataSet),$this->btnText);
		} else {
			$text = "";
		}
			foreach ($this->TargetURLFields as $k=>$fldnm)
			{
				$valarr[$k]=$DataSet->{$fldnm}['value'];
				if ($DataSet->{$fldnm}['type']=='file') {
					$valarr[$k]=$DataSet->{$fldnm}['ftype'];
				}
			}
			$link = gen_str($this->TargetURL, $valarr);
			
			$value="['" . $link . "', ";
			if ($text!="") {$value .= "'" . $text . "'";}
			if ($this->ImgURLFields!="") 
			{
				foreach ($this->ImgURLFields as $k=>$fldnm)
				{
					$valarr[$k]=$DataSet->{$fldnm}['value'];
					if ($DataSet->{$fldnm}['type']=='file') {
						$valarr[$k]=$DataSet->{$fldnm}['ftype'];
						if ($valarr[$k]=="" || $valarr[$k]==null) {$setdef=true;}
					}
				}
				
				$imglink = gen_str($this->ImgURL, $valarr);
				if ($setdef){ $imglink=$this->DefLink; }
				
				$value .= ", '" . $imglink . "'";
			}
			$value .= "]"; return $value;
	}
	
	function getAttrs()
	{
		$strAtr=", ButtonType:'{$this->ButtonType}', btnText:'{$this->btnText}', ImgURL:'{$this->ImgURL}', TargetWindow:'{$this->TargetWindow}'";
		return $strAtr;
	}
}

class DataGridCommandEditCol extends DataGridButtonCol 
{	
	function __construct($btnText="Edit", $Attributes = null, $TargetWindow = "")
	{
		$this->Name=uniqid("COL");
		$this->Type=FieldType::Button;
		if (is_array($Attributes)) $this->SetAttributes($Attributes);
		$this->ButtonType=2;
		$this->btnText=$btnText;
		$this->ImgURL="../images/edtimg.png";
		$this->ImgURLFields="";
		$this->TargetURL=$_SERVER['PHP_SELF']."?lang={$GLOBALS['lang']}&NID={0}&v=e&".get_pms();
		$this->TargetURLFields=array("NID");
		$this->TargetWindow=$TargetWindow;
		$this->HeaderText="";
		$this->Width=40;
	}
}

class DataGridCommandDeleteCol extends DataGridButtonCol
{	
	function __construct($btnText="Delete", $Attributes = null, $TargetWindow = "")
	{
		$this->Name=uniqid("COL");
		$this->Type=FieldType::Button;
		if (is_array($Attributes)) $this->SetAttributes($Attributes);
		$this->ButtonType=2;
		$this->btnText=$btnText;
		$this->ImgURL="../images/dltimg.png";
		$this->ImgURLFields="";
		$this->TargetURL=$_SERVER['PHP_SELF']."?lang={$GLOBALS['lang']}&NID={0}&v=d&".get_pms();
		$this->TargetURLFields=array("NID");
		$this->TargetWindow=$TargetWindow;
		$this->HeaderText="";
		$this->Width=40;
	}
}

class FieldType
{
	const Text = 0;
	const CheckBox = 1;
	const HyperLink = 2;
	const Image = 3;
	const Button = 4;
	const CommandEdit = 5;
	const CommandDelete = 6;
}

class DataGridCols extends Collection 
{
	public $Current;
	
	function __construct()
	{
		$this->Current=new DataGridCol();
	}
	
	function Item($id)
	{
		if (is_int($id)) {$this->Current=&$this->Items[$id]; return $this->Current;}
		foreach ($this->Items as $k=>$mCol)
		{
			if ($mCol->getDataFieldName() == $id || $mCol->Name == $id) {$this->Current=&$this->Items[$k]; return $this->Current;}
		}
		return null;
	}
}
//COLLECTION___________________________________________________________________________________________________________________________________________
class Collection
{
	public $Items;
	
	function Add($_Value)
	{
		$this->Items[]=$_Value;
	}
	
	function Insert($_Value)
	{
		
	}
	
	function Remove($_Key)
	{
		
	}
	
	function Count()
	{
		return count($this->Items);
	}
}
?>