<?php
/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
namespace cn\deepdraw\api\rest\client;

include_once __DIR__.'/../apigateway/ApiGatewaySignUtil.php';
include_once __DIR__.'/../client/RestResponse.php';
include_once __DIR__.'/../constant/Constants.php';

class Client
{
	
	public static $VERSION = "0.7.0";
	
	public static $Description = "~_^";
	
	private $host = null;
	
	private $appKey = null;
	
	private $appSecret = null;
	
	function  __construct($host, $appKey, $appSecret)
	{
		$this->host = $host;
		$this->appKey = $appKey;
		$this->appSecret = $appSecret;
	}
	
	public function get($apiURL, $querys, $headers) {
		return $this->execute("GET", $apiURL, $querys, $headers, null);
	}
	
	private function execute($method, $apiURL, $querys, $headers, $bodys) {
		date_default_timezone_set('PRC');
		
		if (null == $headers) {
			$headers = array();
		}
		
		if(!in_array("Accept",$headers)){
			$headers["Accept"] = "text/plain";
		}
		
		$headers[\cn\deepdraw\api\rest\constant\Constants::X_CA_TIMESTAMP] = strval(time()*1000);
		$headers[\cn\deepdraw\api\rest\constant\Constants::X_CA_NONCE] = strval($this->uuid());
		$headers[\cn\deepdraw\api\rest\constant\Constants::X_CA_KEY] = $this->appKey;
		$headers[\cn\deepdraw\api\rest\constant\Constants::X_CA_SIGNATURE] = \cn\deepdraw\api\rest\apigateway\ApiGatewaySignUtil::signatureStr($this->appSecret,$method, $apiURL,$headers, $querys, $bodys);
		
		$header_ = array();
		foreach ($headers as $itemKey => $itemValue) {
			array_push($header_, $itemKey.":".$itemValue);
		}
		
		array_push($header_, \cn\deepdraw\api\rest\constant\Constants::X_CA_SIGNATURE_HEADERS.":".\cn\deepdraw\api\rest\apigateway\ApiGatewaySignUtil::addSignatureHeaders($headers));
		
		$url = $this->host.$apiURL;
		
		if (is_array($querys) && 0 < count($querys)) {
			$queryPart = "";
			foreach ($querys as $itemKey => $itemValue) {
				$queryPart .= "&".$itemKey;
				if (0 < strlen($itemValue)) {
					$queryPart .= "=".URLEncode($itemValue);
				}
			}
			$url .= "?".$queryPart;
		}
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header_);
		
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_NOBODY, FALSE);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, FALSE);
		curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);
		//curl_setopt($curl, CURLOPT_TIMEOUT, $readtimeout);
		//curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
		
		$exec_result = curl_exec($curl);
		
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$headers = substr($exec_result, 0, $header_size);
		$body = substr($exec_result, $header_size);
		
		$response = new RestResponse();
		$response->setContent($exec_result);
		$response->setStatusCode(curl_getinfo($curl, CURLINFO_HTTP_CODE));
		$response->setHeaders($headers);
		$response->setBody($body);
		
		curl_close($curl);
		
		return $response;
	}
	
	private function uuid()
	{
		mt_srand((double)microtime()*10000);
		$uuid = strtoupper(md5(uniqid(rand(), true)));
		return $uuid;
	}


}
