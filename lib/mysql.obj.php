<?php
 /**
  * Mysql management
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package objects
  * @subpackage mysql
  * @category classes
  * @filesource
  */
 /*
    m0n0wall Central Management Interface
    Copyright (C) 2007  Gouverneur Thomas

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
  */

/* define for sql properties array */
if (!defined('SQL_NONE')) {
 define ('SQL_NONE',   0);  /* not used */
 define ('SQL_INDEX', 1);   /* is the property an index ? */
 define ('SQL_WHERE', 2);   /* is the property an part of the where condition when search for object */
 define ('SQL_EXIST', 4);   /* is the property a part of the condition for the object to exist in the db */
 define ('SQL_PROPE', 8);   /* is the property should be fetched ? */
 define ('SQL_SORTA', 16);  /* sort with this field by ASC ? */
 define ('SQL_SORTD', 32);  /* sort with this field by DESC ? */
}
/* *** NOTE/USAGE ON THESES DEFINES ***

Usage is as following:

class sqlobject {

  public $id;
  public $field1;
  public $field2;
  public $filed3;
  
  protected $_my = array(
				"id" => SQL_INDEX,				// index of the table, only used when fetching by index CANNOT BE MIXED WITH OTHER OPTS
				"field1" => SQL_WHERE | SQL_EXIST | SQL_PROPE,  // field used in WHERE statement when searching and when checking if object exist 
				"field2" => SQL_EXIST | SQL_PROPE,		// only use field when checking existance of object, also populate the field when
										// the fetch of the object is done.
				"field3" => SQL_PROPE, 				// This field is secondary and shouldn't be used to search for an object. (i.e. description)
		);
  protected $_myc = array(
				// mysql => obj 
				"id" => "id",
				"field1" => "field1",
				"field2" => "field2",
				"field3" => "field3",
			);
}

 * </NOTE> */


/*
 **
 * Base mysql object
 **
 */
class Mysql
{
  private $_link = NULL;
  private $_res = NULL;
  private $_nres = -1;
  private $_err = NULL;		/* Latest error given by the server */
  private $_lastid = -1;	/* latest auto-generated ID created by INSERT statement */
  private $_affect = -1;	/* number of rows affected by last DELETE/INSERT/REPLACE/UPDATE statement */

  /* singleton stuff */  
  private static $_instance;	/* instance of the class */
  
  /*
   **
   * By using a singleton we ensure that we will use only one SQL instance.
   **
   */
  public static function getInstance()
  {
    if (!isset(self::$_instance)) {
     $c = __CLASS__;
     self::$_instance = new $c;
    }
    return self::$_instance;
  }

  /*
   **
   * prevent __clone() to be called
   **
   */
  public function __clone()
  {
    trigger_error("Cannot clone a singlton object, use ::instance()", E_USER_ERROR);
  }

  /*
   **
   * Accessors
   **
   */
   public function getError()
   {
     return $this->_err;
   }
 
   public function getLastId()
   {
     return $this->_lastid;
   }

   public function getAffect()
   {
     return $this->_affect;
   }

   public function getNbResult()
   {
     return $this->_nres;
   }



  /* 
   **
   * Connect to the database.
   * store the link resource in $this->_link,
   * if error, store error in $this->_error and return 0;
   * return 1 on success.
   **
   */
  public function connect()
  {
    global $config;
   
    $this->_link = mysql_connect( 	$config['mysql']['host'].':'.$config['mysql']['port'],
				   	$config['mysql']['user'],
					$config['mysql']['pass']
				);
    if ($this->_link)
    {
      if (mysql_select_db($config['mysql']['db'], $this->_link))
      {
        return 1;
      } else
      {
        $this->_err = mysql_error();
        mysql_close($this->_link);
	$this->_link = NULL;
        return 0;
      }
    } else
    {
      $this->_err = mysql_error();
      return 0;
    }
  }


  /*
   **
   * Disconnect the database link;
   * return 1 on success, 0 if the link wasn't correct.
   **
   */
  public function disconnect()
  {
    if ($this->_link)
    {
      mysql_close($this->_link);
      return 1;
    } else return 0;
  }

  /*
   **
   * Count object matching criteria
   **
   */
  public function count($table, $where="")
  {
    $query = "SELECT COUNT(*) FROM `".$table."` ".$where;
    
    $this->_nres = 0;
    
    if ($this->_query($query))
    {
      $row = mysql_fetch_array($this->_res);
      if (isset($row['COUNT(*)']))
	$data = $row['COUNT(*)'];
      mysql_free_result($this->_res);
      return $data;
    }
    else
      return 0;
  }

  /*
   **
   * Query mysql server for select
   **
   */
  public function select($fields, $table, $where="", $sort="")
  {
    $query = "SELECT ".$fields." FROM `".$table."` ".$where." ".$sort;

    $this->_nres = 0;

    if ($this->_query($query))
    {
      $data = array();
      $this->_nres = mysql_num_rows($this->_res);
      if ($this->_nres) {
        for ($i=0; $r = mysql_fetch_assoc($this->_res); $i++)
          $data[$i] = $r;

      }
      mysql_free_result($this->_res);
      return $data;
    }
    else 
      return 0;
  }

  /*
   **
   * Insert data into table
   **
   */
  public function insert($fields, $values, $table)
  {
    $query = "INSERT INTO `".$table."`(".$fields.") VALUES(".$values.")";
    
    if ($this->_query($query))
    {
      $this->_lastid = mysql_insert_id($this->_link);
      $this->_affect = mysql_affected_rows($this->_link);
      return 1;
    }
    else 
    {
     return 0;
    }
  }

  /*
   **
   * Remove data from table
   **
   */
  public function delete($table, $cond)
  {
    $query = "DELETE FROM `".$table."` ".$cond;
    
    if ($this->_query($query))
    {
      $this->_affect = mysql_affected_rows($this->_link);
      return 1;
    }
    else
    {
      return 0;
    }
  }

  /*
   **
   * update data in table
   **
   */
  public function update($table, $set, $where)
  {
    $query = "UPDATE `".$table."` SET ".$set." ".$where;
  
    if ($this->_query($query))
    {
      $this->affect = mysql_affected_rows($this->_link);
      return 1;
    }
    else
    {
      return 0;
    }
  }

  /*
   **
   * Fetch index of a table following $where condition
   **
   */
  function fetchIndex($index, $table, $where)
  {
    $query = "SELECT ".$index." FROM `".$table."` ".$where;

    $this->_nres = 0;

    if ($this->_query($query))
    {
      $data = array();
      $this->_nres = mysql_num_rows($this->_res);
      if ($this->_nres) {
        for ($i=0; $r = mysql_fetch_assoc($this->_res); $i++)
          $data[$i] = $r;

      }
      mysql_free_result($this->_res);
      return $data;
    }
    else 
      return 0;
  }


  /*
   **
   * Internal class stuff
   **
   */

  /* query database and handle errors */
  private function _query($q) 
  {
    $this->_res = mysql_query($q, $this->_link);
    if ($this->_res)
      return 1;
    else {
      $this->_err = mysql_error();
      return 0;
    }
  }
}


?>
