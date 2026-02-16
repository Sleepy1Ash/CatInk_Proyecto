<?php
function tienePermiso($modulo, $accion){
    return $_SESSION['ACL'][$modulo][$accion] ?? false;
}
function esSuperAdmin($u){
    foreach(getModulosACL($u) as $m){
        if($m['permiso'] != 15){
            return false;
        }
    }
    return true;
}
function primerModuloLectura($acl){
    $vistas = [
        'categorias'     => '../views/cats.php',
        'noticias'       => '../views/contenidos.php',
        'publicidad'     => '../views/publicidad.php',
        'suscripciones'  => '../views/suscripciones.php',
        'usuarios'       => '../views/usuarios.php'
    ];
    foreach($acl as $modulo => $permisos){
        if($permisos['leer']){
            return $vistas[$modulo];
        }
    }
    return null;
}
function mapearPermisos($user){
    return [
        'usuarios' => [
            'crear'    => ($user['perm_usuarios'] & 1) === 1,
            'leer'     => ($user['perm_usuarios'] & 2) === 2,
            'editar'   => ($user['perm_usuarios'] & 4) === 4,
            'eliminar' => ($user['perm_usuarios'] & 8) === 8
        ],
        'categorias' => [
            'crear'    => ($user['perm_categorias'] & 1) === 1,
            'leer'     => ($user['perm_categorias'] & 2) === 2,
            'editar'   => ($user['perm_categorias'] & 4) === 4,
            'eliminar' => ($user['perm_categorias'] & 8) === 8
        ],
        'noticias' => [
            'crear'    => ($user['perm_noticias'] & 1) === 1,
            'leer'     => ($user['perm_noticias'] & 2) === 2,
            'editar'   => ($user['perm_noticias'] & 4) === 4,
            'eliminar' => ($user['perm_noticias'] & 8) === 8
        ],
        'publicidad' => [
            'crear'    => ($user['perm_publicidad'] & 1) === 1,
            'leer'     => ($user['perm_publicidad'] & 2) === 2,
            'editar'   => ($user['perm_publicidad'] & 4) === 4,
            'eliminar' => ($user['perm_publicidad'] & 8) === 8
        ],
        'suscripciones' => [
            'crear'    => ($user['perm_suscripciones'] & 1) === 1,
            'leer'     => ($user['perm_suscripciones'] & 2) === 2,
            'editar'   => ($user['perm_suscripciones'] & 4) === 4,
            'eliminar' => ($user['perm_suscripciones'] & 8) === 8
        ]
    ];
}