# TP-LINK WR480-N PHP Library

This class is very simple, is an PHP interface to TP-LINK WR480-N routers,
used to collect the router data such as:

* MAC addresses of the devices connected to network
* Network names
* Bandwidth usage

Everything that you do on the router's admin page, is possible to do with this class.

In order to learn the router's methods and arguments, you need to access your router
with a web browser and inspect the calls. I didn't took the time to reverse engineer
all the setup pages of the router. The requests looks like:

```
http://192.168.100.1/OTUGONPCHDTGDYGB/userRpm/GuestNetWirelessCfgRpm.htm
http://192.168.100.1/OTUGONPCHDTGDYGB/userRpm/AssignedIpAddrListRpm.htm?Refresh=Atualizar

Notice that **OTUGONPCHDTGDYGB** is the session ID, it changes on every login.
```
On the example above, the second URL get the current list of assigned IPs by the router's
DNS server and you do it programatically:

```
$instance = new TPLinkWR480N('192.168.100.1');
$instance->startSession();
print_r($instance->getAssignedIpAddrListRpm(['Refresh' => 'Atualizar']));
$router->endSession();
```

Very simple and usefull although I didn't tested any method to do things such as change the
router setup.


# About login and user/password

Currently there is no support for user/password and the class is set for the default admin/admin
pair. If your router has a different combination of user/password, open the web inspector,
enable the Preserve Log option and login. Search for the cookie on the page headers and replace
the content of following line on the getPage method in the class:

```
'Cookie: __lnkrntdmcvrd=-1; Authorization=Basic%20YWRtaW46MjEyMzJmMjk3YTU3YTVhNzQzODk0YTBlNGE4MDFmYzM%3D'
```


# Available Menu and Actions

The best way to get the actions URLs and parameters is to use the Developer Inspector tool of your browser.

Just for reference, the available options in the MenuRpm.htm are:

* WanIpv6CfgRpm
* IPv6StatusRpm
* DiagnosticRpm
* SysRebootRpm
* DdnsAddRpm
* SystemStatisticRpm
* SystemLogRpm
* RestoreDefaultCfgRpm
* DateTimeCfgRpm
* ChangeLoginPwdRpm
* BakNRestoreRpm
* SoftwareUpgradeRpm
* SysRouteTableRpm
* StaticRouteTableRpm
* FixMapCfgRpm
* AssignedIpAddrListRpm
* LanDhcpServerRpm
* ManageControlRpm
* ParentCtrlRpm
* AccessCtrlAccessRulesRpm
* AccessCtrlTimeSchedRpm
* AccessCtrlAccessTargetsRpm
* AccessCtrlHostsListsRpm
* GuestNetWirelessCfgRpm
* WanCfgRpm
* MacCloneCfgRpm
* NetworkCfgRpm
* WlanAdvRpm
* WpsCfgRpm
* WlanStationRpm
* WlanMacFilterRpm
* WlanSecurityRpm
* WlanNetworkRpm
* UpnpCfgRpm
* SpecialAppRpm
* DMZRpm
* VirtualServerRpm
* QoSRuleListRpm
* QoSCfgRpm
* LanArpBindingRpm
* LanArpBindingListRpm
* AdvScrRpm
* LocalManageControlRpm
* BasicSecurityRpm
* StatusRpm
* WzdStartRpm
* LogoutRpm

