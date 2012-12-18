<?php
/* -----------------------------
 LIP_Database_Mysql : データベース別クラス-MySQL
 /app/LIP/include/database/mysql.php
 --
 @written 12-12-18 SUSH
----------------------------- */

class LIP_Database_Mysql extends LIP_Database_Common {
    /* -----------------------------
     コンストラクタ
     Void __construct( $dns )
     @params String $dns
    ----------------------------- */
    public function __construct( $dns ) {
        parent::__construct( $dns );
    }

    /* -----------------------------
     $filedの配列順で並び替える
        MySQLはorder by fieldを利用
     Boolean add_order( $target, $field )
     --
     @param String $target
     @param Array $field
    ----------------------------- */
    public function add_order_field( $target, $field ) {
        if(! is_array( $field ) ) {
            $this->push_error( 'OPTION', 'Order Field is should be an Array' );
            return FALSE;
        }

        foreach( $field as $string ) {
            $query[] = '?';
            $this->args['ORDER'][] = $string;
        }

        $this->sql['ORDER'][] = sprintf( 'FIELD( `%s`, %s )', $target, implode( ',', $query ) );
        return TRUE;
    }
}