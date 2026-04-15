@extends('lecturer.layouts.app')

@section('content')
    <div class="lecturer-dashboard">
        <section class="lecturer-hero">
            <p class="lecturer-hero-kicker">Dashboard</p>
            <h1 class="lecturer-hero-title">Ringkasan kelas</h1>
        </section>

        <section class="lecturer-grid">
            <div class="lecturer-panel bg-white">
                <h2 class="lecturer-panel-title rank">Rank Distribution</h2>
                <div class="lecturer-bar-wrap">
                    <canvas id="rankChart"></canvas>
                </div>
            </div>

            <div class="lecturer-panel bg-white">
                <h2 class="lecturer-panel-title streak">Streak Distribution</h2>
                <div class="lecturer-chart-wrap">
                    <canvas id="streakChart" class="lecturer-streak-canvas"></canvas>
                </div>
            </div>

            <div class="lecturer-panel bg-white lecturer-panel-wide">
                <h2 class="lecturer-panel-title top">Top 5 Students</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm text-gray-800">
                        <thead class="bg-green-500 text-xs font-semibold uppercase tracking-wider text-white">
                            <tr>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Weekly Score</th>
                                <th class="px-4 py-2">Rank</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($topStudents as $student)
                                <tr>
                                    <td class="px-4 py-2">{{ $student->user->name }}</td>
                                    <td class="px-4 py-2">{{ $student->weekly_score }}</td>
                                    <td class="px-4 py-2">{{ $student->ranks->last()?->name ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <style>
        .lecturer-dashboard {
            max-width: 1200px;
            margin: 0 auto;
        }

        .lecturer-hero {
            margin-bottom: 28px;
            padding: 28px;
            border-radius: 30px;
            border: 1px solid rgba(255, 228, 236, 0.14);
            background: rgba(74, 19, 39, 0.78);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.22);
        }

        .lecturer-hero-kicker {
            margin: 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.38em;
            color: rgba(255, 228, 236, 0.75);
        }

        .lecturer-hero-title {
            margin: 14px 0 0;
            font-size: 46px;
            line-height: 1.15;
            font-weight: 700;
            color: #fff;
        }

        .lecturer-hero-copy {
            max-width: 760px;
            margin: 16px 0 0;
            font-size: 16px;
            line-height: 1.8;
            color: rgba(255, 240, 244, 0.76);
        }

        .lecturer-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
        }

        .lecturer-panel {
            padding: 22px;
            border-radius: 28px;
        }

        .lecturer-panel-wide {
            grid-column: 1 / -1;
        }

        .lecturer-panel-title {
            margin: 0 0 14px;
            font-size: 20px;
            font-weight: 700;
        }

        .lecturer-panel-title.rank {
            color: #b2215b;
        }

        .lecturer-panel-title.streak {
            color: #d97706;
        }

        .lecturer-panel-title.top {
            color: #be185d;
        }

        .lecturer-chart-wrap {
            height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lecturer-bar-wrap {
            height: 360px;
        }

        .lecturer-streak-canvas {
            width: 260px !important;
            height: 260px !important;
            max-width: 260px;
            max-height: 260px;
        }

        @media (max-width: 992px) {
            .lecturer-grid {
                grid-template-columns: 1fr;
            }

            .lecturer-panel-wide {
                grid-column: auto;
            }
        }

        @media (max-width: 768px) {
            .lecturer-hero {
                padding: 22px;
            }

            .lecturer-hero-title {
                font-size: 34px;
            }
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        const rankCtx = document.getElementById('rankChart').getContext('2d');
        new Chart(rankCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($rankStats->pluck('name')->values()) !!},
                datasets: [{
                    label: 'Students',
                    data: {!! json_encode($rankStats->pluck('students_count')->values()) !!},
                    backgroundColor: '#d9467a',
                    borderRadius: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#4a1327',
                        titleColor: '#ffffff',
                        bodyColor: '#ffe4ec'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#64748b',
                            precision: 0,
                            font: {
                                size: 13
                            }
                        },
                        grid: {
                            color: 'rgba(148, 163, 184, 0.2)'
                        },
                        title: {
                            display: true,
                            text: 'Number of Students',
                            color: '#475569',
                            font: {
                                weight: '600'
                            }
                        }
                    },
                    x: {
                        ticks: {
                            color: '#475569',
                            autoSkip: false,
                            maxRotation: 32,
                            minRotation: 32,
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Rank',
                            color: '#475569',
                            font: {
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });

        const streakCtx = document.getElementById('streakChart').getContext('2d');

        new Chart(streakCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($streakStats->pluck('streak')->values()) !!},
                datasets: [{
                    data: {!! json_encode($streakStats->pluck('total')->values()) !!},
                    backgroundColor: ['#f59e0b', '#f97316', '#fb7185', '#f472b6', '#c0265f'],
                    borderColor: '#ffffff',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#4a1327',
                        titleColor: '#ffffff',
                        bodyColor: '#ffe4ec',
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                return `${label} days: ${value} students`;
                            }
                        }
                    },
                    datalabels: {
                        color: '#ffffff',
                        font: {
                            weight: 'bold',
                            size: 12
                        },
                        formatter: (value, ctx) => {
                            if (value <= 0) {
                                return '';
                            }

                            const total = ctx.dataset.data.reduce((sum, current) => sum + current, 0);
                            const percentage = total ? Math.round((value / total) * 100) : 0;
                            return `${percentage}%`;
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    </script>
@endsection
