Fake IP Address Generator
============================

This is a plugin for Joomla 3.x which overrides the source IP to be an otherwise valid IP address.

It's designed for use when multihoming a website between a Tor Hidden Service and the standard WWW. The idea being that security precautions (such as Admin Tool's Web Application Firewall functionality) do not need to be adjusted in order to prevent a bad actor from easily DoS'ing your Onion.

The IP's generated (by default) fall within the DHCP range 169.254.0.0/16 so whilst they'll be accepted by the protections, there should be no downstream impact.

There's more information available on why this plugin exists at http://projects.bentasker.co.uk/jira_projects/browse/MISC-5.html



Information to pass through
----------------------------

The plugin expects two headers to be present - the first is a trigger (you can require the header to be present and of a specific value if preferred)
The second is a header containing an integer (the plugin is designed to work with the remote port number)

The name of both headers is specified within the plugin parameters (as is the network range to use).

The value from the second will be combined with the current time in order to derive an IP within the specified range and the relevant super globals are then updated so that if an IP ban comes in place, it will only affect that connection (assuming keep-alive is in use).


Caveats
--------

The aim of the plugin is not to make it easy to ban users who are accessing the site through a .onion. It is to ensure that the existing protections do not end up banning whatever IP your Hidden Service traffic appears to originate from (i.e. if you're running the client locally, 127.0.0.1). The ban itself is easily circumvented.


Copyright
----------

Copyright (C) 2015 [B Tasker](https://www.bentasker.co.uk)
Released under GNU GPL V2 - http://www.gnu.org/licenses/gpl-2.0.html