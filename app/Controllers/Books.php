<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\BooksModel;

class Books extends ResourceController
{
	protected $modelName = 'App\Models\BooksModel';
	protected $format    = 'json';
	public function index()

	{
		$request = \Config\Services::request();
		$method =  $request->getMethod();
		if ($method == "get" or $method == "GET") {

			$book = new BooksModel();
			$books = $book->where('deleted', '0')->findAll();

			foreach ($books as $i => $value) {

				if (!empty($books[$i]["imagen_libro"])) {
					$containing_dir = dirname(__FILE__);
					$path = explode("app", $containing_dir);
					$rutaFoto = $path[0] . 'writable/uploads/img/portadas/' . $books[$i]["imagen_libro"];
					$newphrase = str_replace('\\', '/', $rutaFoto);

					if (!file_exists($newphrase)) {
						$books[$i]["imagen_libro"] = 'no se ha encontrado la imagen del libro';
					}

					$imgData = file_get_contents($newphrase);

					if (!base64_encode($imgData)) {
						$books[$i]["imagen_libro"] = 'no se ha podido codificar en base64';
					}
					$encode = base64_encode($imgData);
					$books[$i]["imagen_libro"] =  "data:image/jpg;base64," . $encode;
				}
			}

			if (!empty($books)) {

				$respuesta = [
					'status' => 200,
					'message' => "los datos han sido cargados",
					'data' => ([
						'count' => count($books),
						'data' => $books,
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


	public function show($id = null)
	{
		$book = new BooksModel();
		$books = $book->where('id', $id)->find();
		if (!empty($books)) {
			
			$containing_dir = dirname(__FILE__);
			
			$path = explode("app", $containing_dir);
			
			$rutaFoto = $path[0] . 'writable/uploads/img/portadas/' . $books[0]['imagen_libro'];
			$type = pathinfo($rutaFoto, PATHINFO_EXTENSION);
			$newphrase = str_replace('\\', '/', $rutaFoto);
			
			$imgData = file_get_contents($newphrase);
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($imgData);
			

			if ($books[0]['deleted'] == 1) {

				$respuesta = array(
					'error' => TRUE,
					'mensaje' => 'El cliente con ID ' . $id . ' , ah sido borrado'
				);
				return $this->respond($respuesta, 404);

			}
			
			$books[0]['imagen_libro'] = $base64;
			$respuesta = array(
				'error' => FALSE,
				'mensaje' => 'consulta cargada correctamente',
				'resultado' => $books
			);
			return $this->respond($respuesta, 200);
		}
		$respuesta = array(
			'error' => TRUE,
			'mensaje' => 'El ' . $id . ' , no existe'
		);
		return $this->respond($respuesta, 404);
	}

	public function create()
	{
		$books = new BooksModel();
		$data = $this->request->getRawInput();
		$img = $data['imgupload'];
		$img  = str_replace('data:image/jpeg;base64,', '', $img);
		$img = str_replace(' ', '+', $img);


		$imgfile = base64_decode($img);
		$nombreImg = 'book' . time() . '.jpg';
		$containing_dir = dirname(__FILE__);
		$path = explode("app", $containing_dir);
		$rutaFoto = $path[0] . 'writable/uploads/img/portadas/' . $nombreImg;
		$modRuta = str_replace('\\', '/', $rutaFoto);
		file_put_contents($modRuta, $imgfile);

		$data['imagen_libro'] = $nombreImg;

		$books->insert($data);
		$respuesta = array(
			'error' => FALSE,
			'mensaje' => 'libro agregado correctamente',
			'resultado' => $data
		);
		return $this->respond($respuesta, 200);
	}






	public function update($id = null)
	{
		$book = new BooksModel();
		$books = $this->model->find($id);
		$data = $this->request->getRawInput();
		$status = [
			"deleted" => "0",
		];
		if (!empty($books)) {

			$respuesta = array(
				'error' => FALSE,
				'mensaje' => 'registro actualizado correctamente',
				'resultado' => $data
			);
			$book->update($id, $data);

			return $this->respond($respuesta, 200);
		} else {

			$respuesta = array(
				'error' => TRUE,
				'mensaje' => 'El Id ' . $id . ', no existe'
			);
			return $this->respond($respuesta, 400);
		}
	}

	public function delete($id = null)
	{

		$book = new BooksModel();
		$books = $this->model->find($id);
		/*  $data = [
            "deleted" => "1",
        ]; */
		if (!empty($books)) {

			$respuesta = array(
				'error' => FALSE,
				'mensaje' => 'usuario eliminado ' . $id . ' correctamente'
			);

			$book->delete($id);

			return $this->respond($respuesta, 200);
		} else {

			$respuesta = array(
				'error' => TRUE,
				'mensaje' => 'El Id ' . $id . ', no existe'
			);
			return $this->respond($respuesta, 404);
		}
	}
}
