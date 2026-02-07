<?php 
include(__DIR__ . "/../layout/headerAdmin.php");
include(__DIR__ . "/../data/conexion.php");
// ====================================
// KPIs
// ====================================
$kpis = $con->query("
    SELECT
        (SELECT COUNT(*) FROM noticias) AS total_noticias,
        (SELECT COUNT(*) FROM noticias WHERE fecha_publicacion <= NOW()) AS publicadas,
        (SELECT COUNT(*) FROM noticias WHERE fecha_publicacion > NOW()) AS programadas,
        (SELECT COUNT(*) FROM noticias_stats) AS total_vistas,
        (SELECT COUNT(*) FROM noticia_likes) AS total_likes
")->fetch_assoc();
// Tiempo total de lectura
$tiempoTotal = $con->query("
    SELECT COALESCE(SUM(tiempo_segundos),0) AS tiempo_total
    FROM noticias_stats
")->fetch_assoc()['tiempo_total'];
// ====================================
// Últimas noticias (TOP 5) SIN DUPLICAR TIEMPO
// ====================================
$resultNoticias = $con->query("
    SELECT 
        n.id,
        n.titulo,
        n.descripcion,
        n.crop3,
        n.vistas,
        n.likes,
        n.fecha_publicacion,
        GROUP_CONCAT(DISTINCT c.nombre SEPARATOR ',') AS categorias,
        COALESCE((SELECT SUM(tiempo_segundos) FROM noticias_stats WHERE noticia_id = n.id), 0) AS tiempo_total_stats
    FROM noticias n
    LEFT JOIN noticia_categoria nc ON n.id = nc.noticia_id
    LEFT JOIN categorias c ON nc.categoria_id = c.id_c
    GROUP BY n.id
    ORDER BY n.fecha_publicacion DESC
    LIMIT 5
");
$ultimasNoticias = [];
while($row = $resultNoticias->fetch_assoc()){
    $ultimasNoticias[] = $row;
}
?>
<div class="container-fluid">
    <!-- SALUDO -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Bienvenido, <?= htmlspecialchars($fila['usuario']) ?></h1>
    </div>
    <a href="crear.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Nueva Noticia</a>
    <!-- KPIs -->
     <div class="card">
        <div class="card-body">
            <h5 class="card-title">KPIs</h5>
            <div class="row mb-4">
                <?php 
                    $cards = [
                        ['Noticias', $kpis['total_noticias'], 'bi-newspaper', 'bg-primary'],
                        ['Publicadas', $kpis['publicadas'], 'bi-check-circle', 'bg-success'],
                        ['Programadas', $kpis['programadas'], 'bi-clock', 'bg-warning'],
                        ['Vistas', number_format($kpis['total_vistas']), 'bi-eye', 'bg-info'],
                        ['Likes', number_format($kpis['total_likes']), 'bi-heart', 'bg-danger'],
                        ['Tiempo (min)', number_format($tiempoTotal/60), 'bi-stopwatch', 'bg-secondary'],
                    ];
                    foreach($cards as $c):
                ?>
                <div class="col-md-2 mb-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi <?= $c[2] ?> fs-3 <?= $c[3] ?>"></i>
                            <h4 class="mb-0"><?= $c[1] ?></h4>
                            <small class="text-muted"><?= $c[0] ?></small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <!-- FILTROS -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h5>Filtros de Estadísticas</h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label>Fecha Inicio</label>
                            <input type="date" id="filterFechaInicio" class="form-control" value="<?= date('Y-m-d', strtotime('-30 days')) ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Fecha Fin</label>
                            <input type="date" id="filterFechaFin" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-secondary w-100" onclick="loadGlobalStats(); loadLikesStats();">
                                <i class="bi bi-funnel"></i> Aplicar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>
    <!-- GRÁFICOS -->
    <div class="row mb-4">
        <div class="col-md-6"><canvas id="globalChartVistas"></canvas></div>
        <div class="col-md-6"><canvas id="globalChartTiempo"></canvas></div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6"><canvas id="globalChartLikes"></canvas></div>
        <div class="col-md-6"><canvas id="globalChartLikesRegion"></canvas></div>
    </div>
    <!-- ÚLTIMAS NOTICIAS -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light"><h5>Últimas Noticias</h5></div>
            <div class="card-body">
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php foreach($ultimasNoticias as $n):
                        $desc = mb_strimwidth($n['descripcion'] ?? '', 0, 80, '...');
                        $img = !empty($n['crop3']) ? "./../".$n['crop3'] : "./../img/placeholder.jpg";
                    ?>
                    <div class="col">
                        <div class="card">
                            <div class="card-header"><small><?= date('d/m/Y H:i', strtotime($n['fecha_publicacion'])) ?></small></div>
                            <img src="<?= htmlspecialchars($img) ?>" class="card-img-top">
                            <div class="card-body">
                                <div class="news-tags">
                                    <?php foreach(array_filter(array_map('trim', explode(',', $n['categorias'] ?? ''))) as $cat): ?>
                                        <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <h5 class="card-title text-truncate"><?= htmlspecialchars($n['titulo']) ?></h5>
                                <p class="small text-muted"><?= htmlspecialchars($desc) ?></p>
                            </div>
                            <div class="card-footer d-flex justify-content-between small text-muted">
                                <span><i class="bi bi-eye"></i> <?= $n['vistas'] ?></span>
                                <span><i class="bi bi-clock"></i> <?= number_format($n['tiempo_total_stats']/60,0) ?>m</span>
                                <span><i class="bi bi-heart"></i> <?= $n['likes'] ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
<script>
    const charts = {};
    document.addEventListener("DOMContentLoaded", ()=>{ loadGlobalStats(); loadLikesStats(); });
    // ================================
    // GLOBAL STATS
    // ================================
    function loadGlobalStats(){
        const f1=document.getElementById('filterFechaInicio').value;
        const f2=document.getElementById('filterFechaFin').value;
        fetch(`./../controllers/obtener_estadisticas_globales.php?fecha_inicio=${f1}&fecha_fin=${f2}`)
            .then(r=>r.json()).then(d=>{
                renderAreaChart('globalChartVistas', d, 'vistas', 'Vistas por categoría');
                renderAreaChart('globalChartTiempo', d, 'tiempo', 'Tiempo de lectura (s)');
            });
    }
    // ================================
    // LIKES STATS
    // ================================
    function loadLikesStats(){
        const f1=document.getElementById('filterFechaInicio').value;
        const f2=document.getElementById('filterFechaFin').value;
        fetch(`./../controllers/obtener_estadisticas_likes.php?fecha_inicio=${f1}&fecha_fin=${f2}`)
            .then(r=>r.json()).then(d=>{
                renderAreaChart('globalChartLikes', d, 'likes', 'Likes por categoría');
                renderBarChart('globalChartLikesRegion', d.geo.estados, 'Likes por Estado');
            });
    }
    // ================================
    // CHART AREA
    // ================================
    function renderAreaChart(id,data,metric,title){
        if(!data || !data.categorias) return;
        const ctx=document.getElementById(id);
        if(charts[id]) charts[id].destroy();

        const colors=['rgba(75,192,192,0.35)','rgba(255,99,132,0.35)','rgba(54,162,235,0.35)','rgba(255,159,64,0.35)','rgba(153,102,255,0.35)'];

        const datasets = Object.entries(data.categorias).map(([cat,val],i)=>({
            label:cat,
            data:val[metric] || [],
            fill:true,
            tension:.4,
            borderWidth:2,
            backgroundColor:colors[i%colors.length],
            borderColor:colors[i%colors.length].replace('0.35','1')
        }));

        charts[id]=new Chart(ctx,{
            type:'line',
            data:{labels:data.labels,datasets},
            options:{responsive:true,interaction:{mode:'index',intersect:false},
            plugins:{title:{display:true,text:title}},scales:{y:{beginAtZero:true}}}
        });
    }
    // ================================
    // CHART BAR GEO
    // ================================
    function renderBarChart(id,geo,title){
        if(!geo) return;
        const ctx=document.getElementById(id);
        if(charts[id]) charts[id].destroy();
        charts[id]=new Chart(ctx,{
            type:'bar',
            data:{labels:geo.labels.slice(0,10),datasets:[{label:title,data:geo.values.slice(0,10)}]},
            options:{responsive:true,plugins:{title:{display:true,text:title}},scales:{y:{beginAtZero:true}}}
        });
    }
</script>
<?php include(__DIR__ . "/../layout/footerAdmin.php"); ?>