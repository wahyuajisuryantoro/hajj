<!-- resources/views/dashboard.blade.php -->
@extends('layouts.master')

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/cards-statistics.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/cards-analytics.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endsection

@section('content')
    <div class="row g-6 mb-6">
        <!-- Card Welcome -->
        <div class="col-xxl-4">
            <div class="card h-100">
                <div class="card-body text-nowrap">
                    <h5 class="card-title mb-1">Selamat Datang <span class="fw-bold">{{ $loggedInMitra->name }}</span>! 🎉
                    </h5>
                    <p class="card-subtitle mb-3">Total Ujroh Bulan Ini</p>
                    <h4 class="text-primary mb-0">Rp {{ number_format($totalUjroh, 0, ',', '.') }}</h4>
                    <p class="mb-3">{{ $ujrohPercentage }}% dari bulan lalu 🚀</p>
                    <a href="{{ route('bonus.index') }}" class="btn btn-sm btn-primary">Lihat Detail</a>
                </div>
                <img src="{{ asset('assets/img/illustrations/trophy.png') }}" class="position-absolute bottom-0 end-0 me-4"
                    height="140" alt="trophy" />
            </div>
        </div>

        <!-- Total Customer -->
        <div class="col-xxl-2 col-md-3 col-sm-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-primary rounded-3">
                                <i class="ri-user-3-line ri-24px"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <p class="mb-0 text-{{ $customerPercentage >= 0 ? 'success' : 'danger' }} me-1">
                                {{ $customerPercentage }}%</p>
                            <i
                                class="ri-arrow-{{ $customerPercentage >= 0 ? 'up' : 'down' }}-s-line text-{{ $customerPercentage >= 0 ? 'success' : 'danger' }}"></i>
                        </div>
                    </div>
                    <div class="card-info mt-5">
                        <h5 class="mb-1">{{ number_format($totalCustomer) }}</h5>
                        <p>Total Customer</p>
                        <div class="badge bg-label-secondary rounded-pill">4 Bulan Terakhir</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Mitra -->
        <div class="col-xxl-2 col-md-3 col-sm-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-success rounded-3">
                                <i class="ri-team-line ri-24px"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <p class="mb-0 text-{{ $mitraPercentage >= 0 ? 'success' : 'danger' }} me-1">
                                {{ $mitraPercentage }}%</p>
                            <i
                                class="ri-arrow-{{ $mitraPercentage >= 0 ? 'up' : 'down' }}-s-line text-{{ $mitraPercentage >= 0 ? 'success' : 'danger' }}"></i>
                        </div>
                    </div>
                    <div class="card-info mt-5">
                        <h5 class="mb-1">{{ number_format($totalMitra) }}</h5>
                        <p>Total Mitra</p>
                        <div class="badge bg-label-secondary rounded-pill">4 Bulan Terakhir</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Profit Chart (diubah menjadi Total Jamaah) -->
        <div class="col-xxl-2 col-md-3 col-sm-6">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center mb-1 flex-wrap">
                        <h5 class="mb-0 me-1">{{ number_format($totalJamaah) }}</h5>
                        <p class="mb-0 text-{{ $jamaahPercentage >= 0 ? 'success' : 'danger' }}">{{ $jamaahPercentage }}%
                        </p>
                    </div>
                    <span class="d-block card-subtitle">Total Jamaah</span>
                </div>
                <div class="card-body">
                    <div id="totalProfitChart"></div>
                </div>
            </div>
        </div>

        <!-- Total Growth Chart (diubah menjadi Total Bonus) -->
        <div class="col-xxl-2 col-md-3 col-sm-6">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center mb-1 flex-wrap">
                        <h5 class="mb-0 me-1">Rp {{ number_format($totalBonus, 0, ',', '.') }}</h5>
                        <p class="mb-0 text-success">Bonus</p>
                    </div>
                    <span class="d-block card-subtitle">Total Bonus</span>
                </div>
                <div class="card-body">
                    <div id="totalGrowthChart"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-6">
        <!-- Chart Status Pembayaran -->
        <div class="col-lg-4 col-md-6 order-1 order-lg-0">
            <div class="card h-100">
                <div class="card-header pb-1">
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-1">Status Pembayaran</h5>
                        <div class="dropdown">
                            <button class="btn btn-text-secondary rounded-pill text-muted border-0 p-1" type="button">
                                <i class="ri-more-2-line ri-20px"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="organicSessionsChart"></div>
                </div>
            </div>
        </div>

        <!-- Project Timeline diubah jadi List Program -->
        <div class="col-lg-8 col-12">
            <div class="card h-100">
                <div class="row">
                    <div class="col-md-8 col-12 order-2 order-md-0">
                        <div class="card-header">
                            <h5 class="mb-1">Program Mendatang</h5>
                            <p class="mb-0 card-subtitle">Total {{ $upcomingPrograms->count() }} Program Aktif</p>
                        </div>
                        <div class="card-body px-2 pt-xl-7">
                            <div id="projectTimelineChart"></div>
                        </div>
                    </div>
                    <div class="col-md-4 col-12 border-start">
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <h5 class="mb-1">Status Jamaah</h5>
                                <div class="dropdown">
                                    <button class="btn btn-text-secondary rounded-pill text-muted border-0 p-1"
                                        type="button">
                                        <i class="ri-more-2-line ri-20px"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="mb-0 card-subtitle">{{ $statusBelum + $statusSedang + $statusSudah }} Total Jamaah
                            </p>
                        </div>
                        <div class="card-body pt-4">
                            <div class="d-flex align-items-center mb-6">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-warning rounded">
                                        <i class="ri-time-line ri-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3 d-flex flex-column">
                                    <h6 class="mb-1">Belum Berangkat</h6>
                                    <small>{{ $statusBelum }} Jamaah</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-6">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-info rounded">
                                        <i class="ri-plane-line ri-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3 d-flex flex-column">
                                    <h6 class="mb-1">Sedang Berangkat</h6>
                                    <small>{{ $statusSedang }} Jamaah</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-success rounded">
                                        <i class="ri-checkbox-circle-line ri-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3 d-flex flex-column">
                                    <h6 class="mb-1">Sudah Berangkat</h6>
                                    <small>{{ $statusSudah }} Jamaah</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Overview Chart diubah jadi Bonus Overview-->
        <div class="col-xxl-4 col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-0">Overview Bonus</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div id="weeklyOverviewChart"></div>
                    <div class="mt-6">
                        <div class="d-flex align-items-center gap-4">
                            <h4 class="mb-0">{{ $ujrohPercentage }}%</h4>
                            <p class="mb-0">Performa bonus Anda {{ $ujrohPercentage >= 0 ? 'naik' : 'turun' }} dibanding
                                bulan lalu 📈</p>
                        </div>
                        <div class="d-grid mt-6">
                            <a href="{{ route('bonus.index') }}" class="btn btn-primary">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Pembayaran Overview -->
        <div class="col-xxl-4 col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Status Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="mb-7">
                        <div class="d-flex align-items-center">
                            <h4 class="mb-0">{{ $totalJamaah }} Jamaah</h4>
                            <span class="text-{{ $jamaahPercentage >= 0 ? 'success' : 'danger' }} ms-2">
                                <i class="ri-arrow-{{ $jamaahPercentage >= 0 ? 'up' : 'down' }}-s-line ri-20px"></i>
                                <span>{{ $jamaahPercentage }}%</span>
                            </span>
                        </div>
                        <p class="mb-0">Total Jamaah Aktif</p>
                    </div>
                    <ul class="p-0 m-0">
                        @foreach ($statusPembayaran as $key => $status)
                            <li class="d-flex align-items-center mb-4">
                                <div class="flex-shrink-0">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-{{ $status['color'] }} rounded">
                                            <i class="{{ $status['icon'] }} ri-24px"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-1">{{ $status['label'] }}</h6>
                                        <p class="mb-0">Status Pembayaran</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="h6 mb-0">{{ $status['count'] }}</span>
                                        <div class="ms-2 badge bg-label-{{ $status['color'] }} rounded-pill">
                                            {{ $status['percentage'] }}%</div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script>
        'use strict';

        (function() {
            let cardColor, labelColor, headingColor, borderColor, bodyColor, grayColor, bodyColorLabel;

            // Chart Status Pembayaran (Menggantikan Organic Sessions Chart)
            const organicSessionsEl = document.querySelector('#organicSessionsChart');
            if (organicSessionsEl) {
                const statusPembayaran = @json($statusPembayaran);
                const organicSessionsConfig = {
                    chart: {
                        height: 330,
                        type: 'donut',
                        parentHeightOffset: 0,
                        offsetY: 0
                    },
                    labels: Object.values(statusPembayaran).map(item => item.label),
                    series: Object.values(statusPembayaran).map(item => item.count),
                    colors: [config.colors.primary, config.colors.warning, config.colors.success],
                    stroke: {
                        width: 3,
                        lineCap: 'round',
                        colors: [cardColor]
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val, opt) {
                            return opt.w.config.labels[opt.seriesIndex];
                        }
                    },
                    legend: {
                        show: true,
                        position: 'bottom',
                        markers: {
                            offsetX: -3
                        },
                        itemMargin: {
                            vertical: 3,
                            horizontal: 10
                        },
                        labels: {
                            colors: headingColor
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '85%',
                                labels: {
                                    show: true,
                                    name: {
                                        fontSize: '1.1rem'
                                    },
                                    value: {
                                        fontSize: '1.75rem',
                                        color: headingColor,
                                        formatter: function(val) {
                                            return val + ' Jamaah';
                                        }
                                    },
                                    total: {
                                        show: true,
                                        fontSize: '1.1rem',
                                        label: 'Total',
                                        formatter: function(w) {
                                            const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                            return total + ' Jamaah';
                                        }
                                    }
                                }
                            }
                        }
                    }
                };
                const organicSessions = new ApexCharts(organicSessionsEl, organicSessionsConfig);
                organicSessions.render();
            }

            // Bonus Overview Chart (Menggantikan Weekly Overview)
            const weeklyOverviewChartEl = document.querySelector('#weeklyOverviewChart');
            if (weeklyOverviewChartEl) {
                const monthlyData = @json($completeMonths);

                const weeklyOverviewChartConfig = {
                    chart: {
                        type: 'line',
                        height: 200,
                        parentHeightOffset: 0,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                            name: 'Bonus',
                            type: 'column',
                            data: monthlyData
                        },
                        {
                            name: 'Trend',
                            type: 'line',
                            data: monthlyData
                        }
                    ],
                    colors: [config.colors.primary, config.colors.info],
                    plotOptions: {
                        bar: {
                            borderRadius: 8,
                            columnWidth: '50%',
                            endingShape: 'rounded',
                            startingShape: 'rounded'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: [0, 3],
                        colors: [config.colors.primary, config.colors.info]
                    },
                    grid: {
                        strokeDashArray: 10,
                        padding: {
                            bottom: -8,
                            left: 0,
                            right: 0
                        }
                    },
                    xaxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                            'Dec'
                        ],
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '13px'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '13px'
                            },
                            formatter: function(val) {
                                return formatRupiah(val);
                            }
                        }
                    },
                    tooltip: {
                        shared: true,
                        y: {
                            formatter: function(val) {
                                return formatRupiah(val);
                            }
                        }
                    }
                };
                const weeklyOverviewChart = new ApexCharts(weeklyOverviewChartEl, weeklyOverviewChartConfig);
                weeklyOverviewChart.render();
            }

            // Program Timeline Chart
            const projectTimelineEl = document.querySelector('#projectTimelineChart');
            if (projectTimelineEl) {
                const programData = @json($upcomingPrograms);

                const projectTimelineConfig = {
                    chart: {
                        height: 280,
                        type: 'rangeBar',
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                        data: programData.map((program, index) => ({
                            x: program.name,
                            y: [
                                new Date().getTime(),
                                new Date(program.date).getTime()
                            ],
                            fillColor: config.colors[['primary', 'info', 'success', 'warning',
                                'danger'
                            ][index % 5]]
                        }))
                    }],
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 15,
                            distributed: true
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val, opts) {
                            const program = programData[opts.dataPointIndex];
                            return `${program.jamaah_count} Jamaah`;
                        }
                    },
                    xaxis: {
                        type: 'datetime',
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '13px'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '13px'
                            }
                        }
                    },
                    grid: {
                        strokeDashArray: 10,
                        padding: {
                            top: -35,
                            left: 20,
                            right: 20,
                            bottom: 0
                        }
                    },
                    tooltip: {
                        custom: function({
                            seriesIndex,
                            dataPointIndex,
                            w
                        }) {
                            const program = programData[dataPointIndex];
                            return `
                        <div class="p-2">
                            <div class="fw-bold mb-1">${program.name}</div>
                            <div>Tanggal: ${program.date}</div>
                            <div>Jamaah: ${program.jamaah_count}</div>
                            <div>Kuota Terisi: ${program.occupancy_rate}%</div>
                        </div>
                    `;
                        }
                    }
                };

                const projectTimeline = new ApexCharts(projectTimelineEl, projectTimelineConfig);
                projectTimeline.render();
            }

            // Helper function
            function formatRupiah(value) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(value);
            }
        })();
    </script>
@endsection
