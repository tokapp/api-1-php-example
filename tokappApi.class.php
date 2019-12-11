<?php
/**
 * Class helper that simplifies TokApp API petitions.
 * @author TokApp
 *
 */
class tokappApi {
	
	/***********************************************************************************************
	 * PROPERTIES
	 ***********************************************************************************************/
	
	/**
	 * The url in which petitions will be served. 
	 * This value must be given by your service provider.
	 * @var string
	 */
	var $serviceUrl;
	
	/**
	 * Your license API key. Your service provider must generate one for you. 
	 * @var string
	 */
	var $apiKey;
	
	/***********************************************************************************************
	 * CONSTRUCTORS
	 ***********************************************************************************************/
	
	/**
	 * Creates a new class instance.
	 * @param string $serviceUrl	The url in which petitions will be served. 
	 * 								This value must be given by your service provider.
	 * @param string $apiKey		Your license API key. Your service provider must generate one for you. 
	 */
	public function __construct($serviceUrl, $apiKey) {
		$this->serviceUrl = $serviceUrl;
		$this->apiKey = $apiKey;
	}
	
	/***********************************************************************************************
	 * PUBLIC METHODS
	 ***********************************************************************************************/
		
	/**
	 * Gets license and service status.
	 * @return stdClass	Object with data returned by server. 
	 * 					See status command in API documentation for information about fields returned.
	 */
	public function Status() {
		return $this->makePetition("status");
	}
	
	/**
	 * Get contacts username from their phones or emails.
	 * @param array/string $phones	Array or comma separated list with phone numbers.
	 * @param array/string $emails	Array or comma separated list with emails.
	 * @return stdClass 	Server's response as indicated in command documentation.
	 */
	public function GetContacts($phones, $emails) {
		if (!is_array($phones)) $phones = explode(",", $phones);
		if (!is_array($emails)) $emails = explode(",", $emails);
		$data = new stdClass();
		$data->phones = $phones;
		$data->emails = $emails;
		return $this->makePetition("getcontacts", $data);
	}
	
	/**
	 * Send one or more messages.
	 * @param array $messages	Messages array. See send command documentation for more info.
	 * @return stdClass 		Server's response as indicated in command documentation.
	 */
	public function Send($messages) {
		return $this->makePetition("send", $messages);
	}
	
	/**
	 * Retrieves a delivery status.
	 * @param string $id	Delivery identifier (returned by "send" command).
	 * @return stdClass 	Server's response as indicated in command documentation.
	 */
	public function GetDeliveryStatus($id) {
		$data = new stdClass();
		$data->id = $id;
		return $this->makePetition("getdeliverystatus", $data);
	}
	
	/**
	 * Retrieves incoming messages from other users.
	 * @return stdClass 		Server's response as indicated in command documentation.
	 */
	public function GetMessages() {
		return $this->makePetition("getmessages");
	}
	
	/***********************************************************************************************
	 * PRIVATE METHODS
	 ***********************************************************************************************/
	
	/**
	 * Sends specified command to server and returns response.
	 * @param string $command	Command to execute.
	 * @param stdClass/array $data	[Optional] Data to send as specified in command documentation.
	 */
	private function makePetition($command, $data = null) {
		$curl = curl_init();
		
		curl_setopt($curl, CURLOPT_POST, 1);

		$post_data = new stdClass();
		$post_data->key = $this->apiKey;
		$post_data->command = $command;
		if ($data)
			$post_data->data = $data;
		$post_data = json_encode($post_data);
			
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
    		'Content-Type: application/json',                                                                                
    		'Content-Length: ' . strlen($post_data)));
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
				
		curl_setopt($curl, CURLOPT_URL, $this->serviceUrl);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		
		$response = curl_exec($curl);
		if (!$response)
			throw new Exception("Can't connect to service, problems with Internet connection?");
		else
			return json_decode($response);
	}
}