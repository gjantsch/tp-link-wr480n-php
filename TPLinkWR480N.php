<?php

/**
 * TP-LINK WR480-N function library.
 *
 * @author Gustavo Jantsch <jantsch@gmail.com>
 * @method getLoginRpm($options = null)
 * @method getWanIpv6CfgRpm($options = null)
 * @method getIPv6StatusRpm($options = null)
 * @method getDiagnosticRpm($options = null)
 * @method getSysRebootRpm($options = null)
 * @method getDdnsAddRpm($options = null)
 * @method getSystemStatisticRpm($options = null)
 * @method getSystemLogRpm($options = null)
 * @method getRestoreDefaultCfgRpm($options = null)
 * @method getDateTimeCfgRpm($options = null)
 * @method getChangeLoginPwdRpm($options = null)
 * @method getBakNRestoreRpm($options = null)
 * @method getSoftwareUpgradeRpm($options = null)
 * @method getSysRouteTableRpm($options = null)
 * @method getStaticRouteTableRpm($options = null)
 * @method getFixMapCfgRpm($options = null)
 * @method getAssignedIpAddrListRpm($options = null)
 * @method getLanDhcpServerRpm($options = null)
 * @method getManageControlRpm($options = null)
 * @method getParentCtrlRpm($options = null)
 * @method getAccessCtrlAccessRulesRpm($options = null)
 * @method getAccessCtrlTimeSchedRpm($options = null)
 * @method getAccessCtrlAccessTargetsRpm($options = null)
 * @method getAccessCtrlHostsListsRpm($options = null)
 * @method getGuestNetWirelessCfgRpm($options = null)
 * @method getWanCfgRpm($options = null)
 * @method getMacCloneCfgRpm($options = null)
 * @method getNetworkCfgRpm($options = null)
 * @method getWlanAdvRpm($options = null)
 * @method getWpsCfgRpm($options = null)
 * @method getWlanStationRpm($options = null)
 * @method getWlanMacFilterRpm($options = null)
 * @method getWlanSecurityRpm($options = null)
 * @method getWlanNetworkRpm($options = null)
 * @method getUpnpCfgRpm($options = null)
 * @method getSpecialAppRpm($options = null)
 * @method getDMZRpm($options = null)
 * @method getVirtualServerRpm($options = null)
 * @method getQoSRuleListRpm($options = null)
 * @method getQoSCfgRpm($options = null)
 * @method getLanArpBindingRpm($options = null)
 * @method getLanArpBindingListRpm($options = null)
 * @method getAdvScrRpm($options = null)
 * @method getLocalManageControlRpm($options = null)
 * @method getBasicSecurityRpm($options = null)
 * @method getStatusRpm($options = null)
 * @method getWzdStartRpm($options = null)
 * @method getLogoutRpm($options = null)
 */
class TPLinkWR480N
{

    private $debug = false;

    /**
     * Router Address
     * @var string
     */
    protected $routerAddress = '192.168.100.1';

    /**
     * Router session id after login.
     *
     * @var string
     */
    protected $sessionId;

    /**
     * List of router pages/actions.
     * @var array
     */
    protected $validMethods = [
        "LoginRpm",
        "WanIpv6CfgRpm",
        "IPv6StatusRpm",
        "DiagnosticRpm",
        "SysRebootRpm",
        "DdnsAddRpm",
        "SystemStatisticRpm",
        "SystemLogRpm",
        "RestoreDefaultCfgRpm",
        "DateTimeCfgRpm",
        "ChangeLoginPwdRpm",
        "BakNRestoreRpm",
        "SoftwareUpgradeRpm",
        "SysRouteTableRpm",
        "StaticRouteTableRpm",
        "FixMapCfgRpm",
        "AssignedIpAddrListRpm",
        "LanDhcpServerRpm",
        "ManageControlRpm",
        "ParentCtrlRpm",
        "AccessCtrlAccessRulesRpm",
        "AccessCtrlTimeSchedRpm",
        "AccessCtrlAccessTargetsRpm",
        "AccessCtrlHostsListsRpm",
        "GuestNetWirelessCfgRpm",
        "WanCfgRpm",
        "MacCloneCfgRpm",
        "NetworkCfgRpm",
        "WlanAdvRpm",
        "WpsCfgRpm",
        "WlanStationRpm",
        "WlanMacFilterRpm",
        "WlanSecurityRpm",
        "WlanNetworkRpm",
        "UpnpCfgRpm",
        "SpecialAppRpm",
        "DMZRpm",
        "VirtualServerRpm",
        "QoSRuleListRpm",
        "QoSCfgRpm",
        "LanArpBindingRpm",
        "LanArpBindingListRpm",
        "AdvScrRpm",
        "LocalManageControlRpm",
        "BasicSecurityRpm",
        "StatusRpm",
        "WzdStartRpm",
        "LogoutRpm"
    ];

    public function __construct($routerAddress = null)
    {
        if (!empty($routerAddress)) {
            $this->routerAddress = $routerAddress;
        }
    }

    /**
     * Get the page address.
     * Basically it finds the $page address within the $content, this is
     * needed cos the router builds session URLs like:
     *    http://192.168.100.1/NKQWQEJBABEIPRFB/userRpm/Index.htm
     * where NKQWQEJBABEIPRFB is the session id.
     *
     * @param $page
     * @param $content
     * @return null
     */
    protected function extractURL($page, $content)
    {

        $matches = [];
        preg_match_all("/href = \"(.*)\";/im", $content, $matches);

        if (isset($matches[1])) {
            foreach ($matches[1] as $m) {
                if (strpos($m, $page)) {
                    return $m;
                }
            }
        }

        return null;
    }

