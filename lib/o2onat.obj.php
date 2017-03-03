<?php
 /**
  * One to One NAT ruling
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package objects
  * @subpackage nat
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

class O2ONat extends MysqlObj
{
  public $internal = "";
  public $external = "";
  public $subnet = "";

  public $id = -1;
  public $description = "";
  public $if = "";
  public $idhost = -1;

  public $iface = NULL;
  public $mono = NULL;


 
  public $_root = "onetoone";
  public $_conf = array(
				"interface" => "var:if",
				"external" => "var:external",
				"internal" => "var:internal",
				"subnet" => "var:subnet",
				"descr" => "var:description",
			);


  function existsInDb()
  {
     return $this->existsDb();
  }



  public function __construct($id=-1)
  {
    $this->id = $id;
    $this->_table = "nat-one2one";

    $this->_my = array(
			"id" => SQL_INDEX,
			"description" => SQL_PROPE,
			"if" => SQL_PROPE | SQL_WHERE | SQL_EXIST,
			"idhost" => SQL_PROPE | SQL_WHERE | SQL_EXIST
			);
    $this->_myc = array( /* mysql => class */
			"id" => "id",
			"description" => "description",
			"if" => "if",
			"idhost" => "idhost"
			);




    $this->_my = array_merge($this->_my, array(
						"internal" => SQL_PROPE | SQL_WHERE | SQL_EXIST,
						"external" => SQL_PROPE | SQL_WHERE | SQL_EXIST,
						"subnet" => SQL_PROPE,
		));
    $this->_myc = array_merge($this->_myc, array( /* mysql => class */
						"internal" => "internal",
			     			"external" => "external",
						"subnet" => "subnet"
			 	 ));
  

  }
 
}

?>
