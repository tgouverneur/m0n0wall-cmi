<?php
 /**
  * VLANs management
  *
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package objects
  * @subpackage vlan
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
                        
class Vlan extends MysqlObj
{
  public $id = -1;
  public $order = -1;
  public $tag = "";
  public $if = "";
  public $description = "";
  public $idhost = -1;

  /* link to other classes */
  public $mono = NULL;
  public $iface = NULL;
  public $_root = "vlan";

  public $_conf = array(
                                "if" => "var:if",
                                "tag" => "var:tag",
                                "descr" => "var:description"
                        );

 
  function existsInDb()
  {
    return $this->existsDb();
  }
  

  /* other */
  public function __construct($id=-1)
  {
    $this->id = $id;
    $this->_table = "vlans";
    $this->_my = array(
			"id" => SQL_INDEX,
			"order" => SQL_PROPE | SQL_SORTA,
			"tag" => SQL_PROPE|SQL_WHERE|SQL_EXIST,
			"if" => SQL_PROPE|SQL_WHERE|SQL_EXIST,
			"description" => SQL_PROPE,
			"idhost" => SQL_PROPE | SQL_WHERE|SQL_EXIST
			);
    $this->_myc = array( /* mysql => class */
                        "id" => "id",
			"order" => "order",
			"tag" => "tag",
			"if" => "if",
			"description" => "description",
			"idhost" => "idhost"
		      );


  }

}
