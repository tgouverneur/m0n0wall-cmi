* 09/12/2008 *

 - Adapted proxy arp edition for the new template system
 - Adapted syslog edition for the new template system
 - Adapted SNMP edition for the new template system
 - Adapted vlan edition for the new template system
 - Adapted static routes edition for the new template system

* 08/12/2008 *

 - Adapted device edit for the new template system

* 05/12/2008 *

 - Fixed bug in rules mod.
 - Modified usage of short php tags to improve php compat
 - Some cosmetics in the interface changed. (links, messages, titles, ...)
 - Modified a bit license page and moved it to the new template sys.
 - Adapted some more page to new tpl system:
    * interface list
    * interface removal
    * device removal
    * group edit/add
    * user edit/add
    * save2mono
    * hwupdate
    * nat removal
    * luupdate
 - Some .tpl files have been created to handle forms

* 04/12/2008 *
 
 - Fixed bug in template.lib.php
 - Fixed bug in html.lib.php
 - Moved some pages to the new template interface system:
    * alias management
    * backup users
    * device list
    * statistics
    * import
    * importxml
    * global aliases
    * view page for: syslog, static routes, proxy arp, vlans, users, groups
    * dumpxml
    * static route removal
    * backup user removal
    * group removal
    * ProxyARP removal
    * VLAN removal
    * User removal
    * backup user edition

Total of approx 4000 line of diff

* 15/02/2008 *

 - Fixed bug: dnsallowoverride settings wasn't properly handled;

* 14/02/2008 *

 - Fixed bug: If syslog settings weren't in the m0n0wall, import fail;
 - Fixed bug: If SNMP settings weren't in the m0n0wall, import fail;
 - Fixed bug: Rules reimported that have been edited before weren't correctly
 there.

* 13/02/2008 *

 - Fixed bug: When rules is edited it was moved also;
 - Fixed bug: Rule was duplicated even if it was used by only one mono;
 - Fixed bug: When default port is used, port goes to 0 in CMI instead of 443
 or 80;
 - Fixed bug: Unknown properties were in the Dump XML but not in save 2 m0n0;
 - Improved unknown properties recursive code;
 - Propagated the new template system to alias/global alias list;
 - Propagated the new template system to global alias edition;

* 12/02/2008 *

 - Fixed bug: Auto-add firewall rule/proxy arp shown also when editting.
 - Fixed bug: Moving rules with IE browser.
 - Fixed bug: auto add function of NAT Inbound;

* 11/02/2008 *

 - Fixed bug: User password weren't crypted in the CMI.
 - Fixed some cosmetics (displays, borders, ...).

* 05/02/2008 *

  - Keep unknown XML settings intact and push them back into the XML file when
  saving.

* 01/02/2008 *

  - Fixed: When deleting monowall device, also delete unused rules left;
  - Added Unknown object to store all unknown XML settings;
  - Unkown XML settings are now imported into the database. (still the db->xml
  to do);

* 31/01/2008 *

  - First import into SourceForge CVS;
  - cosmetic: added version number in the GUI;
  - cosmetic: Make a link to the doc in the interface;
  - Now, we insert the base m0n0wall object after we know we can fetch config;
  - Fixed: m0n0wall state doesn't goes red when a fw rule is updated (reported
  by jrenier);
  - Fixed: when a rule is edited with protocol == *, protocol goes to TCP
  instead (reported by jrenier);
  - Import m0n0wall by XML files;
  - Create template to manage all lists (for now, busers are displayed
  throught this template);

