<?php
    function randomName($sub = '', $length = 10){
        $letters = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","r","s","t","u","v","y","z","x","w","q");
        $numbers = array(0,1,2,3,4,5,6,7,8,9);
        $combine = array_merge($letters,$numbers);

        $res = $sub;

        $res .= $letters[rand(0,count($letters)-1)];

        for($i = 0; $i < $length - 2; $i++){
            $res .= $combine[rand(0,count($combine)-1)];
        }

        return $res;

    }
    
    function compress_png($path_to_png_file, $max_quality = 90)
    {
        if (!file_exists($path_to_png_file)) {
            throw new Exception("File does not exist: $path_to_png_file");
        }
    
        $min_quality = 40;

        $compressed_png_content = shell_exec("pngquant --quality=$min_quality-$max_quality - < ".escapeshellarg(    $path_to_png_file));
    
        if (!$compressed_png_content) {
            throw new Exception("Conversion to compressed PNG failed. Is pngquant 1.8+ installed on the server?");
        }
    
        return $compressed_png_content;
    }

    function optimizeImage($file, $dst='dst', $with_dir=false){
        if($with_dir){
            $directory = $file;         //folder
            $dst = $dst.'/';
            $dir = array_diff(scandir($directory), array('..', '.'));
            $quality = 50;
    
            for($i = 2; $i < count($dir); $i++){
                $filePath = $file.'/'.$dir[$i];
    
                $info = getimagesize($filePath);
                $extension = image_type_to_extension($info[2]);
                $fileName = explode('/',$filePath);
                $fileName = end($fileName);
                $path = str_replace($fileName,'',$filePath);
    
                if($extension == ".png"){
                    $path_to_uncompressed_file = $filePath;
                    $path_to_compressed_file = $dst.'/'.$fileName;
                    file_put_contents($path_to_compressed_file, compress_png($path_to_uncompressed_file,$quality));
                    echo $i.' is <font color="red">png</font> --- ';
                }
                elseif ($extension == ".jpg" || $extension == ".jpeg") {
                    $image = imagecreatefromjpeg($filePath);
                    imagejpeg($image, $dst.'/'.$fileName,$quality);
                    echo $i.' is <font color="green">jpeg</font> --- ';
                }
    
            }
        }
    
        else{
            $filePath = $file['tmp_name'];
            $quality = 50;
    
            $info = getimagesize($filePath);
            $extension = image_type_to_extension($info[2]);
            $fname = explode(".", $file['name']);
            $ext_for_save = ".".end($fname);
            //$fileName = explode('/',$filePath);
            //$fileName = end($fileName);
            $fileName = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file['name']);;
            $path = str_replace($fileName,'',$filePath);
    
            if($extension == ".png"){

                $image = imagecreatefrompng($filePath);
                $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                imagealphablending($bg, TRUE);
                imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                imagedestroy($image);
                imagejpeg($bg, $dst.'/'.$fileName.$ext_for_save, $quality);
                imagedestroy($bg);
                echo "$fileName is png format and compressed";
            }
            elseif ($extension == ".jpg" || $extension == ".jpeg") {
                $image = imagecreatefromjpeg($filePath);
                imagejpeg($image, $dst.'/'.$fileName.$ext_for_save,$quality);
                echo $fileName.$ext_for_save." is jpeg format and compressed";
            }
            else{
                
            }
    
        }
    }

    $file = $_FILES['dosya'];

    optimizeImage($file);

    //echo randomName('emlakodam');

    

    

?>