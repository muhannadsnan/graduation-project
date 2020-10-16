<?php

// this is a box that can contain page content. 
// it includes a header-image banner at the top, which varies in each page dynamically
// then a header title, that can come with tree (optional), and the "adding tool" (new object) 
    (also optional)
// at last, the closing of the box has to be called


if ($open) {
?>
<div class="panel">
	<? Page_Header();?>
	<div class="panel_title">
	<?php if (stristr($box_title, "ttxtg") == false) { ?>
		<div class="panel_title_text ttxtg">
			<div class="withTree"><? 
				if($withTree=="withTree")
				{
					$tree= $this->Make_Tree(basename($_SERVER['PHP_SELF'])); 
				}
				echo $tree;
			?></div>
		<?=$box_title?>
		<?$this->DisplayAddingTool( $priv, $adding);?>
		</div>
	<?php } else {?>
		<div class="panel_title_text"><?=$box_title?></div>
	<?php } ?>
	</div>
	<div class="panel_bdy" id="<?=$box_id?>">
	<?php
	}
	
	if ($close) {
	?>
	</div>
</div>
<?php
}

function Page_Header(){
	$name = explode('.', $_SERVER['PHP_SELF']);
	$page = basename($name[0]);
	$src = "../images/pages/{$page}.jpg";
	if(file_exists($src)){
		?><img src="<?=$src?>" class="pages_header"/><? 
	}else{
		$src = "../images/pages/{$page}.png";
		if(file_exists($src)){
			?><img src="<?=$src?>" class="pages_header"/><?
		}
	}
}
 