<?php

	function getAllImages($directory){
		//get image names
        
        $allFiles = glob($directory  . '*.*');
        $images = array();
        for($i = 0; $i < sizeof($allFiles); $i++){
            $image = str_replace($directory, '', $allFiles[$i]);
            array_push($images, $image);
        }
		return $images;
	}
?>