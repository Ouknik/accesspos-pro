{{--
Component Chart unifié pour AccessPos Pro avec SB Admin 2
Usage:
@include('components.sb-admin-chart', [
    'chartId' => 'sales-chart',
    'type' => 'line',
    'title' => 'Évolution des ventes',
    'data' => $chartData
])
--}}

@php
    $chartId = $chartId ?? 'chart-' . Str::random(8);
    $type = $type ?? 'line'; // line, bar, pie, doughnut, radar, area
    $title = $title ?? 'Graphique';
    $height = $height ?? 400;
    $data = $data ?? [];
    $options = $options ?? [];
    $cardClass = $cardClass ?? 'shadow mb-4';
    $responsive = $responsive ?? true;
    $animated = $animated ?? true;
@endphp

<div class="card {{ $cardClass }}">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-chart-area mr-2"></i>{{ $title }}
        </h6>
        <div class="dropdown no-arrow">
            <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                <div class="dropdown-header">Actions du graphique:</div>
                <a class="dropdown-item" href="#" onclick="downloadChart{{ Str::studly($chartId) }}('png')">
                    <i class="fas fa-download fa-sm fa-fw mr-2 text-gray-400"></i>
                    Télécharger PNG
                </a>
                <a class="dropdown-item" href="#" onclick="downloadChart{{ Str::studly($chartId) }}('jpg')">
                    <i class="fas fa-download fa-sm fa-fw mr-2 text-gray-400"></i>
                    Télécharger JPG
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" onclick="refreshChart{{ Str::studly($chartId) }}()">
                    <i class="fas fa-sync fa-sm fa-fw mr-2 text-gray-400"></i>
                    Actualiser
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="chart-area">
            <canvas id="{{ $chartId }}" height="{{ $height }}"></canvas>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const ctx = document.getElementById('{{ $chartId }}').getContext('2d');
    
    // Configuration par défaut
    const defaultConfig = {
        type: '{{ $type }}',
        data: @json($data),
        options: {
            responsive: {{ $responsive ? 'true' : 'false' }},
            maintainAspectRatio: false,
            animation: {
                duration: {{ $animated ? '1000' : '0' }}
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: '#4e73df',
                    borderWidth: 1
                }
            },
            scales: {
                @if(in_array($type, ['line', 'bar', 'area']))
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(234, 236, 244, 1)',
                        drawBorder: false,
                        borderDash: [2]
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(234, 236, 244, 1)',
                        drawBorder: false,
                        borderDash: [2]
                    }
                }
                @endif
            },
            ...@json($options)
        }
    };
    
    // Couleurs AccessPos
    const colors = {
        primary: '#4e73df',
        success: '#1cc88a',
        info: '#36b9cc',
        warning: '#f6c23e',
        danger: '#e74a3b',
        secondary: '#858796'
    };
    
    // Appliquer les couleurs automatiquement si non spécifiées
    if (defaultConfig.data.datasets) {
        defaultConfig.data.datasets.forEach((dataset, index) => {
            const colorKeys = Object.keys(colors);
            const colorKey = colorKeys[index % colorKeys.length];
            
            if (!dataset.backgroundColor) {
                dataset.backgroundColor = colors[colorKey] + '20'; // Transparence
                dataset.borderColor = colors[colorKey];
                dataset.borderWidth = 2;
            }
        });
    }
    
    // Créer le graphique
    window.chart{{ Str::studly($chartId) }} = new Chart(ctx, defaultConfig);
    
    console.log('Chart {{ $chartId }} initialisé avec succès');
});

// Fonctions utilitaires
function downloadChart{{ Str::studly($chartId) }}(format) {
    const chart = window.chart{{ Str::studly($chartId) }};
    const url = chart.toBase64Image('image/' + format, 1.0);
    
    const link = document.createElement('a');
    link.download = '{{ $title }}_' + new Date().toISOString().split('T')[0] + '.' + format;
    link.href = url;
    link.click();
    
    AccessPos.utils.showToast('Graphique téléchargé', 'success');
}

function refreshChart{{ Str::studly($chartId) }}() {
    const chart = window.chart{{ Str::studly($chartId) }};
    chart.update();
    AccessPos.utils.showToast('Graphique actualisé', 'info');
}

function updateChartData{{ Str::studly($chartId) }}(newData) {
    const chart = window.chart{{ Str::studly($chartId) }};
    chart.data = newData;
    chart.update();
}
</script>

<style>
.chart-area {
    position: relative;
    height: {{ $height }}px;
    width: 100%;
}

#{{ $chartId }} {
    transition: all 0.3s ease;
}

@media (max-width: 768px) {
    .chart-area {
        height: 300px;
    }
}
</style>
