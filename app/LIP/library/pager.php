<?php
/*
 * ぺージャー拡張クラス
 * /app/LIP/library/pager.php
 */

class LL_Pager {
	private $path = NULL,
			$total = 0,
			$per_page = 1,
			$page = 1;
	
	// ページャーラッパー
	private $wrap = array(
		'before'=> '<div class="pagination pagination-centered"><ul>',
		'after' => '</ul></div>',
	);
	// ページ番号ラッパー
	private $num_wrap = array(
		'before'=> '<li><a href="%link%">&laquo;</a></li>',
		'after' => '<li><a href="%link%">&raquo;</a></li>',
		'number'=> '<li><a href="%link%">%number%</a></li>',
		'active'=> '<li><span>%number%</span></li>',
	);
	
	public function __construct() {
	}
	public function set_base_path( $path ) {
		$this->path = $path;
	}
	public function set_total_count( $total ) {
		$this->total = $total;
	}
	public function set_per_page( $per_page ) {
		$this->per_page = $per_page;
	}
	public function set_current_page( $page ) {
		$this->page = $page;
	}
	public function view() {
		if( $this->page > 1 || ceil($this->total/$this->per_page) > 1 ) {
			echo $this->wrap["before"];
			
			if( $this->page > 1 )
				echo str_replace( "%link%", site_url( sprintf( "%s/%s", $this->path, $this->page-1) ), $this->num_wrap["before"] );
			for( $i=1; $i<=ceil($this->total/$this->per_page); $i++ ) {
				if ($this->page == $i) {
					echo str_replace(
						array( "%number%" ),
						array( $i ),
						$this->num_wrap["active"]
					);
				} else {
					echo str_replace(
						array( "%link%", "%number%" ),
						array( site_url( sprintf( "%s/%s", $this->path, $i) ), $i ),
						$this->num_wrap["number"]
					);
				}
			}
			if( $this->page*$this->per_page < $this->total )
				echo str_replace( "%link%", site_url( sprintf( "%s/%s", $this->path, $this->page+1) ), $this->num_wrap["after"] );
			echo $this->wrap["after"];
		}
	}
}