<?php

namespace ns1\apiCheat;

class ApiCalls implements iApiCalls
{
    
    protected $clean_key;
    protected $search_arg;
    protected $zone_hold;
    protected $valid_key;
    public $zone_list;
    public $record_list;
    public $matches_array;
    
    protected function baseCurl($key, $arg)
    {
        $this->clean_key = \filter_var($key, FILTER_SANITIZE_STRING);
        $ch = \curl_init();
        \curl_setopt($ch, \CURLOPT_URL, self::BASEURL . $arg);
        \curl_setopt($ch, \CURLOPT_HTTPHEADER, array("X-NSONE-Key: $this->clean_key"));
        \curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
        $this->body = \json_decode(\curl_exec($ch), true);
        \curl_close($ch);
        return $this->body;
    }
    
    public function keyValidate($key)
    {
        $arg = "zones";
        $body = self::baseCurl($key, $arg);
        if (array_key_exists('message', $body)) {
            return \FALSE;
        } else {
            $this->valid_key = $key;
            $this->zone_list = self::zoneList($body);
        }
    }
    
    private function zoneList($zones_array)
    {
        foreach ($zones_array as $zones) {
            $this->zone_hold[] = $zones['zone'];
        }
        return $this->zone_hold;
    }
 
    public function getRecords($zone) {
        $this->clean_zone = \filter_var($zone, FILTER_SANITIZE_STRING);
        $zone_arg = "zones/$this->clean_zone";
        $this->record_list = self::baseCurl($this->clean_key, $zone_arg);
    }
    
    public function getMatches($answer)
    {
        $this->search_arg = "search?q=$answer&type=answers";
        $record_array = $this->baseCurl($this->valid_key, $this->arg);
        if (count($this->record_array === 0)) {
            $_SESSION['error'][] = "$answer is not associated with any records!";
            exit;
        } else {
            $this->matches_array = $record_array;
        }
    }
}

/*
$chand = curl_init();
curl_setopt($chand, CURLOPT_URL, 'https://api.nsone.net/v1/zones');
curl_setopt($chand, CURLOPT_HTTPHEADER, array("X-NSONE-Key: $key"));
curl_setopt($chand, CURLOPT_RETURNTRANSFER, true);
$a = json_decode(curl_exec($chand), true);
foreach ($a as $zones) {
     $b[] = $zones['zone'];
}

foreach ($b as $fullrec) {
    $chand = curl_init();
    curl_setopt($chand, CURLOPT_URL, 'https://api.nsone.net/v1/zones/'.$fullrec);
    curl_setopt($chand, CURLOPT_HTTPHEADER, array("X-NSONE-Key: $key"));
    curl_setopt($chand, CURLOPT_RETURNTRANSFER, true);
    $c = json_decode(curl_exec($chand), true);
    $list[] = $c;
}

foreach ($list as $d) {
    $zone = $d['zone'];
    foreach ($d['records'] as $e) {
        $records[] = ['domain' => $e['domain'], 'type' => $e['type']];
    }
    $zonez[] = ['zone' => $d['zone'], $records];
}

print_r($zonez);
*/