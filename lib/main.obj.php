<?php
 /**
  * Main class (Pattern: Singleton)
  * Used in whole project to link object to each-other and
  * to gather easily data from DB.
  *
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package objects
  * @subpackage Main
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

/**
  * Main class used in the whole project as singleton.
  * Link all object to each other and gather base objects
  * from database.
  *
  * @category classes
  * @package objects
  * @subpackage Main
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  *
  */
class Main
{
  /**
   * array of m0n0wall object's
   * @see Monowall, fetchMonoId(), fetchMonoDetails()
   * @var array
   */
  public $monowall = array();	/* array of monowall devices */
  /**
   * array of firewall rules
   * @see Rule
   * @var array
   */
  public $rules = array();	/* array of rules */
  /**
   * array of RuleInt objects
   * @see RuleInt
   * @var array
   */
  public $ruleint = array();	/* array of association of rules */
  /**
   * array of Global Aliases
   * @see GAlias
   * @var array
   */
  public $galiases = array();	/* global aliases */

  /**
   * array of Backup Users
   * @see Buser
   * @var array
   */
  public $busers = array(); /* array of all busers */

  /**
   * Instance of Main class (singleton) 
   * @see Main
   * @var Main
   */
  private static $_instance;    /* instance of the class */

  /**
   * By using a singleton we ensure that we will use only one Main instance.
   * 
   * @return Main Instance of the Main class.
   */
  public static function getInstance()
  {
    if (!isset(self::$_instance)) {
     $c = __CLASS__;
     self::$_instance = new $c;
    }
    return self::$_instance;
  }

  /**
   * prevent __clone() to be called
   *
   * @return void
   */
  public function __clone()
  {
    trigger_error("Cannot clone a singlton object, use ::instance()", E_USER_ERROR);
  }

  /**
   * Find corresponding rule matching $id.
   * @param int $id Id of Rule object requested
   * @return Rule return corresponding rule, else NULL
   */
  function getRule($id)
  {
    foreach ($this->rules as $rule) if ($rule->id == $id) return $rule;
    return NULL;
  }

  /**
   * Find Rule object into array by matching all members of both objects.
   * @param Rule $r Rule to match in array.
   * @return Rule corresponding rule if found, NULL otherwise.
   */
  function getRuleByObj($r)
  { 
    foreach ($this->rules as $rule)
    {
       if (($rule->type == $r->type) &&
           ($rule->if == $r->if) &&
           ($rule->protocol == $r->protocol) &&
           ($rule->snot == $r->snot) &&
           ($rule->source == $r->source) &&
           ($rule->sport == $r->sport) &&
           ($rule->dnot == $r->dnot) &&
           ($rule->destination == $r->destination) &&
           ($rule->dport == $r->dport) &&
           ($rule->icmptype == $r->icmptype) &&
           ($rule->frags == $r->frags) &&
           ($rule->log == $r->log))
          return $rule;
    }
    return 0;
  }

  /**
   * Check if Rule object already exists into rule's array.
   * @param Rule $r Rule object to match.
   * @return int one if matched, zero otherwise
   */
  function ruleExist($r)
  { 
    foreach ($this->rules as $rule)
    {
       if (($rule->type == $r->type) &&
           ($rule->if == $r->if) &&
           ($rule->protocol == $r->protocol) &&
           ($rule->snot == $r->snot) &&
           ($rule->source == $r->source) &&
           ($rule->sport == $r->sport) &&
           ($rule->dnot == $r->dnot) &&
           ($rule->destination == $r->destination) &&
           ($rule->dport == $r->dport) &&
           ($rule->icmptype == $r->icmptype) &&
           ($rule->frags == $r->frags) &&
           ($rule->log == $r->log))
          return 1;
    }
    return 0;
  }

  
  /**
   * Fetch Global Aliases from the database.
   * @return int one if successful, zero otherwise
   */
  function fetchGAliases()
  {
    $index = "`id`";
    $table = "galias";
    $where = "";
    $m = mysql::getInstance();

    if (($idx = $m->fetchIndex($index, $table, $where)))
    { 
      foreach($idx as $t) {
        $ga = new GAlias($t["id"]);
        array_push($this->galiases, $ga);
      }
      return 1;
    }
    return 0;
  }

  /**
   * Fetch Global Aliases details.
   * @return int one if successful, zero otherwise
   */
  function fetchGAliasesDetails()
  {
     $ret = 1;
     foreach ($this->galiases as $ga) {
       if (!$ga->fetchFromId()) $ret = 0;
     }
     return $ret;
  }

