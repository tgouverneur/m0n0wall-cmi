<?php
 /**
  * Syslog settings management
  *
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package objects
  * @subpackage syslog
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

class Syslog extends MysqlObj
{
  public $id = -1;
  public $reverse = 0;
  public $nentries = 0;
  public $remoteserver = "";
  public $filter = "";
  public $rawfilter = "";
  public $enable = 0;
  public $dhcp = 0;
  public $system = 0;
  public $portalauth = 0;
  public $vpn = 0;
  public $nologdefaultblock = 0;
  public $resolve = 0;

  /* link to other classes */
  public $mono = NULL;
  
   public $_root = "syslog";
  public $_conf = array(
				"reverse" => "bool:reverse",
				"nentries" => "var:nentries",
				"remoteserver" => "var:remoteserver",
				"filter" => "bool:filter",
				"rawfilter" => "bool:rawfilter",
				"dhcp" => "bool:dhcp",
				"enable" => "bool:enable",
				"system" => "bool:system",
				"portalauth" => "bool:portalauth",
				"vpn" => "bool:vpn",
				"nologdefaultblock" => "bool:nologdefaultblock",
				"resolve" => "bool:resolve",
			);

  function existsInDb()
  {
    return $this->existsDb();
  }

  /* other */
  public function __construct($id=-1)
  {
    $this->id = $id;
 
    $this->_table = "syslog";
    $this->_my = array(
			"id" => SQL_INDEX,	
			"reverse" => SQL_PROPE,
			"system" => SQL_PROPE,
			"portalauth" => SQL_PROPE,
			"vpn" => SQL_PROPE,
			"dhcp" => SQL_PROPE,
			"nologdefaultblock" => SQL_PROPE,
			"resolve" => SQL_PROPE,
			"nentries" => SQL_PROPE,
			"remoteserver" => SQL_PROPE|SQL_EXIST|SQL_WHERE,
			"filter" => SQL_PROPE|SQL_EXIST|SQL_WHERE,
			"rawfilter" => SQL_PROPE|SQL_EXIST|SQL_WHERE,
			"enable" => SQL_PROPE
			);
    /* var correspondance */
    $this->_myc = array( /* mysql => class */
			"id" => "id",
			"reverse" => "reverse",
			"dhcp" => "dhcp",
			"system" => "system",
			"portalauth" => "portalauth",
			"vpn" => "vpn",
			"nologdefaultblock" => "nologdefaultblock",
			"resolve" => "resolve",
			"nentries" => "nentries",
			"remoteserver" => "remoteserver",
			"filter" => "filter",
			"rawfilter" => "rawfilter",
			"enable" => "enable"
		      );


  }


}


?>
