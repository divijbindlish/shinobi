<?php
	
	//function to set headers depending upon filepath
	function set_headers($filepath){
		if(file_exists($filepath)){
			return implode("\n",["\n","HTTP/1.1 200 OK",
					"Server: lol :D",
					"Content-Type: text/html",
					"Charset: utf-8",
					"Date: ".date('D, d M y H:i:s O'),
					"\n"]);
		}
		else{
			return implode("\n",["\n","HTTP/1.1 404 Not Found",
					"Server: lol :D",
					"Content-Type: text/html",
					"Charset: utf-8",
					"Date: ".date('D, d M y H:i:s O'),
					"\n"]);
		}
	}

	set_time_limit(10);

	//specify address and port on which the server listens
	$address = "127.0.0.1";	
	$port = 1337;

	//create a socket and bind it to localhost
	$sock = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
	socket_bind($sock,$address,$port);
	
	//start listening for connections
	socket_listen($sock);
	
	while(true){

		//accept connections from client
		$client = socket_accept($sock);	
		$msg = "welcome to this basic server...\nto quit, just type quit.\n";
		socket_write($client, $msg);

		//while loop to handle multiple requests from same client
		while(true){

			//read input from client
			$input = socket_read($client,1024);
			$input = trim($input);
			$parts = explode(" ",$input);

			//quit command
			if($input == 'quit'){
				$bye = "goodbye\n";
				socket_write($client, $bye);
				break;
			}

			//header command
			else if($parts[0] == 'HEAD' || $parts[0] == 'head'){
				$filepath = $parts[1];
				if($filepath == '/') $filepath = "/index.html";
				$filepath = ".".$filepath;
				$headers = set_headers($filepath);
				socket_write($client,$headers);
			}

			//get command
			else if($parts[0] == 'GET' || $parts[0] == 'get'){
				$filepath = $parts[1];
				if($filepath == '/') $filepath = "/index.html";
				$filepath = ".".$filepath;
				$headers = set_headers($filepath);
				socket_write($client,$headers);
				if(file_exists($filepath)){
					$content = file_get_contents($filepath);
				}
				else{
					$content = file_get_contents("./notfound.html");
				}
				$content = preg_replace('/\t/','',$content);
				socket_write($client,$content);
			}

			//show response to the client for random string
			else{
				$response = "You typed ".$input."\ncommand not recognized\n";
				socket_write($client, $response);
			}

		}
		
		//close connection with current client
		socket_close($client);

	}

	//close the server
	socket_close($sock);

?>