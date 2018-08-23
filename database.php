<?php
class Database
{
    private static $dbName = 'id5543831_dentalclinic' ;
    private static $dbHost = 'localhost' ;
    private static $dbUsername = 'id5543831_epiz_21996389';
    private static $dbUserPassword = '30Bu8kztutKB';
     
    private static $cont  = null;
     
    public function __construct() {
        die('Init function is not allowed');
    }
     
    public static function connect()
    {
       // One connection through whole application
       if ( null == self::$cont )
       {     
        try
        {
          self::$cont =  new PDO( "mysql:host=".self::$dbHost.";"."dbname=".self::$dbName, self::$dbUsername, self::$dbUserPassword); 
          self::$cont->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e)
        {
          die($e->getMessage()); 
        }
       }

       return self::$cont;
    }
     
    public static function disconnect()
    {
        self::$cont = null;
    }
}
?>