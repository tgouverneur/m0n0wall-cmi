<?php
 /**
  * Unknown element management
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package objects
  * @subpackage unknown
  * @category classes
  * @filesource
  */

class Unknown extends MysqlObj
{
  public $id = -1;
  public $name = "";
  public $value = "";
  public $idparent = -1;
  public $idhost = -1;
  public $parent = null;


  /* link */
  public $mono = NULL;

  function existsInDb()
  {
    return $this->existsDb();
  }

  /* ctor */
  public function __construct($id=-1, $name="", $value="", $parent=null)
  {
    $this->id = $id;
    $this->name = $name;
    $this->value = $value;
    $this->parent = $parent;

    $this->_table = "unknown";

    $this->_myc = array( /* mysql => class */
			"id" => "id",
			"name" => "name",
			"value" => "value",
			"parent" => "idparent",
			"idhost" => "idhost"
			);

    $this->_my = array(
			"id" => SQL_INDEX,
			"name" => SQL_PROPE | SQL_WHERE|SQL_EXIST,
			"value" => SQL_PROPE,
			"parent" => SQL_PROPE | SQL_WHERE|SQL_EXIST,
			"idhost" => SQL_PROPE | SQL_WHERE|SQL_EXIST
			);
  }
}
