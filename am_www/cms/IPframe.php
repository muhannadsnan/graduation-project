<?php
interface IPframe {
	
	/**
	 * Page Header
	 * 
	 * @param string $title
	 * @param array $drawboxes
	 * @param bool $isindex
	 */
	public function header($title="",$pms=array());
	/**
	 * Body Box
	 *
	 * @param string $box_title
	 * @param string $box_type
	 */
	public function open_box($box_title="", $box_type="");
	
	/**
	 * @param string $box_type
	 */
	public function close_box($box_type="");
	
	/**
	 * Page Footer
	 *
	 * @param bool $isindex
	 */
	public function footer($pms=array());
	
}

?>