    /**
     * Build a valid request URL from the method name.
     *
     * @param $method
     * @param null $arguments
     * @return string
     */
    protected function buildURL($method, $arguments = null)
    {
        $url = 'http://' . $this->routerAddress .  '/';
        if (!empty($this->sessionId)) {
            $url .= $this->sessionId . '/';
        }
        $url .=  'userRpm/' . $method . '.htm';
        if (is_array($arguments) && !empty($arguments)) {
            $url .= '?' . http_build_query($arguments);
        }

        return $url;
    }

    /**
     * Get the page content.
     *
     * @param $url
     * @param $referer
     * @return mixed
     */
    protected function getPage($url, $referer)
    {

        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
            'Cookie: __lnkrntdmcvrd=-1; Authorization=Basic%20YWRtaW46MjEyMzJmMjk3YTU3YTVhNzQzODk0YTBlNGE4MDFmYzM%3D',
            'DNT: 1',
            'Host: 192.168.100.1',
            'Pragma: no-cache',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_4)' .
            ' AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36'
        ];

        if (!empty($referer)) {
            $headers[] = 'Referer: ' . $referer;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $return = curl_exec($ch);
        curl_close($ch);

        if ($this->debug) {
            echo ">> $url returns " . strlen($return) . " bytes of data." . PHP_EOL;
        }
        return $return;
    }

    /**
     * Remove string delimiters.
     *
     * @param $str
     * @return string
     */
    protected function cleanString($str)
    {
        return trim(trim($str), '"');
    }

    /**
     * Login on router, get session id.
     *
     */
    public function startSession()
    {
        $html = $this->getLoginRpm(['Save' => 'Save']);
        $index_url = $this->extractURL('Index.htm', $html);
        $parts = explode('/', $index_url);
        $this->sessionId = isset($parts[3]) ? $parts[3] : null;

        if ($this->debug) {
            echo ">> Page content: " . PHP_EOL;
            echo $html . PHP_EOL;
            echo ">> Session ID: " . $this->sessionId . PHP_EOL;
        }
    }

    /**
     * Finish session.
     *
     */
    public function endSession()
    {
        // proper logout
        $this->getLogoutRpm();
        // this is needed, don't know why
        $this->getPage('http://' . $this->routerAddress . '/', 'http://' . $this->routerAddress . '/');
        $this->sessionId = null;
    }

    /**
     * Generic router methods
     *
     * @param $name
     * @param $arguments
     * @return mixed|null
     */
    public function __call($name, $arguments)
    {
        $return = null;
        $name = strlen($name) > 3 ? substr($name, 3) : null;
        $arguments = is_array($arguments) && is_array($arguments[0]) ? $arguments[0] : null;
        if (!empty($name) && in_array($name, $this->validMethods)) {
            $url = $this->buildURL($name, $arguments);
            if ($name == 'LoginRpm') {
                $referer = 'http://' . $this->routerAddress . '/';
            } else {
                $referer = $this->buildURL('Menu', null);
            }
            $return = $this->getPage($url, $referer);
        }

        if ($this->debug) {
            echo ">> Calling $name: $url" . PHP_EOL;
            echo ">> Referer: $referer" . PHP_EOL;
        }

        return $return;
    }

    /**
     * Get bandwidth usage.
     *
     * @return array
     */
    public function getDataUsage()
    {

        $stats = $this->getSystemStatisticRpm([
            'interval' => 15,
            'autoRefresh' => 2,
            'sortType' => 3,
            'Num_per_page' => 100,
            'Goto_page' => 1
        ]);

        $stats = explode("\n", $stats);
        $stats_data = [];
        $started = false;
        $total = 0;
        foreach ($stats as $s) {
            if ($started) {
                if (strpos($s, ');') === false) {
                    $line = explode(',', trim($s));
                    $mac_address = $this->cleanString($line[2]);
                    $stats_data[] = [
                        'ip'=> $this->cleanString($line[1]),
                        'mac' => $mac_address,
                        'packets' => $line[3],
                        'mb' => number_format($line[4]/1024/1024, 1, ',', '.'),
                        'name' => ''
                    ];
                    $total += $line[4];
                } else {
                    break;
                }
            }

            $started = $started || strpos($s, 'statList') > 0;
        }

        // complement with newtwork names
        $dhcp = $this->getAssignedIpAddrListRpm();
        $dhcp = explode("\n", $dhcp);
        $started = false;
        foreach ($dhcp as $s) {
            if ($started) {
                if (strpos($s, ');') === false) {
                    $line = explode(',', trim($s));
                    $mac_address = $this->cleanString($line[1]);
                    foreach ($stats_data as &$data) {
                        if ($data['mac'] == $mac_address) {
                            $data['name'] = $this->cleanString($line[0]);
                        }
                    }
                } else {
                    break;
                }
            }

            $started = $started || strpos($s, 'DHCPDynList') > 0;
        }

        return [
            'data' => $stats_data,
            'total' => $total
        ];
    }

    /**
     * Clean up statistics.
     *
     */
    public function resetDataUsage()
    {

        $this->getSystemStatisticRpm([
            'DeleteAll' => 'All',
            'interval' => 60,
            'autoRefresh' => 1,
            'sortType' => 3,
            'Num_per_page' => 100,
            'Goto_page' => 1
        ]);
    }
}
