<?php
 /**
  * Aliases management
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package objects
  * @subpackage alias
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

class Alias extends MysqlObj
{
  public $id = -1;
  public $name = "";
  public $address = "";
  public $description = "";

  public $idhost = -1;
  public $mono = NULL;

  public $_root = "alias";
  public $_conf = array(
                                "name" => "var:name",
                                "address" => "var:address",
                                "descr" => "var:description"
                        );

  public function existsInDb() { return $this->existsDb(); }

  /* other */
  public function __construct($id=-1)
  {
    $this->id = $id;
    $this->_table = "alias";
    $this->_myc = array( /* mysql => class */
                          "id" => "id",
		  	  "name" => "name",
			  "address" => "address",
			  "description" => "description",
			  "idhost" => "idhost"
		        );
    $this->_my = array(
			"id" => SQL_INDEX,
			"name" => SQL_PROPE | SQL_WHERE | SQL_EXIST,
			"address" => SQL_PROPE | SQL_WHERE,
			"description" => SQL_PROPE,
			"idhost" => SQL_PROPE | SQL_WHERE | SQL_EXIST
			);

  }
}


?>
