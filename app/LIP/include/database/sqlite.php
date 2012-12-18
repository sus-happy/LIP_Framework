<?php
/* -----------------------------
 LIP_Database_Sqlite : データベース別クラス-SQLite
 /app/LIP/include/database/mysql.php
 --
 @written 12-12-18 SUSH
----------------------------- */

class LIP_Database_Sqlite extends LIP_Database_Common {
    /* -----------------------------
     コンストラクタ
     Void __construct( $dns )
     @params String $dns
    ----------------------------- */
    public function __construct( $dns ) {
        parent::__construct( $dns );
    }
}