<?php
require "tokappApi.class.php";

// Check if post data is given
if (isset($_POST["command"]))
	$command = $_POST["command"];
if (isset($_POST["phones"]))
	$phones = $_POST["phones"];
if (isset($_POST["emails"]))
	$emails = $_POST["emails"];
if (isset($_POST["contacts"]))
	$contacts = $_POST["contacts"];
if (isset($_POST["text"]))
	$text = $_POST["text"];
if (isset($_POST["copies"]))
	$copies = $_POST["copies"];
if (isset($_POST["id"]))
	$id = $_POST["id"];
if (isset($_POST["response"]))
	$response = $_POST["response"] == 1 ? 1: 0;

if (isset($command)) {
	$service = new tokappApi("https://api.tokapp.net/", "<Your API key goes here>");

	//	Makes the petition
	try {
		switch ($command) {
			case "status":
				$response = $service->Status();
				break;
			case "getcontacts":
				$response = $service->GetContacts($phones, $emails);
				break;
			case "send":
				$msgs = array();
				for ($id = 1; $id <= $copies; $id ++) {
					$obj = new stdClass();
					$obj->id = $id;
					$obj->text = utf8_encode($text);
					$obj->response = $response;
					$obj->contacts = explode(",", $contacts);
					$msgs[] = $obj;
				}
				$response = $service->Send($msgs);
				break;
			case "getdeliverystatus":
				$response = $service->GetDeliveryStatus($id);
				break;
			case "getmessages":
				$response = $service->GetMessages();
				break;
		}
	} catch (Exception $ex) {
		echo $command . " command was not performed due to this error: " . $ex->getMessage();
	}
}

//	Shows response
if (isset($response))
	echo "Response from server to command $command<br/><br/><pre>" . print_r($response, true) . "</pre>";
?>
<hr />
<form method="post" action="index.php" enctype="application/x-www-form-urlencoded">
	<input type="hidden" name="command" value="status" />
	<input type="submit" value="Status" />
</form>
<hr />
<p>Input comma separated phones and/or emails</p>
<form method="post" action="index.php" enctype="application/x-www-form-urlencoded">
	<input type="hidden" name="command" value="getcontacts" />
	<label>Phones:
	<input type="text" name="phones" /></label>
	<label>Emails:
	<input type="text" name="emails" /></label>
	<input type="submit" value="getcontacts" />
</form>
<hr />
<form method="post" action="index.php" enctype="application/x-www-form-urlencoded">
	<input type="hidden" name="command" value="send" />
	<label>Contacts (comma separated TokApp usernames):<br/>
	<input type="text" name="contacts" /></label><br />
	<label>Message:<br/>
	<textarea rows="3" cols="20" name="text"></textarea></label><br/>
	<label><input type="checkbox" value="1" name="response" />&nbsp;Response</label><br/>
	<input type="text" name="copies" value="1" />
	<input type="submit" value="send" />
</form>
<hr />
<form method="post" action="index.php" enctype="application/x-www-form-urlencoded">
	<input type="hidden" name="command" value="getmessages" />
	<input type="submit" value="getmessages" />
</form>
<hr />
<form method="post" action="index.php" enctype="application/x-www-form-urlencoded">
	<input type="hidden" name="command" value="getdeliverystatus" />
	<label>Delivery ID:<br />
		<input type="text" name="id" />
	</label>
	<input type="submit" value="getdeliverystatus" />
</form>