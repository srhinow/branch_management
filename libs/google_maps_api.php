<?php
/**
* get geodata from Google-API
* https://developers.google.com/maps/documentation/localsearch/jsondevguide?hl=de
* @copyright: powered by google
*/
class googleGeoData
{
    private $url = "https://maps.googleapis.com/maps/api/geocode/json?sensor=false&language=de&address=";
    private $server = false;
    public $geoData = null;

    public function __construct($query=false)
    {
         $this->setServer();
         if($query) return $this->getGeoData($query);
    }

    public function setServer($s=false)
    {
		if(!$this->server) $this->server = 'http://'.$_SERVER['HTTP_HOST'];
		if($s) $this->server = $s;
    }

    /**
    * get geo-data for a location-string
    * @var string
    * @return json-string
    */
    public function getGeoData($query)
    {
		$url = $this->url.$query;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		// curl_setopt($ch, CURLOPT_REFERER, $this->server);
		$data = curl_exec($ch);
		curl_close($ch);

		return $data;
    }

}
