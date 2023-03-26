<?php
	namespace App\Helpers;
	
	class Tools {		
		public static function upload_file($public_path, $g_file){
			$file = time().'-'.$g_file["file"]["name"]['txtimage']; //Nombre de nuestro archivo

			$url_temp = $g_file['file']['tmp_name']['txtimage']; //Ruta temporal a donde se carga el archivo 

			$url_insert = $public_path; //Carpeta donde subiremos nuestros archivos

			//Ruta donde se guardara el archivo, usamos str_replace para reemplazar los "\" por "/"
			$url_target = str_replace('\\', '/', $url_insert) . '/' . $file;

			//Si la carpeta no existe, la creamos
			if (!file_exists($url_insert)) {
				mkdir($url_insert, 0777, true);
			};

			//movemos el archivo de la carpeta temporal a la carpeta objetivo y verificamos si fue exitoso
			if (move_uploaded_file($url_temp, $url_target)) {
				return "The file " . htmlspecialchars(basename($file)) . " has been uploaded successfully.";
			} else {
				return "There was an error uploading your file.";
			}
		}
		
		
	}