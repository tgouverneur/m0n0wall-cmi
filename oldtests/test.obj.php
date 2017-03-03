<?php

require_once("./inc/config.inc.php");
require_once("./lib/mysql.obj.php");

class sqlobject extends MysqlObj {

  public $id;
  public $field1;
  public $field2;
  public $field3;

  public function __construct()
  {
    $this->_my = array(
                                "id" => SQL_INDEX,                              // index of the table, only used when fetching by index
                                "field1" => SQL_WHERE | SQL_EXIST | SQL_PROPE,  // field used in WHERE statement when searching and when checking if object exist
                                "field2" => SQL_EXIST | SQL_PROPE,              // only use field when checking existance of object, also populate the field when
                                                                                // the fetch of the object is done.
                                "field3" => SQL_PROPE                          // This field is secondary and shouldn't be used to search for an object. (i.e. description)
                );
    $this->_myc = array(
                                /* mysql => obj */
                                "id" => "id",
                                "field1" => "field1",
                                "field2" => "field2",
                                "field3" => "field3",
                        );
    $this->_table = "tablatoto";
    $this->id = 42;
    $this->field1 = "mouh";
  }
}


$m = Mysql::getInstance();
if (!$m->connect()) echo $m->getError()."\n";
$s = new sqlobject();
$s->fetchId();
$s->existsDb();
$s->insert();
$s->isChanged();
$s->update();
$s->fetchFromId();

$m->disconnect();


?>
