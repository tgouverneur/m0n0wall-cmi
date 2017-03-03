<?php
 /**
  * Backup Users used to fetch configuration and restore it to m0n0wall devices
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package objects
  * @subpackage config
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

class Buser extends MysqlObj
{
  public $id = -1;		/* ID in the MySQL table */
  public $login;		/* login for backup user */
  public $password;		/* password for backup user */
  public $description;		/* 255 char */

  private $_modified = FALSE;

   /* ctor */
  public function __construct($id=-1) 
  { 
    $this->id=$id;
  
    $this->_table = "busers";

    $this->_my = array(
				"id" => SQL_INDEX,
				"login" => SQL_PROPE | SQL_EXIST | SQL_WHERE,
				"password" => SQL_PROPE | SQL_WHERE,
				"description" => SQL_PROPE
				
			);
 
    $this->_myc = array( /* mysql => class */
		  "id" => "id", 
		  "login" => "login",
		  "password" => "password",
		  "description" => "description"
 		 );


  }
}

?>
