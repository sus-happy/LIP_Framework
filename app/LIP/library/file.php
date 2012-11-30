<?php
/* -----------------------------
 LL_File : ファイル読み書き拡張クラス
 /app/LIP/library/file.php
 --
 @written 12-11-30 SUSH
----------------------------- */

class LL_File extends LIP_Object {
			/* アップロードディレクトリ */
	private $up_dir = "",
			/* アップ制限 */
			$max_file_size = 2,
			$file_type = array("jpg", "jpeg", "png", "gif"),
			/* アップファイル情報 */
			$data = array(),
			/* エラーメッセージ */
			$error_message = array();

	/* -----------------------------
	 コンストラクタ
	 Void __construct()
	----------------------------- */
	public function __construct() {
		$this->disable_exeption_flag();
	}

	/* -----------------------------
	 アップロードディレクトリの設定
	 Boolean set_upload_dir( $dir )
	 --
	 @param String $dir
	----------------------------- */
	public function set_upload_dir( $dir ) {
		if( is_writable( $dir ) ) {
			$this->up_dir = $dir;
			return TRUE;
		} else return FALSE;
	}

	/* -----------------------------
	 アップロードディレクトリの取得
	 Void get_upload_dir()
	----------------------------- */
	public function get_upload_dir() {
		return $this->up_dir;
	}

	/* -----------------------------
	 アップロード制限サイズの指定
	 Boolean set_max_file_size( $size )
	 --
	 @param Integer $size
	----------------------------- */
	public function set_max_file_size( $size ) {
		if( is_numeric( $size ) ) {
			$this->max_file_size = $size;
			return TRUE;
		} return FALSE;
	}

	/* -----------------------------
	 アップロードファイル制限タイプの指定
	 Boolean set_file_type( $type )
	 --
	 @param Array $type
	----------------------------- */
	public function set_file_type( $type ) {
		if( is_array( $type ) ) {
			$this->file_type = $type;
			return TRUE;
		} return FALSE;
	}

	/* -----------------------------
	 何だっけこれ…
	 	@todo 思い出す
	 Void setup_data( $type )
	 --
	 @param String $name
	 @param Mixed $data
	----------------------------- */
	public function setup_data( $name, $data ) {
		$this->data[$name] = $data;
	}

	/* -----------------------------
	 何だっけこれ…
	 	@todo 思い出す
	 Boolean get_data( $name )
	 --
	 @param String $name
	----------------------------- */
	public function get_data( $name ) {
		return $this->data[$name];
	}

	/* -----------------------------
	 アップロード処理
	 Boolean upload( $name, $up_name, $label = "ファイル" )
	 --
	 @param String $name
	 	input type="file"のname属性
	 @param String $up_name
	 	保存する名前
	 @param String $label
	 	エラーメッセージに表示する名称
	----------------------------- */
	public function upload( $name, $up_name, $label = "ファイル" ) {
		$flag = TRUE;

		if ( $_FILES[$name]["size"] !== 0 && ! empty( $_FILES[$name] ) ) {
			$extension = pathinfo($_FILES[$name]["name"], PATHINFO_EXTENSION);
			if( $_FILES[$name]["size"] > $this->max_file_size*1024*1024 ) {
				$flag = false;
				$this->push_error( $name, sprintf( "%sのサイズが大きすぎます（最大%dMB）", $label, $this->max_file_size ) );
			}
			if( !empty($this->file_type) ) {
				if(! in_array($extension, $this->file_type) ) {
					$flag = false;
					$this->push_error( $name, sprintf( "%sに非対応のファイル型式が選択されています（対応ファイル：%s）", $label, implode( ",", $this->file_type ) ) );
				}
			}

			if( $flag ) {
				$file_place = sprintf( "%s/%s.%s", $this->up_dir, $up_name, $extension );
				if( @move_uploaded_file( $_FILES[$name]["tmp_name"], $file_place ) ) {
					$data = array();
					list( $data["size"]["width"], $data["size"]["height"], $data["file_meta"], $data["attr"] ) = getimagesize( $file_place );
					$data["file_meta"] = image_type_to_mime_type( $data["file_meta"] );
					$data["file_origin_name"] = $_FILES[$name]["name"];
					$data["file_name"] = $up_name.".".$extension;
					$this->setup_data( $name, $data );
				} else {
					$flag = FALSE;
					$this->push_error( $name, sprintf( "%sのアップロードに失敗しました", $label ) );
				}
			}
		} else {
			$flag = FALSE;
			$this->push_error( $name, sprintf( "%sが選択されていません", $label ) );
		}
		return $flag;
	}