  /**
   * Fetch Backup Users from database
   * @return int one if successful, zero otherwise
   */
  function fetchBusers()
  {
    $index = "`id`";
    $table = "busers";
    $where = "";
    $m = Mysql::getinstance();

    if (($idx = $m->fetchIndex($index, $table, $where)))
    { 
      foreach($idx as $t) {
        $bu = new Buser($t["id"]);
        $bu->fetchFromId();
        array_push($this->busers, $bu);
      }
      return 1;
    }
    return 0;
  }

  /**
   * Fetch Id of m0n0wall from database
   * @return int one if successful, zero otherwise
   */
  function fetchMonoId()
  {
    $index = "`id`";
    $table = "hosts";
    $where = "";
    $m = mysql::getinstance();

    if (($idx = $m->fetchIndex($index, $table, $where)))
    { 
      foreach($idx as $t) {
        $m = new Monowall($t["id"]);
        array_push($this->monowall, $m);
      }
      return 1;
    }
    return 0;
  }

  /**
   * Fetch m0n0wall's groups from database.
   * @return int one if successful, zero otherwise
   */
  function fetchMonoGroups()
  {
    $ret = 1;
    foreach ($this->monowall as $mono) {
      if (!$mono->fetchGroups()) $ret = 0;
    }
    return $ret;
  }

  /**
   * Fetch m0n0wall's users from database
   * @return int one if successful, zero otherwise
   */
  function fetchMonoUsers()
  {
    $ret = 1;
    foreach ($this->monowall as $mono) {
      if (!$mono->fetchUsers())
        $ret = 0;
    }
    return $ret;
  }

  /**
   * Fetch config from all m0n0wall device
   * @return int one if successful, zero otherwise
   */
  function fetchAllConfig()
  {
    $ret = 1;
    foreach ($this->monowall as $mono) {
      if (!$mono->config->fetchConfig())
        $ret = 0;
    }
    return $ret;
  }

  /**
   * Fetch All NAT rules from database.
   * @return int one if successful, zero otherwise
   */
  function fetchAllNat()
  {
    $ret = 1;
    foreach ($this->monowall as $mono) {
      if (!$mono->fetchAllNat())
        $ret = 0;
    }
    return $ret;
  }

  /**
   * Fetch NAT rules details from database
   * @return int one if successful, zero otherwise
   */
  function fetchAllNatDetails()
  {
    $ret = 1;
    foreach ($this->monowall as $mono) {
      if (!$mono->fetchAllNatDetails())
        $ret = 0;
    }
    return $ret;
  }

  /**
   * Parse all m0n0wall XML configurations
   * @return int one if successful, zero otherwise
   */
  function parseAllConfig()
  {
    $ret = 1;
    foreach ($this->monowall as $mono) {
      if (!$mono->config->parseConfig())
        $ret = 0;
    }
    return $ret;
  }

  /**
   * Fetch m0n0wall interface from database
   * @return int one if successful, zero otherwise
   */
  function fetchMonoIfaces()
  {
    $ret = 1;
    foreach ($this->monowall as $mono) {
      if (!$mono->fetchIfaces())
        $ret = 0;
    }
    return $ret;
  }

  /**
   * Fetch various m0n0wall's properties from database
   * @return int one if successful, zero otherwise
   */
  function fetchMonoProp()
  {
    $ret = 1;
    foreach ($this->monowall as $mono) {
      if (!$mono->fetchProp()) $ret = 0;
    }
    return $ret;
  }

  /**
   * Fetch m0n0wall interfaces details from database
   * @return int one if successful, zero otherwise
   */
  function fetchMonoIfacesDetails()
  {
    $ret = 1;
    foreach ($this->monowall as $mono) {
      if(!$mono->fetchIfacesDetails()) $ret = 0;
    }
    return $ret;
  }

  /**
   * Fetch m0n0wall's static routes from database
   * @return int one if successful, zero otherwise
   */
  function fetchMonoRoutes()
  {
    $ret = 1;
    foreach ($this->monowall as $mono) {
      if(!$mono->fetchRoutes()) $ret = 0;
    }
    return $ret;
  }

  /**
   * Fetch m0n0wall's static routes details from database
   * @return int one if successful, zero otherwise
   */
  function fetchMonoRoutesDetails()
  {
    $ret = 1;
    foreach ($this->monowall as $mono) {
      if (!$mono->fetchRoutesDetails()) $ret = 0;
    }
    return $ret;
  }

