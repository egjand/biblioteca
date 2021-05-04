<?php

namespace App\Models;

use CodeIgniter\Model;

class BooksModel extends Model
{
    protected $table      = 'biblioteca';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'apellido', 'nombre_autor', 'titulo',
        'lugar_impresion','editorial', 'correo_electronico','año_publicacion','imagen_libro'
    ];

    protected $useTimestamps = true;
    protected $createdField  = '';
    protected $updatedField  = 'fecha_consulta';
    protected $deletedField  = 'deleted';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
