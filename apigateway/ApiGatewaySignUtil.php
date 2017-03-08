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
namespace cn\deepdraw\api\rest\apigateway;

include_once __DIR__.'/../constant/Constants.php';

class ApiGatewaySignUtil
{
	const baseSignHeaders = array("Accept", "Content-MD5", "Content-Type", "Date");
	
	public static function signatureStr($secret, $method, $path, $headers, $querys, $bodys) {
		$str = self::buildSignStr($method, $path, $headers, $querys, $bodys);
		
		return base64_encode(hash_hmac('sha256', $str, $secret, true));
	}
	
	private static function buildSignStr($method, $path, $headers,$querys, $bodys) {
		$sb = self::baseToSign($method, $headers);
		$sb.= self::headersToSign($headers);
		$sb.= self::urlToSign($path, $querys, $bodys);
		
		return $sb;
	}
	
	private static function baseToSign($method, $headers) {
		$sb = "";
		$sb.= strtoupper($method);
		$sb.= "\n";
		if (array_key_exists(self::baseSignHeaders[0], $headers) && null != $headers[self::baseSignHeaders[0]]) {
			$sb.= $headers[self::baseSignHeaders[0]];
		}
		$sb.= "\n";
		if (array_key_exists(self::baseSignHeaders[1], $headers) && null != $headers[self::baseSignHeaders[1]]) {
			$sb.= $headers[self::baseSignHeaders[1]];
		}
		$sb.= "\n";
		if (array_key_exists(self::baseSignHeaders[2], $headers) && null != $headers[self::baseSignHeaders[2]]) {
			$sb.= $headers[self::baseSignHeaders[2]];
		}
		$sb.= "\n";
		if (array_key_exists(self::baseSignHeaders[3], $headers) && null != $headers[self::baseSignHeaders[3]]) {
			$sb.= $headers[self::baseSignHeaders[3]];
		}
		$sb.= "\n";
		
		return $sb;
	}
	
	private static function headersToSign($headers) {
		$sb = "";
		
		if (is_array($headers)) {
			ksort($headers);
			foreach ($headers as $itemKey => $itemValue)
			{
				if(!in_array($itemKey,self::baseSignHeaders)){
					$sb.=$itemKey;
					$sb.=":";
					if (0 < strlen($itemValue)) {
						$sb.=$itemValue;
					}
					$sb.="\n";
				}
			}
		}
		
		return $sb;
	}
	
	private static function urlToSign($path, $querys, $bodys) {
		$sb = "";
		if (0 < strlen($path))
		{
			$sb.=$path;
		}
		$sbParam = "";
		$sortParams = array();
		
		if (is_array($querys)) {
			foreach ($querys as $itemKey => $itemValue) {
				if (0 < strlen($itemKey)) {
					$sortParams[$itemKey] = $itemValue;
				}
			}
		}
		if (is_array($bodys)) {
			foreach ($bodys as $itemKey => $itemValue) {
				if (0 < strlen($itemKey)) {
					$sortParams[$itemKey] = $itemValue;
				}
			}
		}

		ksort($sortParams);

		foreach ($sortParams as $itemKey => $itemValue) {
			if (0 < strlen($itemKey)) {
				if (0 < strlen($sbParam)) {
					$sbParam.="&";
				}
				$sbParam.=$itemKey;
				if (null != $itemValue)
				{
					if (0 < strlen($itemValue)) {
						$sbParam.="=";
						$sbParam.=$itemValue;
					}
				}
			}
		}
		if (0 < strlen($sbParam)) {
			$sb.="?";
			$sb.=$sbParam;
		}
		
		return $sb;
	}
	
	public static function addSignatureHeaders($headers) {
		if (is_array($headers)) {
			unset($headers[self::baseSignHeaders[0]]);
			unset($headers[self::baseSignHeaders[1]]);
			unset($headers[self::baseSignHeaders[2]]);
			unset($headers[self::baseSignHeaders[3]]);
			unset($headers[\cn\deepdraw\api\rest\constant\Constants::X_CA_SIGNATURE]);
			
			ksort($headers);
			$signHeadersStringBuilder = "";
			foreach ($headers as $itemKey => $itemValue)
			{
				if (0 < strlen($signHeadersStringBuilder))
				{
					$signHeadersStringBuilder.= ",";
				}
				$signHeadersStringBuilder.= $itemKey;
			}
			
			return $signHeadersStringBuilder;
		}
	}

}