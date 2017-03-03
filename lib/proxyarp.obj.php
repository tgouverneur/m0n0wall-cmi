<?php
 /**
  * ProxyARP Management
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package objects
  * @subpackage proxyarp
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

class ProxyArp extends MysqlObj
{
  public $id = -1;
  public $if = "";
  public $network = "";
  public $from = "";
  public $to = "";
  public $idhost = -1;
  public $description = "";

  public $_root = "proxyarpnet";
  public $_conf = array(
				"interface" => "var:if",
				"network" => "varo:network",
				"from" => "varo:from",
				"to" => "varo:to",
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

    $this->_table = "proxyarp";

    $this->_my = array(
			"id" => SQL_INDEX,
			"if" => SQL_PROPE | SQL_WHERE | SQL_EXIST,
			"network" => SQL_PROPE | SQL_WHERE | SQL_EXIST,
			"from" => SQL_PROPE | SQL_WHERE | SQL_EXIST,
			"to" => SQL_PROPE | SQL_WHERE | SQL_EXIST,
			"description" => SQL_PROPE,
			"idhost" => SQL_PROPE | SQL_WHERE | SQL_EXIST
			);

    $this->_myc = array( /* mysql => class */
                        "id" => "id",
                        "if" => "if",
			"network" => "network",
			"from" => "from",
			"to" => "to",
			"description" => "description",
			"idhost" => "idhost"
		      );


  }
}


?>
