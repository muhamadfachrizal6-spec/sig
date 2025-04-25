@extends('layouts.navigation-admin')
@section('title', 'Edit Key Statistics for ' . $company->name)

@section('contents')
    <div class="container">
        <h1 class="mb-4 text-center primary-color-text">Edit Key Statistics for {{ $company->name }}</h1>

        <form method="GET" action="">
            <div class="card shadow p-4 mb-4 bg-white rounded">
                <div class="row g-4 align-items-center">
                    <!-- Filter Tahun -->
                    <div class="col-md-6">
                        <label for="filter_years" class="form-label fw-bold primary-color-text">Select Years to
                            Display</label>
                        <div class="border rounded p-3 bg-light">
                            @foreach ($allYears as $year)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="filter_years[]"
                                        value="{{ $year }}" id="year_{{ $year }}"
                                        {{ in_array($year, $years) ? 'checked' : '' }}>
                                    <label class="form-check-label primary-color-text" for="year_{{ $year }}">
                                        {{ $year }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted d-block mt-2" data-bs-toggle="tooltip" data-bs-placement="right"
                            title="Select up to 3 years for filtering the data.">
                            You can select up to 3 years.
                        </small>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="col-md-4 offset-md-2 d-flex justify-content-md-end justify-content-start">
                        <button type="submit"
                            class="btn btn-custom2 d-flex align-items-center justify-content-center w-100 shadow-sm">
                            <i class="fas fa-filter me-2"></i> Filter Data
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Form untuk update data -->
        <form action="{{ route('admin_analyze.key_statistics.update', $company->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Income Statement Section -->
            <div class="row income-statement d-flex flex-wrap gap-4">
                <p class="mt-4"><b>Income Statement Data</b></p>
                <hr>
                @foreach (['revenue', 'gross_profit', 'net_profit'] as $type)
                    <div class="col-md-5">
                        <div class="card p-3 border-0">
                            <h4>{{ strtoupper(str_replace('_', ' ', $type)) }}</h4>
                            <div class="w-50">
                                <label for="unit_{{ $type }}">Unit for {{ $type }}</label>
                                <input type="text"
                                    name="unit_{{ strtolower($type) }}"
                                    @foreach ($quarters as $quarter)
                                        @foreach ($years as $year)
                                            value="{{ old('unit_' . strtolower($type), $revenueData[$quarter][$year]['unit_' . strtolower($type)] ?? '') }}"
                                        @endforeach
                                    @endforeach
                                    placeholder="unit ex: (M / % / etc)" 
                                    class="form-control w-50">
                            </div>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Quarter</th>
                                        @foreach ($years as $year)
                                            <th>{{ $year }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($quarters as $quarter)
                                        <tr>
                                            <td>{{ $quarter }}</td>
                                            @foreach ($years as $year)
                                                <td>
                                                    <input type="text"
                                                        name="revenues[{{ $type }}][{{ $year }}][{{ $quarter }}]"
                                                        value="{{ old('revenues.' . $type . '.' . $year . '.' . $quarter, $revenueData[$quarter][$year][$type] ?? '-') }}"
                                                        class="form-control">
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach


                <!-- Financial Position Section -->
                <div class="row assets d-flex flex-wrap gap-4">
                    <p class="mt-4"><b>Financial Position Data</b></p>
                    <hr>
                    @foreach (['asset', 'liability', 'equality'] as $type)
                        <div class="col-md-5">
                            <div class="card p-3 border-0">
                                <h4>{{strtoupper(str_replace('_', ' ', $type))}}</h4>
                                <div class="w-50">
                                    <label for="unit_{{ $type }}">Unit for {{ $type }}</label>
                                    <input type="text"
                                        name="unit_{{ strtolower($type) }}"
                                        @foreach ($quarters as $quarter)
                                            @foreach ($years as $year)
                                                value="{{ old('unit_' . strtolower($type), $financialPositionData[$quarter]['unit_' . strtolower($type)][$year] ?? '') }}"
                                            @endforeach
                                        @endforeach
                                        placeholder="unit ex: (M / % / etc)" 
                                        class="form-control w-50">
                                </div>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Quarter</th>
                                            @foreach ($years as $year)
                                                <th>{{ $year }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($quarters as $quarter)
                                            <tr>
                                                <td>{{ $quarter }}</td>
                                                @foreach ($years as $year)
                                                    <td>
                                                        <input type="text"
                                                            name="financial_positions[{{ $type }}][{{ $year }}][{{ $quarter }}]"
                                                            value="{{ old('financial_positions.' . $type . '.' . $year . '.' . $quarter, $financialPositionData[$quarter][$type][$year] ?? '-') }}"
                                                            class="form-control">
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach

                    <!-- Dividend Section -->
                    <div class="row dividend d-flex flex-wrap">
                        <p class="mt-4"><b>Dividend Data</b></p>
                        <hr>
                        @foreach (['dividend_per_share', 'yield'] as $type)
                            <div class="col-md-6">
                                <div class="card p-3 border-0">
                                    <h4>{{ strtoupper(str_replace('_', ' ', $type)) }}</h4>
                                    <div class="w-50">
                                        <label for="unit_{{ $type }}">Unit for {{ $type }}</label>
                                        <input type="text"
                                            name="unit_{{ strtolower($type) }}"
                                            @foreach ($quarters as $quarter)
                                                @foreach ($years as $year)
                                                    value="{{ old('unit_' . strtolower($type), $dividendData[$quarter]['unit_' . strtolower($type)][$year] ?? '') }}"
                                                @endforeach
                                            @endforeach
                                            placeholder="unit ex: (M / % / etc)" 
                                            class="form-control w-50">
                                    </div>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Quarter</th>
                                                @foreach ($years as $year)
                                                    <th>{{ $year }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($quarters as $quarter)
                                                <tr>
                                                    <td>{{ $quarter }}</td>
                                                    @foreach ($years as $year)
                                                        <td>
                                                            <input type="text"
                                                                name="dividends[{{ $type }}][{{ $year }}][{{ $quarter }}]"
                                                                value="{{ old('dividends.' . $year . '.' . $quarter, $dividendData[$quarter][$type][$year] ?? '-') }}"
                                                                class="form-control">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4">
                        <button class="btn-custom2 btn" type="submit" class="btn btn-primary">Update Key
                            Statistics</button>
                    </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            function toggleColumns() {
                // Show or hide revenue
                if ($('#display_revenue').is(':checked')) {
                    $('.revenue').show();
                } else {
                    $('.revenue').hide();
                }

                // Show or hide gross profit
                if ($('#display_gross_profit').is(':checked')) {
                    $('.gross_profit').show();
                } else {
                    $('.gross_profit').hide();
                }

                // Show or hide net profit
                if ($('#display_net_profit').is(':checked')) {
                    $('.net_profit').show();
                } else {
                    $('.net_profit').hide();
                }

                // Show or hide assets
                if ($('#display_assets').is(':checked')) {
                    $('.assets').show();
                } else {
                    $('.assets').hide();
                }

                // Show or hide liabilities
                if ($('#display_liabilities').is(':checked')) {
                    $('.liabilities').show();
                } else {
                    $('.liabilities').hide();
                }

                // Show or hide dividends
                if ($('#display_dividends').is(':checked')) {
                    $('.dividends').show();
                } else {
                    $('.dividends').hide();
                }
            }

            // Call the function when the page loads
            toggleColumns();

            // Call the function whenever the user changes a checkbox
            $('input[type="checkbox"]').on('change', function() {
                toggleColumns();
            });
        });
    </script>

    @if ($errors->any())
        <script>
            let errorMessages = '';
            @foreach ($errors->all() as $error)
                errorMessages += '{{ $error }}<br>';
            @endforeach

            Swal.fire({
                title: 'Error!',
                html: errorMessages,
                icon: 'error',
                confirmButtonText: 'OK'
            })
        </script>
    @endif
    @if (session('success'))
        <script>
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'OK'
            })
        </script>
    @endif
@endsection
