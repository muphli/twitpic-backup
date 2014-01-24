<?php 
	
	//Prevent error reporting to command line
	error_reporting(0);

	//read arguments, store username
	$argv = $_SERVER['argv'];
	$username = $argv[1];
	$foldername = $argv[2];
	$path = '';
		
	//parse response, count images, calculate pages
	$responseRaw = file_get_contents("http://api.twitpic.com/2/users/show.json?username=" . $username);
	if (!$http_response_header == 'HTTP/1.1 404 Not Found'){
		$imageCounter = $response->photo_only_count;
		$pageCounter = ceil(($imageCounter)/20);
	
		if (isset($username)){
			
			if (isset($foldername)){
			$path = getcwd() . '/' . $foldername;
			}
			else $path = getcwd() . '/' . 'twitpic-backup_' . $username;
				
			//delete old folder if exists
			if (is_dir($path)){
					$filesInDir = glob($path . '/*');
					foreach ($filesInDir as $file){
						unlink($file);
					}
				rmdir($path);
			}
			mkdir($path);
			
			//print total number of images
			echo "\n@" . $username . " uploaded " . $imageCounter. " images to TwitPic.\n";
			echo "\nDownload initiated...\n";
			
			//for all pages...
			for ($i = 1; $i <= $pageCounter; $i++){
			  
		  		//read xml
		  		$response = file_get_contents("http://api.twitpic.com/2/users/show.json?username=" . $username . "&page=" . $i);
		    	$page = json_decode($response);
		    	    
		    	//for all images of one page...
		    	for ($j = 0; $j < count($page->images); $j++){
		    	    	    	
		    		//read short-id and timestamp
		    		$imageID = $page->images[$j]->short_id;
		      		$imageTimestamp = $page->images[$j]->timestamp;
		      		$imageType = $page->images[$j]->type;
		      		
		      		//set url
		      		$imageUrl = "http://twitpic.com/show/full/" . $imageID;
		      		
		      		//set filename
		      		$filename = $imageTimestamp . "." . $imageType;
		      		
		      		//download image
		      		file_put_contents($path . "/" . $filename, file_get_contents($imageUrl));
		      		
		      		//decrement counter
		      		$imageCounter--;
		      		
		      		//print status message for each image
		      		echo "Download of image with ID '" . $imageID . "' successful (" . $imageCounter . " images left).\n";
		    	
		    	}
		    	
		    	if ($imageCounter == 0){
		    	
		    		//print status message if download is complete
			    	echo "Download complete.";
			    	
		    	}
				
			}
		
	  	}
  	
	  	else echo "\nArgument 'username' is missing.\nPlease call this file as follows: php twitpic-backup.php username [destination-folder].";
	
	}
	
	else echo "User '" . $username . "' not found.";  	
?> 
	

  
    
  	
