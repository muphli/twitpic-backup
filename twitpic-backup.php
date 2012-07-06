<?php 

	/******************************************************************************
	*
	* twitpic-backup - backup all your twitpic-images
	* Copyright (C) 2012 Christian Becker
	*
	* This program is free software; you can redistribute it and/or modify it
	* under the terms of the GNU General Public License as published by the
	* Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* This program is distributed in the hope that it will be useful, but
	* WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
	* or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
	* more details.
	*
	* You should have received a copy of the GNU General Public License along with
	* this program; if not, write to the Free Software Foundation, Inc.,
	* 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
	*
	*****************************************************************************/

	//read arguments, store username
	$argv = $_SERVER['argv'];
	$username = $argv[1];
	$foldername = $argv[2];
	$path = '';
		
	//parse response, count images, calculate pages
	$response = json_decode(file_get_contents("http://api.twitpic.com/2/users/show.json?username=" . $username));
	$imageCounter = $response->photo_count;
	$pageCounter = ceil(($imageCounter)/20);
		
	//create destination-folder
	if (isset($foldername)){
		mkdir($foldername);
		$path = $foldername;
		
	}
	else {
		$path = "twitpic-backup_" . $username;
		mkdir($path);		
	} 
	
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
  	
?> 
	

  
    
  	
