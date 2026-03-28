<?php
function obtenerEmbedSocial($url){
    // X (Twitter)
    if(preg_match('/(twitter\.com|x\.com)\/.*\/status\/(\d+)/i',$url)){
        return [
            "type" => "twitter",
            "url" => $url
        ];
    }
    // INSTAGRAM
    if(preg_match('/instagram\.com/i',$url)){
        return [
            "type" => "instagram",
            "url" => strtok($url,'?')
        ];
    }
    // TIKTOK
    if(preg_match('/tiktok\.com/i',$url)){
        if(preg_match('/video\/(\d+)/',$url,$m)){
            return [
                "type" => "tiktok",
                "id" => $m[1]
            ];
        }
    }
    // FACEBOOK
    if(preg_match('/facebook\.com/i',$url)){
        return [
            "type" => "facebook",
            "url" => $url
        ];
    }
    // YOUTUBE
    if(preg_match('/youtube\.com|youtu\.be/i',$url)){
        preg_match('/(v=|\/)([0-9A-Za-z_-]{11})/',$url,$m);
        if(isset($m[2])){
            return [
                "type" => "youtube",
                "id" => $m[2]
            ];
        }
    }
    // VIMEO
    if(preg_match('/vimeo\.com/i',$url)){
        preg_match('/vimeo\.com\/(\d+)/',$url,$m);
        if(isset($m[1])){
            return [
                "type"=>"vimeo",
                "id"=>$m[1]
            ];
        }
    }
    return false;
}
function wrapEmbedSocial($html){
    return '<div class="social-embed-container"><div class="video-responsive">' . $html . '</div></div>';
}
function renderizarEmbedSocial($url){
    $embed = obtenerEmbedSocial($url);
    if(!$embed) return "";
    switch($embed['type']){
        case "twitter":
            return wrapEmbedSocial(
                '<blockquote class="twitter-tweet">
                    <a href="'.$embed['url'].'"></a>
                </blockquote>'
            );
        case "instagram":
            return wrapEmbedSocial(
                '<blockquote class="instagram-media"
                    data-instgrm-permalink="'.$embed['url'].'"
                    data-instgrm-version="14">
                </blockquote>
                <script async src="https://www.instagram.com/embed.js"></script>'
            );
        case "tiktok":
            return wrapEmbedSocial(
                '<iframe
                    src="https://www.tiktok.com/embed/v2/'.$embed['id'].'"
                    width="100%"
                    height="100%"
                    frameborder="0"
                    allowfullscreen></iframe>'
            );
        case "facebook":
            return wrapEmbedSocial(
                '<iframe
                    src="https://www.facebook.com/plugins/post.php?href='.urlencode($embed['url']).'"
                    width="100%"
                    height="100%"
                    frameborder="0"></iframe>'
            );
        case "youtube":
            return wrapEmbedSocial(
                '<iframe
                        src="https://www.youtube.com/embed/'.$embed['id'].'"
                        frameborder="0"
                        allowfullscreen></iframe>'
            );
        case "vimeo":
            return wrapEmbedSocial(
                '<iframe
                        src="https://player.vimeo.com/video/'.$embed['id'].'"
                        frameborder="0"
                        allowfullscreen></iframe>'
            );
    }
}
function procesarEmbedsSociales($html){
    // Solo procesar bloques creados desde el botón "Embeb" (social-embed)
    $html = preg_replace_callback(
        '/<div class="social-embed" data-url="(.*?)">.*?<\/div>/i',
        function($match){
            return renderizarEmbedSocial($match[1]);
        },
        $html
    );

    // Si un embed quedó envuelto en un párrafo (Quill suele hacer esto) lo desenpaquetamos
    $html = preg_replace(
        '/<p[^>]*>\s*(<blockquote[\s\S]*?<\/blockquote>\s*(?:<script[\s\S]*?<\/script>)?)\s*<\/p>/i',
        '$1',
        $html
    );

    // eliminar párrafos vacíos residuales
    $html = preg_replace('/<p[^>]*>\s*<\/p>/i', '', $html);

    return $html;
}