<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WhatsAppBotController extends Controller
{
    public function testMessage(Request $request)
    {
        // Get the message sent by the user
        $userMessage = strtolower(trim($request->input('message')));
        $responseMessage = '';

        // Start the session if not already started
        if (!session_id()) {
            session_start();
        }
        
        // Define main menu and sub-menus
        $mainMenu = [
            '1. ZAAD',
            '2. Internet',
            '3. Troubleshooting',
            '4. Sim-card',
            '5. Self-support',
            '6. Connect with agent',
        ];

        // Sub-menus...
        $zaadSubMenu = [
            "1. New ZAAD Account (Information)",
            "2. Merchant (Information)",
            "3. Wrong ZAAD Transfer Support",
            "4. Last ZAAD Transactions",
            "0. Go Back",
        ];

        $internetSubMenu = [
            "1. New Fiber Service",
            "2. Internet Billing",
            "3. Troubleshooting",
            "0. Go Back",
        ];

        $simCardSubMenu = [
            "1. Mushaax",
            "2. Ping/buk",
            "3. Telesom Services",
            "0. Go Back",
        ];

        // Default greeting and main menu
        if (in_array($userMessage, ['hi', 'hello', 'morning', 'good morning', 'asc'])) {
            $responseMessage = "Good MORNING, Khalid! Please choose what we can help with today:<br>";
            $responseMessage .= implode("<br>", $mainMenu);
            $_SESSION['menu_state'] = 'main';

        } elseif ($_SESSION['menu_state'] === 'main') {
            // Handle main menu selections
            switch ($userMessage) {
                case '1':
                    $responseMessage = "You have chosen ZAAD services. Please select an option:<br>" . implode("<br>", $zaadSubMenu);
                    $_SESSION['menu_state'] = 'zaad';
                    break;
                case '2':
                    $responseMessage = "You have chosen Internet services. Please select an option:<br>" . implode("<br>", $internetSubMenu);
                    $_SESSION['menu_state'] = 'internet';
                    break;
                case '4':
                    $responseMessage = "You have chosen Sim Card services. Please select an option:<br>" . implode("<br>", $simCardSubMenu);
                    $_SESSION['menu_state'] = 'sim_card';
                    break;
                case '6':
                    $responseMessage = "Connecting you with an agent. Please hold on...";
                    $_SESSION['menu_state'] = 'main'; // Reset to main menu
                    break;
                default:
                    $responseMessage = "Sorry, I didn’t understand that. Please type 'hi' or 'hello' to start again.";
            }
        } 

        // Handle Ping/Buk number entry
        if ($_SESSION['menu_state'] === 'sim_card') {
            if ($userMessage === '2') {
                $responseMessage = "Please enter your phone number for Ping/Buk:";
                $_SESSION['menu_state'] = 'ping_buk_number_entry'; // Set a new state to handle number entry
            } elseif ($userMessage === '0') {
                $responseMessage = "Going back to the main menu...";
                $_SESSION['menu_state'] = 'main';
                $responseMessage .= "<br>" . implode("<br>", $mainMenu);
            }
        } 
        
        // Validate Ping/Buk phone number
        if ($_SESSION['menu_state'] === 'ping_buk_number_entry') {
            if (is_numeric($userMessage) && strlen($userMessage) === 9) {
                $_SESSION['ping_buk_number'] = $userMessage;

                // Call the API
                $apiResponse = $this->callPingBukAPI($_SESSION['ping_buk_number']);
                
                if ($apiResponse['status'] === 'success') {
                    $responseMessage = "Ping/Buk details for number: " . $_SESSION['ping_buk_number'] . "\nResponse: " . $apiResponse['message'];
                } else {
                    $responseMessage = "Error: " . $apiResponse['message'];
                }

                // Reset the session state after handling the request
                $_SESSION['menu_state'] = 'sim_card';
                unset($_SESSION['ping_buk_number']); // Remove the stored number
            } else {
                $responseMessage = "Please enter a valid 9-digit number:";
            }
            return back()->with('response', $responseMessage);
        }

        return back()->with('response', $responseMessage);
    }

   private function callPingBukAPI($phoneNumber)
    {
        // Prepare the cURL request to the Ping/Buk API
        $curl = curl_init();
    
        $postData = json_encode([
            "Callsub" => $phoneNumber,
            "UserId" => "imll",
        ]);
    
        curl_setopt_array($curl, [
            CURLOPT_URL => "http://10.55.1.143:8983/api/CRMApi/GetSimDetails",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => [
                "apiTokenUser: CRMUser",
                "apiTokenPwd: ZEWOALJNADSLLAIE321@!",
                "Content-Type: application/json"
            ],
        ]);
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
    
        curl_close($curl);
    
        if ($err) {
            return ['status' => 'error', 'message' => "cURL Error: " . $err];
        } else {
            // Parse the API response (assuming it's JSON)
            $decodedResponse = json_decode($response, true);
            
            if ($decodedResponse && isset($decodedResponse['status']) && $decodedResponse['status'] == '1') {
                // Return the decoded data as well
                return [
                    'status' => 'success',
                    'message' => 'Success',
                    'data' => $decodedResponse['Data'] // Change here to return the Data array
                ];
            } else {
                return ['status' => 'error', 'message' => 'Failed to fetch details.'];
            }
   // Call the API
$apiResponse = $this->callPingBukAPI($_SESSION['ping_buk_number']);

if ($apiResponse['status'] === 'success') {
    // Constructing the response message
    $data = $apiResponse['data']; // Get the Data array from the API response
    $responseDetails = [];

    // Loop through the data to extract relevant fields
    foreach ($data as $item) {
        $responseDetails[] = "IMSI: " . $item['IMSI'] .
                             ", ICCID: " . $item['ICCID'] .
                             ", PIN1: " . $item['PIN1'] .
                             ", PIN2: " . $item['PIN2'] .
                             ", PUK1: " . $item['PUK1'] .
                             ", PUK2: " . $item['PUK2'] .
                             ", Activation Date: " . $item['ActivatioDate'] .
                             ", SIM Type: " . $item['SimType'];
    }

    // Join the details into a single string
    $responseMessage = "Ping/Buk details for number: " . $_SESSION['ping_buk_number'] . "\nResponse: Success\nDetails:\n" . implode("\n", $responseDetails);
} else {
    $responseMessage = "Error: " . $apiResponse['message']; // This will still handle any error responses
}

// Reset the session state after handling the request
$_SESSION['menu_state'] = 'sim_card';
unset($_SESSION['ping_buk_number']); // Remove the stored number

return back()->with('response', $responseMessage);


        }
    }
}
