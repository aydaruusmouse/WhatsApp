<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class WhatsAppBotController extends Controller
{
    protected $sessionData = [];

    public function handleIncomingMessage(Request $request)
    {
        $from = $request->input('From'); // Sender's number
        $userMessage = trim($request->input('Body')); // Message content

        \Log::info("Received message from {$from}: {$userMessage}");

        // Initialize session data for the user
        if (!isset($this->sessionData[$from])) {
            $this->sessionData[$from] = ['menu_state' => 'main'];
        }

        // Process the user message based on the current menu state
        $responseMessage = $this->processUserMessage($userMessage, $from);

        \Log::info("Response message for {$from}: {$responseMessage}");

        // Send the response message back to the user
        $this->sendMessage($from, $responseMessage);
        return response()->xml(['Message' => $responseMessage]);
    }

    protected function processUserMessage($userMessage, $from)
    {
        // Define main menu and submenus
        $mainMenu = [
            '1. ZAAD',
            '2. Internet',
            '3. Sim-card',
            '4. Value Added Services',
            '5. Self Support',
            '6. Additional Services',
            '7. Customer Satisfaction',
            '8. Connect with agent',
        ];

        $zaadSubMenu = [
            "1. New ZAAD Account (Information)",
            "2. Merchant (Information)",
            "3. Wrong ZAAD Transfer Support",
            "4. Last ZAAD Transactions",
            "5. Waafi",
            "6. Connect With Agent",
            "0. Go back"
        ];

        // ... (define other submenus similarly)

        // Handle user message based on the current menu state
        $menuState = $this->sessionData[$from]['menu_state'];
        switch ($menuState) {
            case 'main':
                return $this->handleMainMenu($userMessage, $mainMenu, $zaadSubMenu, $from);
            case 'zaad':
                return $this->handleZaadSubMenu($userMessage, $from);
            // Add additional cases for other menus (internet, sim_card, etc.)
            default:
                return "Sorry, I didn’t understand that. Please type 'hi' or 'hello' to start again.";
        }
    }

    protected function handleMainMenu($userMessage, $mainMenu, $zaadSubMenu, $from)
    {
        switch ($userMessage) {
            case '1': // ZAAD
                $this->sessionData[$from]['menu_state'] = 'zaad';
                return "You have chosen ZAAD services. Please select an option:\n" . implode("\n", $zaadSubMenu);
            // Handle other main menu options similarly...
            default:
                return "Please select a valid option:\n" . implode("\n", $mainMenu);
        }
    }

    protected function handleZaadSubMenu($userMessage, $from)
    {
        switch ($userMessage) {
            case '1':
                return "You have chosen to create a new ZAAD account. Please provide your information.";
            case '2':
                return "You have chosen Merchant Information. Please specify your merchant details.";
            case '3':
                return "You have chosen Wrong ZAAD Transfer Support. Please describe your issue.";
            case '4':
                return "You have chosen Last ZAAD Transactions. Fetching your last transactions...";
            case '5':
                return "You have chosen Waafi services. Please provide the details.";
            case '6':
                return "You have chosen to connect with an agent. Please hold on.";
            case '0':
                $this->sessionData[$from]['menu_state'] = 'main'; // Reset to main menu
                return "Going back to the main menu.";
            default:
                return "Invalid option. Please select a valid submenu option.";
        }
    }

    // Add similar functions for other submenus (internet, sim_card, etc.)

    protected function sendMessage($to, $message)
    {
        // Log the number being sent to
        \Log::info("Sending message to: {$to}");

        $twilioSID = 'AC38dcca7bf336dcf27b4027f401338024';
        $twilioToken = 'e8942ed61298b38c3427a2c9df896a15';
        $twilioNumber = 'whatsapp:+14155238886'; 

        $client = new Client($twilioSID, $twilioToken);
        $client->messages->create($to, [
            'from' => $twilioNumber,
            'body' => $message,
        ]);
    }
}
