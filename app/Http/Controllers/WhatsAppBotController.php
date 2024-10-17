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

        // Sub-menus defined...
        $zaadSubMenu = [
            "1. New ZAAD Account (Information)",
            "2. Merchant (Information)",
            "3. Wrong ZAAD Transfer Support",
            "4. Last ZAAD Transactions",
            "0. Go Back",
        ];

        // Other sub-menus...

        // Default greeting and main menu
        if (in_array($userMessage, ['hi', 'hello', 'morning', 'good morning', 'asc'])) {
            // Personalize greeting
            $responseMessage = "Good MORNING, Khalid! Please choose what we can help with today:<br>";
            $responseMessage .= implode("<br>", $mainMenu);
            // Reset the menu state
            $_SESSION['menu_state'] = 'main';
        } 
        
        // Handle main menu selections
        elseif ($_SESSION['menu_state'] === 'main') {
            // Handling main menu selections...
        } 
        
        // Handle sub-menu options based on the current state
        elseif ($_SESSION['menu_state'] === 'zaad') {
            // Handling ZAAD sub-menu options...
        } 
        
        // Handle Internet, Troubleshooting, Sim-card, Self-support similarly...

        // Handle Ping/Buk number entry
        if ($_SESSION['menu_state'] === 'ping_buk_number_entry') {
            // Validate the phone number (basic validation)
            if (is_numeric($userMessage) && strlen($userMessage) === 9) {
                $_SESSION['ping_buk_number'] = $userMessage;

                // Call the API
                $apiResponse = $this->callPingBukAPI($_SESSION['ping_buk_number']);
                
                if ($apiResponse['status'] === 'success') {
                    $responseMessage = "Ping/Buk details for number: " . $_SESSION['ping_buk_number'] . "<br>Response: " . $apiResponse['message'];
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
            // Log the cURL error
            \Log::error("cURL Error: " . $err);
            return ['status' => 'error', 'message' => "cURL Error: " . $err];
        } else {
            // Log the raw API response for debugging
            \Log::info('API Response: ', ['response' => $response]);

            // Parse the API response
            $decodedResponse = json_decode($response, true);
            
            // Check if the response is valid
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['status' => 'error', 'message' => 'Invalid JSON response.'];
            }

            // Check the status in the API response
            if (isset($decodedResponse['status'])) {
                // Check for status "1" to indicate success
                if ($decodedResponse['status'] === '1') {
                    return ['status' => 'success', 'message' => $decodedResponse['data']]; // Adjust as necessary
                }
            }

            return ['status' => 'error', 'message' => 'Unknown error occurred.'];
        }
    }
}