	/* -----------------------------
	 コピー
	 Boolean copy( $from, $to )
	 --
	 @param String $from
	 @param String $to
	----------------------------- */
	public function copy( $from, $to ) {
		$from_file = sprintf( "%s/%s", $this->up_dir, $from );
		$to_file = sprintf( "%s/%s", $this->up_dir, $to );
		return @ copy( $from_file, $to_file );
	}

	/* -----------------------------
	 サイズを変更してコピー
	 Boolean thum_copy( $idata, $toname, $w = NULL, $h = NULL, $q = 90, $f = FALSE )
	 --
	 @param Array $idata
	 @param String $toname
	 @param Integer $w
	 @param Integer $h
	 @param Integer $q
	 @param Boolean $f
	 @todo idataは微妙な気がする…
	----------------------------- */
	public function thum_copy( $idata, $toname, $w = NULL, $h = NULL, $q = 90, $f = FALSE ) {
		if( empty( $q ) ) $q = 90;

		if( $idata ) {
			$file = $this->up_dir."/".$idata["file_name"];
			switch( $idata["file_meta"] ) {
				case "image/jpg":
				case "image/jpe":
				case "image/jpeg":
				case "image/pjpeg":
					$gimg = imagecreatefromjpeg( $file );
				break;
				case "image/gif":
					$gimg = imagecreatefromgif( $file );
				break;
				case "image/png":
					$gimg = imagecreatefrompng( $file );
				break;
				default:
					echo "NotImage";
					exit;
				break;
			}

			$w = is_numeric( $w ) ? $w : $idata["size"]["width"];
			$h = is_numeric( $h ) ? $h : $idata["size"]["height"];
			/* 比率無視 */
			if( empty( $f ) ) {
				$sw = $w/$idata["size"]["width"];
				$sh = $h/$idata["size"]["height"];
				if( $sw < 1 || $sh < 1 ) {
					if( $sw <= $sh ) {
						$h = $idata["size"]["height"]*$sw;
					} else {
						$w = $idata["size"]["width"]*$sh;
					}
				} else {
					$w = $idata["size"]["width"];
					$h = $idata["size"]["height"];
				}
			}
			$timg = imagecreatetruecolor($w, $h);
			imagecopyresampled(
				$timg,		//貼り付けするイメージID
				$gimg,		//コピーする元になるイメージID
				0,        	//int dstX (貼り付けを開始するX座標)
				0,        	//int dstY (貼り付けを開始するY座標)
				0,        	//int srcX (コピーを開始するX座標)
				0,        	//int srcY (コピーを開始するY座標)
				$w, 		//int dstW (貼り付けする幅)
				$h,	 	//int dstH (貼り付けする高さ)
				$idata["size"]["width"],   	//int srcW (コピーする幅)
				$idata["size"]["height"]   	//int srcH (コピーする高さ)
			);

			// 表示
			$tofile = $this->up_dir."/".$toname;
			ImageJPEG($timg, $tofile, $q);

			imagedestroy( $timg );
			imagedestroy( $gimg );
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/* -----------------------------
	 削除
	 Boolean delete( $name )
	 --
	 @param String $name
	----------------------------- */
	public function delete( $name ) {
		$file_place = sprintf( "%s/%s", $this->up_dir, $name );
		return @ unlink( $file_place );
	}
}