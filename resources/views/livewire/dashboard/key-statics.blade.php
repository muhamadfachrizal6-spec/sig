<div>
    <div class="row mb-4">
        @if (session()->has('message'))
            <script>
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: '{{ session('message') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            </script>
        @endif
        <div class="col-lg-4">
            <label for="account" class="form-label">Account:</label>
            <select id="account" class="form-select" wire:model="account">
                <option value="All">All</option>
                <option value="IncomeStatement">Income Statement</option>
                <option value="FinancialPosition">Financial Position</option>
                <option value="Dividend">Dividend</option>
            </select>
        </div>

        <div class="col-lg-4">
            <label for="timeframe" class="form-label">Timeframe:</label>
            <button type="button" class="btn {{ $timeframe ? 'bg-white' : 'primary-color text-white' }} border-1 border-primary-black w-100 text-start" data-bs-toggle="modal"
                data-bs-target="#timeframeModal">
                {{ $timeframe ?: 'Custom timeframe' }}
            </button>
        </div>

        <div class="col-lg-4">
            <label for="periode" class="form-label">Periode:</label>
            <select id="periode" class="form-select" wire:model="periode">
                <option value="annual">Annual</option>
                <option value="quarterly">Quarterly</option>
            </select>
        </div>
    </div>
    <div wire:loading wire:target="submitFilter, submitYears, companyChanged, updatedAccount" class="spinner-container">
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    <div class="modal fade" id="timeframeModal" tabindex="-1" aria-labelledby="timeframeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title primary-color-text" id="timeframeModalLabel">Select Timeframe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="submitFilter">
                        <!-- Timeframe Section -->
                        <div class="mb-4">
                            <p class="mb-2 fw-bold primary-color-text">Choose a Timeframe:</p>
                            <div class="d-flex flex-column gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="threeYears" name="timeframe"
                                        value="3 Years" wire:model.defer="timeframe">
                                    <label class="form-check-label" for="threeYears">Last 3 Years</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="fiveYears" name="timeframe"
                                        value="5 Years" wire:model.defer="timeframe">
                                    <label class="form-check-label" for="fiveYears">Last 5 Years</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="tenYears" name="timeframe"
                                        value="10 Years" wire:model.defer="timeframe">
                                    <label class="form-check-label" for="tenYears">Last 10 Years</label>
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <hr class="my-4">

                        <!-- Specific Years Section -->
                        <div>
                            <p class="mb-2 fw-bold primary-color-text">Or Select Specific Years:</p>
                            <div class="row row-cols-4 g-3">
                                @foreach ($yearsAvailable as $year)
                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="year{{ $year }}"
                                                value="{{ $year }}" wire:model.defer="selectedYears">
                                            <label class="form-check-label"
                                                for="year{{ $year }}">{{ $year }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="modal-footer mt-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn primary-color text-white" data-bs-dismiss="modal">Apply Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row d-flex flex-row justify-content-between g-3 nowrap" style="cursor: pointer" data-bs-toggle="modal"
        data-bs-target="#dataModal">
        <div class="col-lg-6 border-0">
            <div class="card p-3">
                @if (empty($profitData))
                    <p>No data available for the selected timeframe.</p>
                @else
                    @foreach ($profitData as $year => $quarters)
                        <h6>Growth Net Profit {{ $year }}</h6>
                        @if (is_array($quarters) || is_object($quarters))
                            @foreach ($quarters as $quarter => $data)
                                @if (is_array($data) || is_object($data))
                                    @foreach ($data as $entry)
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <span>{{ $quarter }}</span>
                                                    <span>{{ $entry['value'] }}%</span>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar 
                                                        @if($entry['value'] >= 75) 
                                                            bg-success 
                                                        @elseif($entry['value'] >= 50) 
                                                            bg-warning 
                                                        @else 
                                                            bg-danger 
                                                        @endif"
                                                        role="progressbar"
                                                        style="width: {{ $entry['value'] }}%;"
                                                        aria-valuenow="{{ $entry['value'] }}" 
                                                        aria-valuemin="0" 
                                                        aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p>No data available for {{ $quarter }}.</p>
                                @endif
                            @endforeach
                        @else
                            <p>No data available for {{ $year }}.</p>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>

        <div class="col-lg-6 border-0">
            <div class="card p-3">
                @if (empty($priceData))
                    <p>No data available for the selected timeframe.</p>
                @else
                    @foreach ($priceData as $year => $quarters)
                        <h6>Price {{ $year }}</h6>
                        @if (is_array($quarters) || is_object($quarters))
                            @foreach ($quarters as $quarter => $data)
                                @if (is_array($data) || is_object($data))
                                    @foreach ($data as $entry)
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <span>{{ $quarter }}</span>
                                                    <span>{{ $entry['value'] }}</span>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="width: {{ $entry['value'] }}"
                                                        aria-valuenow="{{ $entry['value'] }}" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p>No data available for {{ $quarter }}.</p>
                                @endif
                            @endforeach
                        @else
                            <p>No data available for {{ $year }}.</p>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
        <div class="modal-dialog custom-modal-chart" modal-dialog-centered" style="max-width: 90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dataModalLabel">Comparisson Price & Growth Net Profit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalPopupData"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <p class="mt-4"><b>Analyze Statistics</b></p>
    <hr>

    <div class="row g-3">
        @if ($account === 'All')
            <!-- Tampilkan Income Statement Chart -->
            <div class="col-lg-4">
                <div class="shadow rounded" id="incomeStatement"></div>
            </div>
        @elseif ($account === 'IncomeStatement')
            <div class="col-lg-12">
                <div class="shadow rounded" id="incomeStatement"></div>
            </div>
        @endif

        @if ($account === 'All')
            <!-- Tampilkan Financial Position Chart -->
            <div class="col-lg-4">
                <div class="shadow rounded" id="financialPosition"></div>
            </div>
        @elseif ($account === 'FinancialPosition')
            <div class="col-lg-12">
                <div class="shadow rounded" id="financialPosition"></div>
            </div>
        @endif

        @if ($account === 'All')
            <div class="col-lg-4">
                <div class="shadow rounded" id="dividendData"></div>
            </div>
        @elseif ($account === 'Dividend')
            <div class="col-lg-12">
                <div class="shadow rounded" id="dividendData"></div>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const incomeStatementData = @json($incomeStatementData);
        const financialPositionData = @json($financialPositionData);
        const dividendData = @json($dividendData);

        loadKeyStatisticsChart('incomeStatement', incomeStatementData, 'Income Statement');
        loadKeyStatisticsChart('financialPosition', financialPositionData, 'Financial Position');
        loadKeyStatisticsChart('dividendData', dividendData, 'Dividend');
        loadKeyStatisticsChart('modalPopupData', modalPopupData, 'PERIODE');
    });

    window.addEventListener('update-chart', function(event) {
        loadKeyStatisticsChart('incomeStatement', event.detail.incomeStatementData, 'Income Statement');
        loadKeyStatisticsChart('financialPosition', event.detail.financialPositionData, 'Financial Position');
        loadKeyStatisticsChart('dividendData', event.detail.dividendData, 'Dividend');
        loadKeyStatisticsChart('modalPopupData', event.detail.modalPopupData, 'PERIODE');
    });

    function loadKeyStatisticsChart(container, data, title) {
        if (!data || !data.categories || !data.series) {
            console.error("Invalid data format:", data);
            return;
        }

        const formattedData = {
            categories: data.categories,
            series: data.series.map(series => {
                return {
                    name: series.name,
                    data: series.data.map(value => parseFloat(value))
                };
            })
        };

        if (formattedData.categories && formattedData.series) {
            if (container === 'modalPopupData') {
                Highcharts.chart(container, {
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: title
                    },
                    xAxis: {
                        categories: formattedData.categories,
                        labels: {
                            rotation: -45, // Memutar label agar tidak bertumpuk
                            step: 1 // Menampilkan setiap label
                        }
                    },
                    yAxis: {
                        title: {
                            text: 'Values in Percent (%)'
                        }
                    },
                    plotOptions: {
                        series: {
                            compare: 'percent',
                            compareStart: true
                        }
                    },
                    tooltip: {
                        pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.change}%)<br/>',
                        valueDecimals: 2
                    },
                    series: formattedData.series.map(series => ({
                        ...series,
                        compareStart: true
                    }))
                });
            } else {
                Highcharts.chart(container, {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: title
                    },
                    xAxis: {
                        categories: formattedData.categories,
                        labels: {
                            rotation: -45, // Memutar label agar tidak bertumpuk
                            step: 1 // Menampilkan setiap label
                        }
                    },
                    yAxis: {
                        title: {
                            text: 'Values in Milliards'
                        }
                    },
                    tooltip: {
                        pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>Rp.{point.y} Milyards</b><br/>'
                    },
                    series: formattedData.series
                });
            }
        }
    }
</script>
<script>
    document.addEventListener('livewire:load', function() {
        Livewire.on('closeModal', (modalId) => {
            const modal = document.getElementById(modalId);
            if (modal) {
                const bootstrapModal = bootstrap.Modal.getInstance(modal);
                if (bootstrapModal) {
                    bootstrapModal.hide();
                }

                // Hapus elemen backdrop
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());

                // Pulihkan scroll di body
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            }
        });
    });
</script>