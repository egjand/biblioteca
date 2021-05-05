<?php

namespace App\Controllers;

use App\Models\RevistaModel;

use CodeIgniter\RESTful\ResourceController;
use phpDocumentor\Reflection\Types\Integer;

class Hemeroteca extends ResourceController
{
    protected $modelName = 'App\Models\RevistaModel';
    protected $format    = 'json';
    public function index()
    {
        $request = \Config\Services::request();
        $method =  $request->getMethod();
        if ($method == "get" or $method == "GET") {
            $revista = new RevistaModel;
            $revistas = $revista->where('deleted', '0')->findAll();
            foreach ($revistas as $i => $value) {

				if (!empty($revistas[$i]["imagen_revista"])) {
					$containing_dir = dirname(__FILE__);
					$path = explode("app", $containing_dir);
					$rutaFoto = $path[0] . 'writable/uploads/img/revistas/' . $revistas[$i]["imagen_revista"];
					$newphrase = str_replace('\\', '/', $rutaFoto);

					if (!file_exists($newphrase)) {
						$revistas[$i]["imagen_revista"] = 'no se ha encontrado la imagen de la revista o periodico';
					}

					$imgData = file_get_contents($newphrase);

					if (!base64_encode($imgData)) {
						$revistas[$i]["imagen_revista"] = 'no se ha podido codificar en base64';
					}
					$encode = base64_encode($imgData);
					$revistas[$i]["imagen_revista"] =  "data:image/jpg;base64," . $encode;
				}
			}

			if (!empty($revistas)) {

				$respuesta = [
					'status' => 200,
					'message' => "los datos han sido cargados",
					'data' => ([
						'count' => count($revistas),
						'data' => $revistas,
					]),
				];
			} else {
				$respuesta = [
					'status' => 404,
					'message' => "Ningun resultado cargado"
				];
			}
			return $this->respond($respuesta, $respuesta['status']);
		}
		$respuesta = [
			'status' => 400,
			'total_results' => 0,
			'mensaje' => "esta peticion debe ser de tipo GET"
		];
		return $this->respond($respuesta, $respuesta['status']);
	}

    public function muestra($id = null)
	{   
		$revista = new RevistaModel();
		$revistas = $revista->where('id', $id)->find();
        var_dump($id);
        return;
		if (!empty($revistas)) {
			$containing_dir = dirname(__FILE__);
			
			$path = explode("app", $containing_dir);
			
			$rutaFoto = $path[0] . 'writable/uploads/img/revistas/' . $revistas[0]['imagen_revista'];
			$type = pathinfo($rutaFoto, PATHINFO_EXTENSION);
			$newphrase = str_replace('\\', '/', $rutaFoto);
			
			$imgData = file_get_contents($newphrase);
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($imgData);
			

			if ($revistas[0]['deleted'] == 1) {

				$respuesta = array(
					'error' => TRUE,
					'mensaje' => 'El registro con ID ' . $id . ' , ah sido borrado'
				);
				return $this->respond($respuesta, 404);

			}
			
			$revistas[0]['imagen_revista'] = $base64;
			$respuesta = array(
				'error' => FALSE,
				'mensaje' => 'consulta cargada correctamente',
				'resultado' => $revistas
			);
			return $this->respond($respuesta, 200);
		}
		$respuesta = array(
			'error' => TRUE,
			'mensaje' => 'El ' . $id . ' , no existe'
		);
		return $this->respond($respuesta, 404);
	}

 }
    

