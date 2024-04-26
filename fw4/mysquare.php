<?php

require dirname(__FILE__) . '/square/autoload.php';

use Square\SquareClient;
use Square\LocationsApi;
use Square\Exceptions\ApiException;
use Square\Http\ApiResponse;
use Square\Models\ListLocationsResponse;
use Square\Environment;
use Square\Models\Money;
use Square\Models\CreatePaymentRequest;
use Square\Models\CreateCustomerRequest;
use Square\Models\UpsertCatalogObjectRequest;
use Square\Models\CatalogObject;
use Square\Models\CatalogObjectType;
use Square\Models\CatalogSubscriptionPlan;
use Square\Models\SubscriptionPhase;
use Square\Models\Address;
use Square\Models\CreateSubscriptionRequest;

class mysquare {
	
	private $testserver;
	private $client;
	
	function __construct($square_access_token,$testserver) {
		$this->testserver = $testserver;
		if ($testserver) {
			$this->client = new SquareClient([
				'accessToken' => $square_access_token,
				'environment' => Environment::SANDBOX,
			]);
		} else {
			$this->client = new SquareClient([
				'accessToken' => $square_access_token,
				'environment' => Environment::PRODUCTION,
			]);
		}
	}
	
	function regist_customer($name,$mail,$address,$locality="Japan",$country="JP"){
		
		if(empty($name) || empty($mail) || empty($address)){
			throw new Exception("You need name and mail and address");
		}
		
		$customers_api = $this->client->getCustomersApi();
		
		$customer = new CreateCustomerRequest();
		$customer->setIdempotencyKey(uniqid());
		$customer->setGivenName($name);
		$customer->setFamilyName("");
		$customer->setEmailAddress($mail);
		$address_c = new Address();
		$address_c->setAddressLine1($address);
		$address_c->setLocality("Japan");
		$address_c->setCountry("JP");
		$customer->setAddress($address_c);

		$result = $customers_api->createCustomer($customer);
		$result_body = json_decode($result->getBody(),true);
		$customer_id = $result_body["customer"]["id"];
		if ($result->isError()) {
			$txt = "";
			foreach($result_body["errors"] as $error){
				$txt .= $error["detail"] . " ";
			}
			throw new Exception($txt);
		}else{
			return $customer_id;
		}
	}
	
	function regist_card($customer_id,$nonce){
		$customers_api = $this->client->getCustomersApi();
		$card = new Square\Models\CreateCustomerCardRequest($nonce);
		$result = $customers_api->createCustomerCard($customer_id,$card);
		$result_body = json_decode($result->getBody(),true);
		$card_id = $result_body["card"]["id"];
		if($card_id == null){
			$txt = "";
			foreach($result_body["errors"] as $error){
				$txt .= $error["detail"] . " ";
			}
			throw new Exception($txt);
		}
		return $card_id;
	}
	
	function payment($customer_id,$card_id,$price,$currency="JPY"){
		$payments_api = $this->client->getPaymentsApi();

		$money = new Money();
		$money->setAmount($price);
		$money->setCurrency($currency);

		$create_payment_request = new CreatePaymentRequest($card_id, uniqid(), $money);
		$create_payment_request->setCustomerId($customer_id);

		$result = $payments_api->createPayment($create_payment_request);
		if ($result->isError()) {
			$result_body = json_decode($result->getBody(),true);
			$txt = "";
			foreach($result_body["errors"] as $error){
				$txt .= $error["detail"] . " ";
			}
			throw new Exception($txt);
		}else{
			return true;
		}
	}
	
}
