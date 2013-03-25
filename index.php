<?php
/*

	Author: Jay Engineer
	Home Page: https://github.com/jay754/php-last.fm
	Script: php-last.fm 
	Returns the top 10 songs from the last.fm site (can easily changed to your liking just change the limit can only go up to 50)

	license: The BSD 3-Clause License
*/

	class lastfm {
		private $_method; //method
		private $_APIkey; //apikey
		private $_format; //json or xml
		
		/**
			
			@paras - username, key
		**/
		
		public function __construct($method, $key, $format = NULL){
			$this -> _method = $method;
			$this -> _APIkey = $key;
			$this -> _format = $format;
		}

		/**
			HTTPstatus Method
			
			@paras - url
			Returns the http status of the website
		**/
		
		protected function HTTPstatus($url) {
			$headers = get_headers($url);
			return substr($headers[0], 9, 3); //returns http status
		}
		
		/**
			getInfo Method
			
			this was the worst method I had ever dealt with
			@paras - None
			Returns the top 10 songs from last fm site
		**/
		public function getInfo(){
			$key = $this -> _APIkey; //apikey
			$method = $this -> _method; //method for getting the songs
			$limit = "10"; //limit per page
			$http_status = $this->HTTPstatus("http://ws.audioscrobbler.com/2.0/?method=".$method."&api_key=".$key."&limit=".$limit); //http status
			
			if ($http_status == "200"){
				$data = file_get_contents("http://ws.audioscrobbler.com/2.0/?method=".$method."&api_key=".$key."&limit=".$limit);
				$xml = simplexml_load_string($data);
				$tracks = array(); //return $xml->tracks->track->{5};
				
				for($i=0;$i<intval($limit);$i++){
					foreach($xml->tracks->track->{$i}->artist as $x){
						$tracks[$i]["Artist"] = (string)$x->name; //append artists
						$tracks[$i]["Url"] = (string)$x->url; //append url
					}
				}
			
				for($i=0;$i<intval($limit);$i++){
					foreach($xml->tracks->track->{$i} as $x){
						$tracks[$i]["Picture"] = (string)$x; //append artists
					}
				}
			
				for($i=0;$i<intval($limit);$i++){
					foreach($xml->tracks->track->{$i}->name as $x){
						$tracks[$i]["Song"] = (string)$x; //song for some reason this was a true pain in the ass (just wouldn't append on the other for loop that's why had to to do 2 different for loops) 
					}
				}
				
				for($i=0;$i<intval($limit);$i++){
					foreach($xml->tracks->track->{$i}->duration as $x){
						$tracks[$i]["Duration"] = (string)$x; //duration
					}
				}
				
				return $tracks; //returns 
			}
			
			else {
				print $http_status." error";
			}
		
		}
		
		public function convert(){
			return;
		}

	} //end class

	function styling(){
		$lastFmObj = new lastfm('chart.getTopTracks','6ac5f948a3bf4a3ecbaa57b391820c78'); //the object
		$info = $lastFmObj->getInfo();
		$results = array();
		
		for($i=0;$i<sizeof($info);$i++){
			$div = "<div id='wrap' style='margin:20px auto;width:300px;border:1px solid white;padding:10px;background:#fff;border-radius:5px;'>
					<h2>Artist</h1><p>".$info[$i]['Artist']."</p>".
					"<img src='".$info[$i]['Picture']."' width='150' height='150'/>".
					"<h4>Song</h4><p>".$info[$i]['Song']."</p>".
					"<h4>Link</h4><a href='".$info[$i]['Url']."'> Link </a>
					</div>";
			
			$results[] = $div;
		}
		return $results;
	}

	foreach(styling() as $k)
		print $k;
//end script	
?> 