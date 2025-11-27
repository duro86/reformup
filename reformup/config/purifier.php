<?php

return [

    'encoding'         => 'UTF-8',
    'finalize'         => true,
    'ignoreNonStrings' => false,
    'cachePath'        => storage_path('app/purifier'),
    'cacheFileMode'    => 0755,

    'settings' => [

        /*
         |------------------------------------------------------------------
         | Perfil por defecto
         |------------------------------------------------------------------
         | Si llamas Purifier::clean($html) sin segundo parámetro,
         | usará este perfil.
         */
        'default' => [
            'HTML.Doctype'             => 'HTML 4.01 Transitional',
            // Aquí permitimos un HTML muy básico y seguro
            'HTML.Allowed'             => 'p,br,ul,ol,li,strong,b,em,i',
            'CSS.AllowedProperties'    => '',
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty'   => true,
        ],

        /*
         |------------------------------------------------------------------
         | Perfil para descripciones de solicitudes (CKEditor)
         |------------------------------------------------------------------
         | Editamos los formatos permitidos.
         */
        'solicitud' => [

            'HTML.Allowed' => implode(',', [
                // Texto básico
                'p',
                'br',
                'span',

                // Formato
                'strong',
                'b',
                'em',
                'i',
                'u',
                'blockquote',
                'code',

                // Listas
                'ul',
                'ol',
                'li',

                // Títulos
                'h1',
                'h2',
                'h3',
                'h4',

                // Enlaces seguros
                'a[href|title|target]',

                // Imágenes 
                //'img[src|alt|width|height]',

                // Separadores
                'hr'
            ]),

            'CSS.AllowedProperties' => implode(',', [
                'font-weight',
                'font-style',
                'text-decoration',
                'color',
                'background-color',
                'text-align'
            ]),

            // Limpieza automática
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty'   => true,

            // Importante: bloquear scripts y cosas raras
            'HTML.ForbiddenElements' => ['script', 'style', 'iframe'],
            'HTML.ForbiddenAttributes' => ['onerror', 'onclick', 'onload']
        ],


        /*
         |------------------------------------------------------------------
         | Perfil para opiniones / comentarios de clientes
         |------------------------------------------------------------------
         | Normalmente texto plano con algo de negrita/cursiva.
         */
        'comentario' => [
            'HTML.Doctype'             => 'HTML5',
            'HTML.Allowed'             => 'p,br,strong,b,em,i',
            'CSS.AllowedProperties'    => '',
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty'   => true,
        ],

        /*
         |------------------------------------------------------------------
         | Perfil para contenido algo más “rico” (si algún día lo necesitas)
         |------------------------------------------------------------------
         */
        'contenido_rico' => [
            'HTML.Doctype'             => 'HTML5',
            'HTML.Allowed'             => 'p,br,ul,ol,li,strong,b,em,i,h2,h3,a[href],blockquote',
            'CSS.AllowedProperties'    => 'text-align',
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty'   => true,
        ],

    ],

];
