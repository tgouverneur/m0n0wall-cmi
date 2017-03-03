For 1.0:

 Features:
  * Finish developer Documentation;
  * Advanced outbound nat enabled state function is not yet in db;
  * Support the Advanced Setup page.

 Improvements:
  * Code cleaning of all html/* files; -almost done-
  * Code cleaning of lib/config.obj.php (IIRC: need to remove old sort method);
  * Code cleaning of positions.lib.php (Make an usable object with this mess);
  * Split error handling for CURL in separate obj (curl.obj.php maybe)
  * Check showraw in syslog and other settings; (Bill)
  * cosmetics: nat / inbound
  * Use a connect hostname not the same as the monowall hostname; (Bill)
  * show effective description in the interface list;(Bill)

 Wishes:
  * Add uptime column in the list of m0n0;
  * More verbose messages instead of simply "failed";
  * Reimport icon to avoid the need of deleting/import again a m0n0wall when the config
   needs to be taken-back from the device;
  * Make global vars for common settings (syslog, snmp, ...);
  * Check when a rule is added that the alias exists;
  * Error checking in Update HW List function;
  * SNMP: checkboxes instead of 1/0 displayed;
  * keep the order of everything in the xml;

 Bugs:
  * [reminder] Thinking of a possible sql injection on the importxml part. need to look at this.

For 1.1:

 * Blockpriv: some exception in the fw rules input seen in monowall's iface;
 * Batch processing for update of many m0n0walls;
 * GUI Improvement;
