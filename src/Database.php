<?php
/**
 * Description of database
 *
 * @author HERVE Tchokote
 */
class Database 
{
    protected $dbcon;

    public $dbname = "db_mailerlite_subcribers";
    public $dbuser = "root";
    public $dbpass = "";
    public $dbhost = "localhost";

    public function connect() 
    {
        
        $this->dbcon = null;

       try {

           $this->dbcon = new PDO("mysql:host=$this->dbhost;dbname=$this->dbname", $this->dbuser, $this->dbpass, array(\PDO::MYSQL_ATTR_INIT_COMMAND =>  'SET NAMES utf8', \PDO::ATTR_EMULATE_PREPARES => false, \PDO::ATTR_STRINGIFY_FETCHES => false));
           $this->dbcon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

       } Catch(PDOException $e) {

            echo $e->Message();

       }
       return $this->dbcon;

   }
    
}

?>
