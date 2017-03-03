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
/*
 **
 * Base class for all object that use mysql
 **
 */
class MysqlObj 
{
  protected $_my = array();
  protected $_table = "";
  
  /*
   **
   * mysql common functions
   **
   */
  /* Fetch object's index in the table */
  function fetchId()
  {
    $id = array_search(SQL_INDEX, $this->_my);
    if ($id === FALSE) 
      return 0; /* no index in obj */

    $where = "WHERE ";
    $i=0;
    foreach ($this->_my as $k => $v) {
      if ($v & SQL_WHERE)
      {
        if ($i && $i < count($this->_my)) $where .= " AND ";

        $where .= "`".$k."`='".$this->{$this->_myc[$k]}."'";
        $i++;
      }
    }
    
    $my = Mysql::getInstance();
    if (($data = $my->select("`".$id."`", $this->_table, $where)))
    {
      if ($my->getNbResult() == 1)
      {
        $this->{$this->_myc[$id]} = $data[0][$id];
      }
      else return 0;
    } else return 0;
  }

  /* insert object in database  */
  function insert()
  {
    $values = "";
    $names = "";
    $i=0;
    foreach ($this->_my as $k => $v) {

      if ($v == SQL_INDEX) continue; /* skip index */

      if ($i && $i < count($this->_my)) { 
        $names .= ","; $values .= ","; 
      }
      $names .= "`".$k."`";
      $values .= "'".$this->{$this->_myc[$k]}."'";
      $i++;
    }
    $my = Mysql::getInstance();
    $r = $my->insert($names, $values, $this->_table);
    $id = array_search(SQL_INDEX, $this->_my);
    if ($id !== FALSE) $this->{$id} = $my->getLastId();
    return $r;
  }

  /* update the object into database */
  function update()
  {
    $id = array_search(SQL_INDEX, $this->_my);
    if ($id === FALSE)
      return 0; /* no index in obj */

    $where = "WHERE `".$id."`='".$this->{$this->_myc[$id]}."'";
    $set = "";
    $i = 0;
    foreach ($this->_my as $k => $v) {

      if ($v == SQL_INDEX) continue; /* skip index */

      if ($i && $i < count($this->_my)) { 
        $set .= ","; 
      }
      $set .= "`".$k."`='".$this->{$this->_myc[$k]}."'";
      $i++;
    }
    $my = Mysql::getInstance();
    return $my->update($this->_table, $set, $where);

  }

  /* does the object exists in database ? */
  function existsDb()
  {
    $where = " WHERE ";
    $i = 0;
    foreach ($this->_my as $k => $v) {
      
      if ($v == SQL_INDEX) continue; /* skip index */
      if (!($v & SQL_EXIST)) continue; /* skip properties that shouldn't define unicity of object */
      if ($i && $i < count($this->_my)) $where .= " AND ";

      $where .= "`".$k."`='".$this->{$this->_myc[$k]}."'";
      $i++;
    }
    
    $id = array_search(SQL_INDEX, $this->_my);

    if ($id === FALSE)
    {
      $id = array_keys($this->_my); /* if no index, take the first field of the table */
      $id = $id[0];
    } 

    $my = Mysql::getInstance();
    if (($data = $my->select("`".$id."`", $this->_table, $where)) == FALSE)
      return 0;
    else {
      if ($my->getNbResult()) {
        if ($this->{$this->_myc[$id]} != -1 && $data[0][$id] == $this->{$this->_myc[$id]}) { return 1; }
        if ($this->{$this->_myc[$id]} == -1) return 1;
      } else
        return 0;
    }
  }

  /* has the object changed ? */
  function isChanged()
  {
    $where = " WHERE ";
    $i = 0;
    
    if (!$this->existsDb()) return 0;

    foreach ($this->_my as $k => $v) {
      
      if ($v == SQL_INDEX) continue; /* skip index */
      if (!($v & SQL_PROPE)) continue;
      if ($i && $i < count($this->_my)) $where .= " AND ";

      $where .= "`".$k."`='".$this->{$this->_myc[$k]}."'";
      $i++;
    }
    
    $id = array_search(SQL_INDEX, $this->_my);

    if ($id !== FALSE) {
      
      if ($this->{$this->_myc[$id]} != -1) $where .= " AND `".$id."`='".$this->{$this->_myc[$id]}."'";
      
      $my = Mysql::getInstance();
      if (($data = $my->select("`".$id."`", $this->_table, $where)) == FALSE)
        return 1;
      else {
        if ($my->getNbResult()) {
          return 0;
        } else
	  return 1;
      }
    }
   
  }

  /* fetch object with XXX */
  function fetchFromField($field)
  {
    $i = 0;
    $fields = "";
    foreach ($this->_my as $k => $v) {
      if ($i && $i < count($this->_my)) $fields .= ",";

      $fields .= "`".$k."`";
      $i++;
    }    

    $where = "WHERE `".$field."`='".$this->{$this->_myc[$field]}."'";

    $my = Mysql::getInstance();
    if (($data = $my->select($fields, $this->_table, $where)) == FALSE)
      return 0;
    else
    {
      if ($my->getNbResult() != 0)
      {
        foreach ($data[0] as $k => $v) {
          if (array_key_exists($k, $this->_myc))
          {
            $this->{$this->_myc[$k]} = $v;
          }
        }
      } else return 0;
    }
  }


  /* fetch object with INDEX */
  function fetchFromId()
  {
    $i = 0;
    $fields = "";
    foreach ($this->_my as $k => $v) {
      if ($v != SQL_INDEX)
      {
        if ($i && $i < count($this->_my)) $fields .= ",";

        $fields .= "`".$k."`";
        $i++;
      }
    }    
    $id = array_search(SQL_INDEX, $this->_my);
    if ($id !== FALSE && $this->{$this->_myc[$id]} != -1) {

      $where = "WHERE `".$id."`='".$this->{$this->_myc[$id]}."'";

      $my = Mysql::getInstance();
      if (($data = $my->select($fields, $this->_table, $where)) == FALSE)
        return 0;
      else
      {
        if ($my->getNbResult() != 0)
        {
          foreach ($data[0] as $k => $v) {
            if (array_key_exists($k, $this->_myc))
            {
              $this->{$this->_myc[$k]} = $v;
            }
          }
        } else return 0;
      }
    } else return 0;

    return $id;
  }

  function delete()
  {
    $i = 0;
    $w = "WHERE ";
    foreach ($this->_my as $k => $v) {
      if ($v == SQL_INDEX)
      {
        if ($i && $i < count($this->_my)) $fields .= ",";

        $w .= "`".$k."`='".$this->{$this->_myc[$k]}."'";
        $i++;
      }
    }
    $id = array_search(SQL_INDEX, $this->_my);
    return Mysql::getInstance()->delete($this->_table, $w);
  }
  /* ... */

}


?>
