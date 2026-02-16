<?php
function getModulosACL($u){
    return [
        'categorias' => [
            'permiso' => $u['perm_categorias'],
            'vista'   => '../views/cats.php'
        ],
        'noticias' => [
            'permiso' => $u['perm_noticias'],
            'vista'   => '../views/contenidos.php'
        ],
        'publicidad' => [
            'permiso' => $u['perm_publicidad'],
            'vista'   => '../views/publicidad.php'
        ],
        'suscripciones' => [
            'permiso' => $u['perm_suscripciones'],
            'vista'   => '../views/suscripciones.php'
        ],
        'usuarios' => [
            'permiso' => $u['perm_usuarios'],
            'vista'   => '../views/usuarios.php'
        ]
    ];

}