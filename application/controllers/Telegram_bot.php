<?php 
require_once("Home.php"); // loading home controller

class Telegram_bot extends Home
{
    public function __construct()
    {

        parent::__construct();
        $this->load->library('telegram');
        $token = "1430885459:AAHqB6DWpX3b2__HeeQr81a5ValwRcN8hto";
        $link ="https://api.telegram.org/bot".$token;
        $server_link= "https://newrajshahi.com/telegram/telegram.php";
        $this->telegram->link = $link;
        $this->telegram->server_link = $server_link;
       
    }


    public function index()
    {
    	
		
        $this->telegram->set_webhook();

        // first you must to get the command to your telegram 
          $this->telegram->load_command();
         $data = $this->telegram->receive_data();
         if(isset($data['message']))
         {
               $this->telegram->msgId = $data['message']['from']['id'];
               $this->telegram->name = $data['message']['from']['first_name'];
               $this->telegram->chatId = $data['message']['chat']['id'];
               $this->telegram->text = $data['message']['text'];
               $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
               $this->telegram->text = preg_replace($regexEmoticons, '', $this->telegram->text);
               $this->telegram->reply_message();
         }
         elseif (isset($data['callback_query'])) {
                 // echo "callback";exit();
                $this->telegram->callback_data = $data['callback_query']['data'];
                $this->telegram->callback_id = $data['callback_query']['id'];
                $this->telegram->callback_from_id = $data['callback_query']['from']['id'];
                
                $this->telegram->callback_reply();
         }
         
        


         

    }

    public function create_command()
    {
        echo "ok";

        $command_array = [

                    [
                        "command" => "xeroneit", 
                        "description" => "About xeroneit"
                    ],
                    [
                        "command" => "myself", 
                        "description" => "About Myself"
                    ],
                    [
                        "command" => "ronok",
                        "description" => "About Ronok"
                    ],
                    [
                         "command" => "miraz",
                        "description" => "About Miraz"
                    ]
            ];
            $command_array = json_encode($command_array);
            $parameters = array(
                    'commands' => $command_array
                );
            echo  $this->telegram->send('setMyCommands', $parameters);
    }

   
}




 ?>