* 30/01/2008 *

  - Added template class for future code cleaning of html/* files;
  - Added tpl/ with some test template used with template object

*********************************
****     1.0-RC4 release     ****
*********************************

* 30/01/2008 *

  - First error handling for Import and restore config (to be improved);
  - Fixed bug with staticroutes import (reported by: Bill Arlofski);
  - Some cosmetics fixed. (mostly, no border things seen during debug);
  - Error handling for update Hardware and Update timestamp;
  - Updated sql/ in install dir with correct table create statements;
  - Added UPGRADE file;
  - Fixed bug with Save2Monowall.

*********************************
****     1.0-RC3 release     ****
*********************************

* 28/01/2008 *

  - Cosmetic: Add "import new m0n0wall" link in the main page beside add new m0n0wall.
  - Cosmetic: no border if no description of busers;
  - Fixed bug with staticroutes table missing in installer (reported by: Bill Arlofski);
  - Fixed bug with import of VLANs (reported & fix by: Bill Arlofski);
  - Fixed bug with custom port import and http/https choice.

* 25/01/2008 *

  - Improved settings for interaction with m0n0wall device (port/protocol).

*********************************
****     1.0-RC2 release     ****
*********************************

* 24/01/2008 *

  - Fixed bug with add rule. (reported by: wax78)
  - Fixed bug with CURL detection in installer (reported by: Bill Arlofski)

*********************************
****     1.0-RC1 release     ****
**** FIRST OFFICIAL RELEASE  ****
*********************************

* 23/01/2008 *

  - Removed doc/ dir in public release.
  - Removed m0n0wall.ini phpdoc file in public release.

**** 1.0-alpha2 release ****

* 23/01/2008 *

  - Fixed installation missing table (rules-int);

* 22/01/2008 *

  - Fixed bug with import;
  - Fixed bug with syslog settings;
  - Finished user documentation;
  - Implemented rules for local/global aliases;

* 21/01/2008 *

  - "auto-add firewall rule to permit traffic through this nat rule" Option implemented in NAT tab.
  - "auto-add proxyarp entry to this interface" Option implemented in 1:1 NAT tab.
  - Fixed the syslog->filter and syslog->rawfilter issue.
  - Updated installer with new sql for syslog table;

* 17/01/2008 *

  - Fixed bug with installer;
  - Testing of installation procedure and README, INSTALL file written;
  - Written HTML installation documentation together with screenshots;
  - Implemented the mono->changed field, if a change is made to monowall, timestamp of this field is updated
  - Updated host list to integrate the notion of changes, reflected by a green/red bullet.
  - Link on this bullet to save configuration to monowall device.

**** 1.0-alpha1 release ****

* 17/01/2008 *

  - Created install/ directory;
  - Installation procedure done: automatic installation.
  - Automatic database creation.
  - Automatic config.inc.php population.
  - License change from GPLv3 to BSD.
  - Prepared to be release under 1.0-alpha1 tag.

* 16/01/2008 *

  - Added firmware version into database and filled when using "Update HW List" action;
  - Easy view of firmware version in host list;
  - Added last changed time into database and update function from monowall list;
  - Basic implementation of Save to monowall feature;
  - config.obj.obj, main.obj.php code cleaning (mainly returns codes of functions)


* 15/01/2008 *

  - Added Modification of WAN interfaces;
  - Added Addition of interfaces;
  - Inteface deletion;
  - Make SNMP and Syslog settings comes by default when adding a monowall device;
  - Make interface come by default when adding a monowall device;
  - When deleting a rule, if it's the last iface to use the rule, delete the rule from db;

* 14/01/2008 *
  
  - Reorganization of menu;
  - Added Modification of LAN and OPT interfaces. (Still WAN left);

* 10/01/2008 *
 
  - Added a lot of phpdoc tags;
  - Added configuration for phpdoc;
  - Generated phpdoc into doc/ directory;
  - Make TODO list in documentation accurate;

* 09/01/2008 *

  - Group edition;
  - Group addition;

* 08/01/2008 *

  - Fixed autoloader and propagated change in all files;
  - Splitted some object files to reflect objects names;
  - Propagated user input sanitization into all files.

* 07/01/2008 *

  - Global Alias edition;
  - Global Alias removal;
  - Class autoloader library;

* 04/01/2008 *

  - Removal of users;
  - Removal of groups;
  - Removal of NAT rules;
  - Removal of Aliases;
  - Removal of ProxyARP;
  - Removal of Static Routes;
  - Removal of VLANs;
  - Added global Alias table in SQL schema;
  - Added global Alias object;
  - Added global Alias view in interface;
  - Modified dumpxml function to include Global Aliases.

* 10/12/2007 *

  - Edition/Addition of VLANs objects.
  - Eddition/Addition of Static Routes objects. 
  - Addition of all NAT entries.

* 07/12/2007 *

  - Added HwIface object to store Physical interface details;
  - Added method in monowall object to fetch hardware details;
  - Added hardware update html page to update hardware details;
  - Modified vlan edition forms to match hardware details;

* 29/11/2007 *

  - Added edit/add for ProxyARP.

* 28/11/2007 *

  - Added SQL schema to CVS.
  - Edition of SNMP settings.
  - Edition of Syslog Settings.
  - Edition/Add of Alias.

* 27/11/2007 *

  - Added all forms for object except interface one.
  - Added code structure for edition of all objects.
  - Updated TODO file with bug found and thoughts.

* 22/11/2007 *

  - Added edition of user objects
  - Added Addition of user objects.

* 19/11/2007 *

  - Added Syslog view.
  - Added SNMP view.
  - Added Import m0n0wall function that add m0n0wall and fetch/parse/insert config in db.

* 13/11/2007 *

  - Added monowall choice in viewfw.php.

* 12/11/2007 *
 
  - Finished rule edition.
  - Finished rule deletion;
  - Implemented Users, Groups, Alias, Interface, ProxyARP, Static Routes and VLANs list.
  - Prepared files for edition of all that.

* 08/11/2007 *

  - Modified table for list of monowall/backup user (cosmetic).

* 06/11/2007 *

  - Finished rule edition.
  - Rules move implemented in viewfw.php
  - Manage position when moving rule from one interface to another.

* 05/11/2007 *

  - Fixed minor bug in edition of firewall (use_ip checkbox).
  - Rules edition partially implemented (src/dst left)
  - Added update() and delete() overload in RuleInt class.

* 30/11/2007 *

  - Added firewall view to GUI.
  - Added removal of backup users.
  - Added removal of monowall.
  - Added edit form for rules.

* 29/11/2007 *

  - Added backup user view/add/modify to GUI.
  - Added m0n0wall view/add/modify to GUI.

* 26/11/2007 *

  - First templates for web interface.
  - Directory structure for web interface.

*** 0.2 Released ***

* 24/11/2007 *

  - Fixed bug with duplicates rules.

* 23/11/2007 *

  - Rules Ordering done.

* 17/11/2007 *

  - Added some missing fields in syslog and <system>.
  - Finished usage of mysql lib.
  - Bugfix in rule parsing.
  - Removed all FALSE/TRUE in function's return and replaced with 0/1.
  - Added some missing properties to <filter> section.
  - Ordered the VLAN additions.
  - Added <not/> in the advancednat/destination.
  - Fixed <pages> in <group> section.
  - Added missing dnsserver properties in <system>.
  - Fixed <staticroutes/> instead of <></> when there is no routes at all.
  - Advanced nat enable properties added.
  - Added enabled properties of rules.
  - Added correct position of rules in db (not in XML!).

* 16/11/2007 *

  - Written Mysql and MysqlObj object that will replace the current SQL lib.
  - Written example of usage for mysql lib.
  - Adapted Group, Interface, Alias to use new mysql lib.
  - Adapted Nat to use new mysql lib.
  - Adapted Proxyarp & Prop to use new mysql lib.
  - Adapted rules to use new mysql lib.

*** 0.1 Released ***

* 15/11/2007 *

  - Added syslog object.
  - Added snmp object.
  - Added additionnal properties.
  - Added groups.
  - Added user.
  - Added saveConfig method to save config into a file.
  - Added webgui with hardcoded values.
  - Fixed optX bug in XML export.
  - Fixed bug with enable of optX ifaces.
  - Added some hardcoded non-managed properties (ipsec, wol, ...).

* 10/11/2007 *

  - First code for local->config array conversion.
  - Interface->config array done.
  - Nat->config array done.
  - Rules->config array done.
  - Added Alias object.
  - Added ProxyArp object.

* 10/10/2007 *

  - First Changelog entries.
  - Vlan class added.
  - StaticRoute added.
  - Nat hierarchy of class added.
  - Added code to create NAT objects from config.
  - Added code to read NAT objects from db.

