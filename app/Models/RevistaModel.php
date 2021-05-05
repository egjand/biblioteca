<?php

namespace App\Models;

use CodeIgniter\Model;

class RevistaModel extends Model
{
    protected $table      = 'hemeroteca';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'titulo', 'tiempo_circulando', 'numero_publicacion',
        'ciudad_impresion','pais_impresion', 'fecha_impresion','imagen_revista'
    ];

    protected $useTimestamps = true;
    protected $createdField  = '';
    protected $updatedField  = '';
    protected $deletedField  = 'deleted';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}