<?php
function obtenerEmbedVideo($url){
    // YOUTUBE
    if(preg_match('/(youtube\.com|youtu\.be)/i', $url)){
        preg_match('/(v=|\/)([0-9A-Za-z_-]{11}).*/', $url, $matches);
        if(isset($matches[2])){
            return [
                "src" => "https://www.youtube.com/embed/".$matches[2],
                "ratio" => "16:9"
            ];
        }
    }
    // TIKTOK
    if(preg_match('/tiktok\.com/i', $url)){
        preg_match('/video\/(\d+)/', $url, $matches);
        if(isset($matches[1])){
            return [
                "src" => "https://www.tiktok.com/embed/v2/".$matches[1],
                "ratio" => "9:16"
            ];
        }
    }
    // FACEBOOK
    if(preg_match('/facebook\.com/i', $url)){
        return [
            "src" => "https://www.facebook.com/plugins/video.php?href="
                    .urlencode($url)
                    ."&show_text=false",
            "ratio" => "16:9"
        ];
    }
    // VIMEO
    if(preg_match('/vimeo\.com/i', $url)){
        preg_match('/vimeo\.com\/(\d+)/', $url, $matches);
        if(isset($matches[1])){
            return [
                "src" => "https://player.vimeo.com/video/".$matches[1],
                "ratio" => "16:9"
            ];
        }
    }
    // INSTAGRAM
    if(preg_match('/instagram\.com/i', $url)){
        // eliminar parámetros ?igsh=...
        $url = strtok($url, '?');
        return [
            "src" => rtrim($url, '/') . '/',
            "ratio" => "9:16",
            "type" => "instagram"
        ];
    }
    return false;
}
function renderizarVideo($url){
    $embed = obtenerEmbedVideo($url);
    if(!$embed){
        return "<p>No se puede mostrar el video</p>";
    }
    // INSTAGRAM
    if(isset($embed['type']) && $embed['type'] == "instagram"){
        return '
        <div class="video-responsive-vertical">
            <blockquote 
                class="instagram-media"
                data-instgrm-permalink="'.$embed['src'].'"
                data-instgrm-version="14">
            </blockquote>
        </div>
        <script>
        if(window.instgrm){
            window.instgrm.Embeds.process();
        }
        </script>
        ';
    }
    // NORMAL (YouTube, TikTok, Facebook, Vimeo)
    $ratio = $embed['ratio'] == "9:16"
        ? "padding-bottom:177.77%; max-width:400px; margin:auto;"
        : "padding-bottom:56.25%;";
    return '
        <div class="video-responsive" style="'.$ratio.'">
            <iframe 
                src="'.$embed['src'].'"
                frameborder="0"
                allowfullscreen
                loading="lazy">
            </iframe>
        </div>
    ';
}