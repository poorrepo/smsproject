<?php

    function readSocket($sock){
            $reply = socket_read($sock, 10000, PHP_NORMAL_READ) //Reading the reply from socket
                    or die("error: failed to read from socket\n");

            return $reply;
    }

    function writeSocket($sock, $text){
            socket_write($sock, $text."\n", strlen($text) + 1)  //Writing the text to the socket
                    or die("error: failed to write to socket\n");
    }

    function executeAPICommand2() {
        $PORT = "9990";             //the port on which we are connecting to the "remote" machine
        $HOST = "127.0.0.1";        //the ip of the remote machine (in this case it's the same machine)

        $sock = socket_create(AF_INET, SOCK_STREAM, 0)      //Creating a TCP socket
        or die("error: could not create socket\n");

        $succ = socket_connect($sock, $HOST, $PORT)         //Connecting to to server using that socket
        or die("error: could not connect to host\n");

        for($i=0; $i<10; $i++)
        {
                $random=rand(5, 2000);

                $datetime = date_create()->format('Y-m-d H:i:s');

                $pakiet = array 
                (
                        'user'=>"admin",
                        'pass'=>"tajnehaslo",
                        'query'=>"newTask",
                        'number'=>"$random",
                        'message'=>"nowa wiadomosc 123",
                        'send_time'=>"$datetime"
                );
                //print_r($text);

                $text = json_encode($pakiet);

                writeSocket($sock, $text);
                echo readSocket($sock);

                sleep(1);
        }
    }
    
    function executeAPICommand() 
    {
        $PORT = "9990";             //the port on which we are connecting to the "remote" machine
        $HOST = "127.0.0.1";        //the ip of the remote machine (in this case it's the same machine)

        if(!$sock = socket_create(AF_INET, SOCK_STREAM, 0))      //Creating a TCP socket
        {
            return false;
        }
        
        error_reporting(E_ERROR);
        if(!$succ = socket_connect($sock, $HOST, $PORT))         //Connecting to to server using that socket
        {
            return false;
        }

        $pakiet = array 
        (
                'user'=>"admin",
                'pass'=>"tajnehaslo",
                'query'=>"hello"
        );
        //print_r($text);

        $text = json_encode($pakiet);

        writeSocket($sock, $text);
        $wynik=readSocket($sock);
        
        if (strpos($wynik,'hi') !== false) 
        {
            return true;
        }
        else
        {
            return false;
        }

        //sleep(1);
    }
    
    function check_if_server_working()
    {
        return executeAPICommand();
    }
?>