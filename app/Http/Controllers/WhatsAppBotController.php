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

        // ZAAD sub-menu
        $zaadSubMenu = [
            "1. New ZAAD Account (Information)",
            "2. Merchant (Information)",
            "3. Wrong ZAAD Transfer Support",
            "4. Last ZAAD Transactions",
            "0. Go Back",
        ];

        // Merchant sub-menu
        $merchantSubMenu = [
            "1. New Merchant Account",
            "2. Document Requirements",
            "3. Support Information",
            "0. Go Back",
        ];

        // Internet sub-menu
        $internetSubMenu = [
            "1. New Fiber Service",
            "2. Internet Billing",
            "3. Troubleshooting",
            "0. Go Back",
        ];

        // Troubleshooting sub-menu
        $troubleshootingSubMenu = [
            "1. Report an Issue",
            "2. FAQs",
            "3. Live Support",
            "0. Go Back",
        ];

        // Sim-card sub-menu
        $simCardSubMenu = [
            "1. Mushaax",
            "2. Ping/buk",
            "3. Telesom Services",
            "0. Go Back",
        ];

        // Self-support sub-menu
        $selfSupportSubMenu = [
            "1. Account Management",
            "2. Billing Support",
            "3. Service Feedback",
            "0. Go Back",
        ];
        $cites=[
            "1. Hargeisa", 
            "2. Burco",
            "3. Berbera",
            "4. Boorama",
            "5. Wajaale",
            "6. Buuhoodle",
            "7. Gabileys",
            "8. Laascaanood",
        ];

        $newbundle=[
            "5MB  $20 Monthly" => "value 20",
            "7MB $30 Monthly",
            "15MB $50 Monthly",
            "20MB $80 Monthly",
            "35MB $150 Monthly",
            "More than the above speed",
        ];

        // Default greeting and main menu
        if (in_array($userMessage, ['hi', 'hello', 'morning', 'good morning', 'asc'])) {
            // Personalize greeting
            $responseMessage = "Good MORNING, Khalid! Please choose what we can help with today:<br>";
            $responseMessage .= implode("<br>", $mainMenu);
            // Reset the menu state
            $_SESSION['menu_state'] = 'main';

        } 
        
        // Handle main menu selections
          // Handle main menu selections
        elseif ($_SESSION['menu_state'] === 'main') {
            if ($userMessage === '1') {
                $responseMessage = "You have chosen ZAAD services. Please select an option:<br>" . implode("<br>", $zaadSubMenu);
                $_SESSION['menu_state'] = 'zaad';
            } 
            elseif ($userMessage === '2') {
                $responseMessage = "You have chosen Internet services. Please select an option:<br>" . implode("<br>", $internetSubMenu);
                $_SESSION['menu_state'] = 'internet';
            } 
            elseif ($userMessage === '3') {
                $responseMessage = "You have chosen Troubleshooting services. Please select an option:<br>" . implode("<br>", $troubleshootingSubMenu);
                $_SESSION['menu_state'] = 'troubleshooting';
            }
            elseif ($userMessage === '4') {
                $responseMessage = "You have chosen Sim Card services. Please select an option:<br>" . implode("<br>", $simCardSubMenu);
                $_SESSION['menu_state'] = 'sim_card';
            }
            elseif ($userMessage === '5') {
                $responseMessage = "You have chosen Self-support services. Please select an option:<br>" . implode("<br>", $selfSupportSubMenu);
                $_SESSION['menu_state'] = 'self_support';
            }
            elseif ($userMessage === '6') {
                $responseMessage = "Connecting you with an agent. Please hold on...";
                $_SESSION['menu_state'] = 'main'; // Reset to main menu
            }
            else {
                $responseMessage = "Sorry, I didn’t understand that. Please type 'hi' or 'hello' to start again.";
            }
        } 

        // Handle sub-menu options based on the current state
        elseif ($_SESSION['menu_state'] === 'zaad') {
            if ($userMessage === '1') {
                $responseMessage = "To create a new ZAAD account, please provide your details.";
            } elseif ($userMessage === '2') {
                $responseMessage = "For Merchant information, please visit our website.";
            } elseif ($userMessage === '3') {
                $responseMessage = "For wrong ZAAD transfer support, please provide the transaction details.";
            } elseif ($userMessage === '4') {
                $responseMessage = "To check your last ZAAD transactions, please provide your account number.";
            } elseif ($userMessage === '0') {
                $responseMessage = "Going back to the main menu...";
                $_SESSION['menu_state'] = 'main';
                $responseMessage .= "<br>" . implode("<br>", $mainMenu);
            } else {
                $responseMessage = "Invalid option. Please select from the ZAAD submenu:<br>" . implode("<br>", $zaadSubMenu);
            }
        } elseif ($_SESSION['menu_state'] === 'internet') {
            // Handle Internet sub-menu options
            if ($userMessage === '1') {
                $responseMessage = "To order a new fiber service, please provide your address:<br>" . implode("<br>", $cites);
                

                

            } elseif ($userMessage === '2') {
                $responseMessage = "For internet billing inquiries, please provide your account number.";
            } elseif ($userMessage === '3') {
                $responseMessage = "For troubleshooting, please describe the issue.";
            } elseif ($userMessage === '0') {
                $responseMessage = "Going back to the main menu...";
                $_SESSION['menu_state'] = 'main';
                $responseMessage .= "<br>" . implode("<br>", $mainMenu);
            } else {
                $responseMessage = "Invalid option. Please select from the Internet submenu:<br>" . implode("<br>", $internetSubMenu);
            }
        }  
        elseif ($_SESSION['menu_state'] === 'troubleshooting') {
            // Handle Troubleshooting sub-menu options
            if ($userMessage === '1') {
                $responseMessage = "Please describe the issue you are facing.";
            } elseif ($userMessage === '2') {
                $responseMessage = "Here are some FAQs: [FAQ List].";
            } elseif ($userMessage === '3') {
                $responseMessage = "Connecting you to live support...";
            } elseif ($userMessage === '0') {
                $responseMessage = "Going back to the main menu...";
                $_SESSION['menu_state'] = 'main';
                $responseMessage .= "<br>" . implode("<br>", $mainMenu);
            } else {
                $responseMessage = "Invalid option. Please select from the Troubleshooting submenu:<br>" . implode("<br>", $troubleshootingSubMenu);
            }
        } elseif ($_SESSION['menu_state'] === 'sim_card') {
            // Handle Sim-card sub-menu options
            if ($userMessage === '1') {
                $responseMessage = "To get a new Sim Card, please visit the nearest Telesom branch.";
            } elseif ($userMessage === '2') {
                if (!isset($_SESSION['ping_buk_number'])) {
                    $responseMessage = "Please enter your phone number for Ping/Buk:";
                    $_SESSION['menu_state'] = 'ping_buk_number_entry'; // Set a new state to handle number entry
                }
            } elseif ($userMessage === '3') {
                $responseMessage = "To replace your Sim Card, please visit the nearest branch and provide your ID.";
            } elseif ($userMessage === '0') {
                $responseMessage = "Going back to the main menu...";
                $_SESSION['menu_state'] = 'main';
                $responseMessage .= "<br>" . implode("<br>", $mainMenu);
            } else {
                $responseMessage = "Invalid option. Please select from the Sim-card submenu:<br>" . implode("<br>", $simCardSubMenu);
            }
        } elseif ($_SESSION['menu_state'] === 'self_support') {
            // Handle Self-support sub-menu options
            if ($userMessage === '1') {
                $responseMessage = "To manage your account, please log in to your online account or call customer support.";
            } elseif ($userMessage === '2') {
                $responseMessage = "For billing support, please provide your account number.";
            } elseif ($userMessage === '3') {
                $responseMessage = "We appreciate your feedback. Please let us know your thoughts.";
            } elseif ($userMessage === '0') {
                $responseMessage = "Going back to the main menu...";
                $_SESSION['menu_state'] = 'main';
                $responseMessage .= "<br>" . implode("<br>", $mainMenu);
            } else {
                $responseMessage = "Invalid option. Please select from the Self-support submenu:<br>" . implode("<br>", $selfSupportSubMenu);
            }
        }

         // Handle Ping/Buk number entry
         if ($_SESSION['menu_state'] === 'ping_buk_number_entry') {
            // Validate the phone number (basic validation, you can make this more complex)
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
                $responseMessage = "Please enter a valid 7-digit number:";
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
        
        // Log the raw API response for debugging
        \Log::info('API Response: ', ['response' => $response]);

        // Check the status in the API response
        if ($decodedResponse && isset($decodedResponse['status'])) {
            if ($decodedResponse['status'] === '1') { // Update to check for "1" as a success indicator
                return [
                    'status' => 'success',
                    'data' => $decodedResponse['Data'] // Now return the data array
                ];
            } else {
                return ['status' => 'error', 'message' => $decodedResponse['Message'] ?? 'An unknown error occurred.'];
            }
        } else {
            return ['status' => 'error', 'message' => 'Invalid response structure.'];
        }
    

        // Return the response to the view
        return back()->with('response', $responseMessage);
    }
}
}
