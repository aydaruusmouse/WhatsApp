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

        // Sub-menus
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

        $troubleshootingSubMenu = [
            "1. Report an Issue",
            "2. FAQs",
            "3. Live Support",
            "0. Go Back",
        ];

        $simCardSubMenu = [
            "1. Mushaax",
            "2. Ping/Buk",
            "3. Telesom Services",
            "0. Go Back",
        ];

        $selfSupportSubMenu = [
            "1. Account Management",
            "2. Billing Support",
            "3. Service Feedback",
            "0. Go Back",
        ];

        $cites = [
            "1. Hargeisa", 
            "2. Burco",
            "3. Berbera",
            "4. Boorama",
            "5. Wajaale",
            "6. Buuhoodle",
            "7. Gabileys",
            "8. Laascaanood",
        ];

        // Default greeting and main menu
        if (in_array($userMessage, ['hi', 'hello', 'morning', 'good morning', 'asc'])) {
            $responseMessage = "Good MORNING, Khalid! Please choose what we can help with today:<br>";
            $responseMessage .= implode("<br>", $mainMenu);
            $_SESSION['menu_state'] = 'main';
        } 
        
        // Handle main menu selections
        elseif ($_SESSION['menu_state'] === 'main') {
            switch ($userMessage) {
                case '1':
                    $responseMessage = "You have chosen ZAAD services. Please select an option:<br>" . implode("<br>", $zaadSubMenu);
                    $_SESSION['menu_state'] = 'zaad';
                    break;
                case '2':
                    $responseMessage = "You have chosen Internet services. Please select an option:<br>" . implode("<br>", $internetSubMenu);
                    $_SESSION['menu_state'] = 'internet';
                    break;
                case '3':
                    $responseMessage = "You have chosen Troubleshooting services. Please select an option:<br>" . implode("<br>", $troubleshootingSubMenu);
                    $_SESSION['menu_state'] = 'troubleshooting';
                    break;
                case '4':
                    $responseMessage = "You have chosen Sim Card services. Please select an option:<br>" . implode("<br>", $simCardSubMenu);
                    $_SESSION['menu_state'] = 'sim_card';
                    break;
                case '5':
                    $responseMessage = "You have chosen Self-support services. Please select an option:<br>" . implode("<br>", $selfSupportSubMenu);
                    $_SESSION['menu_state'] = 'self_support';
                    break;
                case '6':
                    $responseMessage = "Connecting you with an agent. Please hold on...";
                    $_SESSION['menu_state'] = 'main'; // Reset to main menu
                    break;
                default:
                    $responseMessage = "Sorry, I didn’t understand that. Please type 'hi' or 'hello' to start again.";
                    break;
            }
        } 

        // Handle sub-menu options based on the current state
        elseif ($_SESSION['menu_state'] === 'zaad') {
            $responseMessage = $this->handleZaadSubMenu($userMessage);
        } elseif ($_SESSION['menu_state'] === 'internet') {
            $responseMessage = $this->handleInternetSubMenu($userMessage, $cites);
        } elseif ($_SESSION['menu_state'] === 'troubleshooting') {
            $responseMessage = $this->handleTroubleshootingSubMenu($userMessage);
        } elseif ($_SESSION['menu_state'] === 'sim_card') {
            $responseMessage = $this->handleSimCardSubMenu($userMessage);
        } elseif ($_SESSION['menu_state'] === 'self_support') {
            $responseMessage = $this->handleSelfSupportSubMenu($userMessage);
        }

        // Handle Ping/Buk number entry
        if ($_SESSION['menu_state'] === 'ping_buk_number_entry') {
            $responseMessage = $this->handlePingBukNumberEntry($userMessage);
        }

        return back()->with('response', $responseMessage);
    }

    private function handleZaadSubMenu($userMessage)
    {
        switch ($userMessage) {
            case '1':
                return "To create a new ZAAD account, please provide your details.";
            case '2':
                return "For Merchant information, please visit our website.";
            case '3':
                return "For wrong ZAAD transfer support, please provide the transaction details.";
            case '4':
                return "To check your last ZAAD transactions, please provide your account number.";
            case '0':
                $_SESSION['menu_state'] = 'main';
                return "Going back to the main menu...<br>" . implode("<br>", $mainMenu);
            default:
                return "Invalid option. Please select from the ZAAD submenu:<br>" . implode("<br>", $zaadSubMenu);
        }
    }

    private function handleInternetSubMenu($userMessage, $cites)
    {
        switch ($userMessage) {
            case '1':
                return "To order a new fiber service, please provide your address:<br>" . implode("<br>", $cites);
            case '2':
                return "For internet billing inquiries, please provide your account number.";
            case '3':
                return "For troubleshooting, please describe the issue.";
            case '0':
                $_SESSION['menu_state'] = 'main';
                return "Going back to the main menu...<br>" . implode("<br>", $mainMenu);
            default:
                return "Invalid option. Please select from the Internet submenu:<br>" . implode("<br>", $internetSubMenu);
        }
    }

    private function handleTroubleshootingSubMenu($userMessage)
    {
        switch ($userMessage) {
            case '1':
                return "Please describe the issue you are facing.";
            case '2':
                return "Here are some FAQs: [FAQ List].";
            case '3':
                return "Connecting you to live support...";
            case '0':
                $_SESSION['menu_state'] = 'main';
                return "Going back to the main menu...<br>" . implode("<br>", $mainMenu);
            default:
                return "Invalid option. Please select from the Troubleshooting submenu:<br>" . implode("<br>", $troubleshootingSubMenu);
        }
    }

    private function handleSimCardSubMenu($userMessage)
    {
        switch ($userMessage) {
            case '1':
                return "To get a new Sim Card, please visit the nearest Telesom branch.";
            case '2':
                $_SESSION['menu_state'] = 'ping_buk_number_entry'; // Set a new state to handle number entry
                return "Please enter your phone number for Ping/Buk:";
            case '3':
                return "To replace your Sim Card, please visit the nearest branch and provide your ID.";
            case '0':
                $_SESSION['menu_state'] = 'main';
                return "Going back to the main menu...<br>" . implode("<br>", $mainMenu);
            default:
                return "Invalid option. Please select from the Sim-card submenu:<br>" . implode("<br>", $simCardSubMenu);
        }
    }

    private function handleSelfSupportSubMenu($userMessage)
    {
        switch ($userMessage) {
            case '1':
                return "To manage your account, please log in to your online account or call customer support.";
            case '2':
                return "For billing support, please provide your account number.";
            case '3':
                return "We appreciate your feedback. Please let us know your thoughts.";
            case '0':
                $_SESSION['menu_state'] = 'main';
                return "Going back to the main menu...<br>" . implode("<br>", $mainMenu);
            default:
                return "Invalid option. Please select from the Self-support submenu:<br>" . implode("<br>", $selfSupportSubMenu);
        }
    }

    private function handlePingBukNumberEntry($userMessage)
    {
        // Validate the phone number (basic validation, you can make this more complex)
        if (is_numeric($userMessage) && strlen($userMessage) === 9) {
            $_SESSION['ping_buk_number'] = $userMessage; // Store the entered number
            $apiResponse = $this->callPingBukAPI($userMessage);

            // Check the response from the API and build a message
            if ($apiResponse['status'] === 'success') {
                return "Ping/Buk details for number: " . $_SESSION['ping_buk_number'] . "\nDetails: " . $apiResponse['message'];
            } else {
                return "Error: " . $apiResponse['message'];
            }
        } else {
            return "Invalid phone number. Please enter a valid 9-digit phone number:";
        }
    }

    private function callPingBukAPI($phoneNumber)
    {
        // Replace with actual API endpoint and API key if needed
        $apiUrl = "https://api.example.com/pingbuk";
        $apiKey = "your_api_key"; // Replace with your API key
        
        // Initialize a cURL session
        $curl = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $apiKey",
            "Content-Type: application/json",
        ]);

        // Execute the API call
        $response = curl_exec($curl);
        
        // Handle any errors
        if (curl_errno($curl)) {
            return ['status' => 'error', 'message' => 'API request failed: ' . curl_error($curl)];
        }

        // Close the cURL session
        curl_close($curl);

        // Parse the API response
        $decodedResponse = json_decode($response, true);

        // Check if the response is valid
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['status' => 'error', 'message' => 'Invalid JSON response.'];
        }

        // Check the status in the API response
        if (isset($decodedResponse['status'])) {
            // Assuming 'status' is 'success' and there are additional details in the response
            if ($decodedResponse['status'] === 'success') {
                $details = $decodedResponse['details'] ?? 'No additional details available.';
                return [
                    'status' => 'success',
                    'message' => $details,
                ];
            } else {
                return ['status' => 'error', 'message' => $decodedResponse['message'] ?? 'An error occurred.'];
            }
        } else {
            return ['status' => 'error', 'message' => 'Response status not found.'];
        }
    }
}