  /**
   * Fetch m0n0wall's VLANs from database
   * @return int one if successful, zero otherwise
   */
  function fetchMonoVlans()
  {
    $ret = 1;
    foreach ($this->monowall as $mono) {
      if (!$mono->fetchVlans()) $ret = 0;
    }
    return $ret;
  }

  /**
   * Fetch m0n0wall's VLANs details from database
   * @return int one if successful, zero otherwise
   */
  function fetchMonoVlansDetails()
  {
    $ret = 1;
    foreach ($this->monowall as $mono) {
      if (!$mono->fetchVlansDetails()) $ret = 0;
    }
    return $ret;
  }

  /**
   * Fetch m0n0wall's aliases from database
   * @return int one if successful, zero otherwise
   */
  function fetchMonoAlias()
  {
    $ret = 1;
    foreach ($this->monowall as $mono) {
      if (!$mono->fetchAlias())
        $ret = 0;
    }
    return $ret;
  }

  /**
   * Fetch m0n0wall's aliases details from database
   * @return int one if successful, zero otherwise
   */
  function fetchMonoAliasDetails()
  {
    $ret = 1;
    foreach ($this->monowall as $mono) {
      if (!$mono->fetchAliasDetails()) $ret = 0;
    }
    return $ret;
  }

  /**
   * Fetch m0n0wall's ProxyARP from database
   * @return int one if successful, zero otherwise
   */
  function fetchMonoProxyarp()
  {
    $ret = 1;
    foreach ($this->monowall as $mono) {
      if(!$mono->fetchProxyarp()) $ret = 0;
    }
    return $ret;
  }

  /**
   * Fetch m0n0wall's ProxyArp details from database
   * @return int one if successful, zero otherwise
   */
  function fetchMonoProxyarpDetails()
  {
    $ret = 1;
    foreach ($this->monowall as $mono) {
      if(!$mono->fetchProxyarpDetails()) $ret = 0;
    }
    return $ret;
  }

  /**
   * Fetch all m0n0wall details from database
   * @return int one if successful, zero otherwise
   */
  function fetchMonoDetails()
  {
    $ret = 1;
    foreach ($this->monowall as $mono) {
      if(!$mono->fetchFromId()) $ret = 0;
    }
    return $ret;
  }

  /**
   * Fetch Syslog settings of all m0n0wall devices from database
   * @return int one if successful, zero otherwise
   */
  function fetchMonoSyslog()
  {
    $ret = 1;
    foreach ($this->monowall as $mono) {
      if(!$mono->fetchSyslog()) $ret = 0;
    }
    return $ret;
  }

  /**
   * Fetch SNMP settings of all m0n0wall devices from database
   * @return int one if successful, zero otherwise
   */
  function fetchMonoSnmp()
  {
    $ret = 1;
    foreach ($this->monowall as $mono) {
      if (!$mono->fetchSnmp()) $ret = 0;
    }
    return $ret;
  }

  /**
   * Fetch RulesInt object from database for all monowall devices
   * @return int one if successful, zero otherwise
   */
  function fetchRuleInt()
  {
    $ret = 1;
    foreach ($this->monowall as $m) {
      if (!$m->fetchRules()) $ret = 0;  
    }
    return $ret;
  }

  /**
   * Remove rules of one specific interface from rulesint set.
   * @param int $idif ID of interface to remove from set.
   * @return int one if successful, zero otherwise
   */
  function removeRuleIntIf($idif)
  {
    $newruleint = array();
    foreach ($this->ruleint as $ri)
    {
      if ($ri->idint != $ri->idint) array_push($newruleint, $ri);
    }
    $this->ruleint = $newruleint;
    return 1;
  }

  /* mysql vs rules objects */

  /**
   * Fetch list of all firewall rules from database
   * @return int one if successful, zero otherwise
   */
  function fetchRulesId()
  {
    $index = "`id`";
    $table = "rules";
    $where = "";
    $m = mysql::getinstance();

    if (($idx = $m->fetchIndex($index, $table, $where)))
    { 
      foreach($idx as $t) {
        array_push($this->rules, new Rule($t["id"]));
      }
      return 1;
    }
    return 0;
  }

  /**
   * Fetch all rules details from database
   * @return int one if successful, zero otherwise
   */
  function fetchRulesDetails()
  {
    $ret = 1;
    foreach($this->rules as $rule) {
      if(!$rule->fetchFromId()) $ret = 0;
    }
    return $ret;
  }

  /**
   * Count m0n0wall devices in memory.
   * @return number of m0n0wall devices loaded.
   */
  function monoCount()
  {
    return count($this->monowall);
  }

}


?>
