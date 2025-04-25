<div>
    <div class="row d-flex g-3">
        <div class="col-lg-6 d-flex">
            <div class="card p-3 border-2 w-100 #43654C shadow">
                <h4 class="primary-color-text">Company Profile</h4>
                <div
                    class="header-company d-flex flex-row gap-4 p-3 align-items-center rounded border primary-border shadow">
                    <img src="{{ asset($company->logo ?: 'assets/img/logo/sig2.png') }}" alt="Logo {{ $company->name }}"
                        class="rounded border primary-border" width="70" height="70">
                    <div class="category d-flex flex-column justify-content-center text-center">
                        <p class="primary-color-text text-start"><b>{{ $company->name }}</b></p>
                        <div class="category-container">
                            @if ($company->category == 'Auto')
                                <span class="category-item auto">
                                    <i class="fas fa-car"></i> Auto
                                </span>
                            @elseif ($company->category == 'Tech')
                                <span class="category-item tech">
                                    <i class="fas fa-microchip"></i> Tech
                                </span>
                            @else
                                <span class="category-item energy">
                                    <i class="fas fa-microchip"></i> {{ $company->category }}
                                </span>
                            @endif
                        </div>

                    </div>
                </div>
                <div class="company-info container-fluid p-3">
                    <div class="row mb-2">
                        <div class="col-12">
                            <p class="primary-color-text fw-bold">Address</p>
                            <p>{{ $company->address }}</p>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-6">
                            <p class="primary-color-text fw-bold">Market Cap</p>
                            <p>Rp.{{ number_format($marketCap, 2, ',', '.') }} Milyards</p>
                        </div>
                        <div class="col-6">
                            <p class="primary-color-text fw-bold">Price</p>
                            <p>Rp.{{ number_format($price, 2, ',', '.') }} Milyards</p>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-6">
                            <p class="primary-color-text fw-bold">Company code</p>
                            <p>{{ $company->ticker }}</p>
                        </div>
                        <div class="col-6">
                            <p class="primary-color-text fw-bold">Growth Net Profit (YoY)</p>
                            <p>{{ $growthNetProfit ?? '-' }}%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 d-flex">
            <div class="card p-3 border-2 w-100 #43654C shadow">
                <h4 class="primary-color-text align-self-start">Description</h4>
                <div class="header-company d-flex flex-column gap-4 p-3 align-items-center rounded border primary-border shadow">
                    <p class="text-justify">{!! nl2br(e($company->description ?? 'Description not available.')) !!}</p>
                </div>
            </div>
        </div>
    </div>

    <p class="mt-4"><b>Income Statement</b></p>
    <hr>

    <div class="row row-cols-xl-3 row-cols-md-2 row-cols-sm-1 g-3">
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card p-3 border-2 #43654C">
                <h4 class="primary-color-text">Revenue</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            @foreach ($years as $year)
                                <th>{{ $year }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($revenueData as $quarter => $revenues)
                            <tr>
                                <td>{{ $quarter }}</td>
                                @foreach ($years as $year)
                                    <td>{{ isset($revenues['revenue'][$year]) ? $revenues['revenue'][$year] : '-' }} {{ $revenues['unit_revenue'][$year]}}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card p-3 border-2 #43654C">
                <h4 class="primary-color-text">Gross Profit</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            @foreach ($years as $year)
                                <th>{{ $year }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($revenueData as $quarter => $revenues)
                            <tr>
                                <td>{{ $quarter }}</td>
                                @foreach ($years as $year)
                                    <td>{{ isset($revenues['gross_profit'][$year]) ? $revenues['gross_profit'][$year] : '-' }} {{ $revenues['unit_gross_profit'][$year]}}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card p-3 border-2 #43654C">
                <h4 class="primary-color-text">Net Profit</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            @foreach ($years as $year)
                                <th>{{ $year }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($revenueData as $quarter => $revenues)
                            <tr>
                                <td>{{ $quarter }}</td>
                                @foreach ($years as $year)
                                    <td>{{ isset($revenues['net_profit'][$year]) ? $revenues['net_profit'][$year] : '-' }} {{ $revenues['unit_net_profit'][$year]}}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <p class="mt-4"><b>Financial Position</b></p>
    <hr>

    <div class="row row-cols-xl-3 row-cols-md-2 row-cols-sm-1 g-3">
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card p-3 border-2 #43654C">
                <h4 class="primary-color-text">Asset</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            @foreach ($years as $year)
                                <th>{{ $year }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($financialPositionData as $quarter => $positions)
                            <tr>
                                <td>{{ $quarter }}</td>
                                @foreach ($years as $year)
                                    <td>{{ isset($positions['asset'][$year]) ? $positions['asset'][$year] : '-' }} {{ $positions['unit_asset'][$year]}}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card p-3 border-2 #43654C">
                <h4 class="primary-color-text">Liability</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            @foreach ($years as $year)
                                <th>{{ $year }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($financialPositionData as $quarter => $positions)
                            <tr>
                                <td>{{ $quarter }}</td>
                                @foreach ($years as $year)
                                    <td>{{ isset($positions['liability'][$year]) ? $positions['liability'][$year] : '-' }} {{ $positions['unit_liability'][$year]}}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card p-3 border-2 #43654C">
                <h4 class="primary-color-text">Equity</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            @foreach ($years as $year)
                                <th>{{ $year }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($financialPositionData as $quarter => $positions)
                            <tr>
                                <td>{{ $quarter }}</td>
                                @foreach ($years as $year)
                                    <td>{{ isset($positions['equality'][$year]) ? $positions['equality'][$year] : '-' }} {{ $positions['unit_equality'][$year]}}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <p class="mt-4"><b>Dividend</b></p>
    <hr>

    <div class="row row-cols-xl-3 row-cols-md-2 row-cols-sm-1 g-3">
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card p-3 border-2 #43654C">
                <h4 class="primary-color-text">Dividend per Share</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            @foreach ($years as $year)
                                <th>{{ $year }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dividendData as $quarter => $dividends)
                            <tr>
                                <td>{{ $quarter }}</td>
                                @foreach ($years as $year)
                                    <td>{{ isset($dividends['dividend_per_share'][$year]) ? $dividends['dividend_per_share'][$year] : '-' }} {{ $dividends['unit_dividend_per_share'][$year]}}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card p-3 border-2 #43654C">
                <h4 class="primary-color-text">Yield</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            @foreach ($years as $year)
                                <th>{{ $year }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dividendData as $quarter => $dividends)
                            <tr>
                                <td>{{ $quarter }}</td>
                                @foreach ($years as $year)
                                    <td>{{ isset($dividends['yield'][$year]) ? $dividends['yield'][$year] : '-' }} {{ $dividends['unit_yield'][$year]}}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <p class="mt-4"><b>Profitability Ratio</b></p>
    <hr>

    <div class="row row-cols-xl-3 row-cols-md-2 row-cols-sm-1 g-3">
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card p-3 border-2 #43654C">
                <h4 class="primary-color-text">ROE</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            @foreach ($years as $year)
                                <th>{{ $year }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($profitabilityRatioData as $quarter => $ratios)
                            <tr>
                                <td>{{ $quarter }}</td>
                                @foreach ($years as $year)
                                    <td>{{ isset($ratios['ROE'][$year]) ? $ratios['ROE'][$year] : '-' }} {{ $ratios['unit_roe'][$year]}}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card p-3 border-2 #43654C">
                <h4 class="primary-color-text">GPM</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            @foreach ($years as $year)
                                <th>{{ $year }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($profitabilityRatioData as $quarter => $ratios)
                            <tr>
                                <td>{{ $quarter }}</td>
                                @foreach ($years as $year)
                                    <td>{{ isset($ratios['GPM'][$year]) ? $ratios['GPM'][$year] : '-' }} {{ $ratios['unit_gpm'][$year]}}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card p-3 border-2 #43654C">
                <h4 class="primary-color-text">NPM</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            @foreach ($years as $year)
                                <th>{{ $year }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($profitabilityRatioData as $quarter => $ratios)
                            <tr>
                                <td>{{ $quarter }}</td>
                                @foreach ($years as $year)
                                    <td>{{ isset($ratios['NPM'][$year]) ? $ratios['NPM'][$year] : '-' }} {{ $ratios['unit_npm'][$year]}}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <p class="mt-4"><b>Relative Ratio</b></p>
    <hr>

    <div class="row row-cols-xl-3 row-cols-md-2 row-cols-sm-1 g-3">
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card p-3 border-2 #43654C">
                <h4 class="primary-color-text">EPS</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            @foreach ($years as $year)
                                <th>{{ $year }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($relativeRatioData as $quarter => $ratios)
                            <tr>
                                <td>{{ $quarter }}</td>
                                @foreach ($years as $year)
                                    <td>{{ isset($ratios['EPS'][$year]) ? $ratios['EPS'][$year] : '-' }} {{ $ratios['unit_eps'][$year]}}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card p-3 border-2 #43654C">
                <h4 class="primary-color-text">PER</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            @foreach ($years as $year)
                                <th>{{ $year }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($relativeRatioData as $quarter => $ratios)
                            <tr>
                                <td>{{ $quarter }}</td>
                                @foreach ($years as $year)
                                    <td>{{ isset($ratios['PER'][$year]) ? $ratios['PER'][$year] : '-' }} {{ $ratios['unit_per'][$year]}}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card p-3 border-2 #43654C">
                <h4 class="primary-color-text">BVPS</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            @foreach ($years as $year)
                                <th>{{ $year }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($relativeRatioData as $quarter => $ratios)
                            <tr>
                                <td>{{ $quarter }}</td>
                                @foreach ($years as $year)
                                    <td>{{ isset($ratios['BVPS'][$year]) ? $ratios['BVPS'][$year] : '-' }} {{ $ratios['unit_bvps'][$year]}}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card p-3 border-2 #43654C">
                <h4 class="primary-color-text">PBV</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            @foreach ($years as $year)
                                <th>{{ $year }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($relativeRatioData as $quarter => $ratios)
                            <tr>
                                <td>{{ $quarter }}</td>
                                @foreach ($years as $year)
                                    <td>{{ isset($ratios['PBV'][$year]) ? $ratios['PBV'][$year] : '-' }} {{ $ratios['unit_pbv'][$year]}}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <p class="mt-4"><b>Liquidity Ratio</b></p>
    <hr>

    <div class="row row-cols-xl-3 row-cols-md-2 row-cols-sm-1 g-3">
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card p-3 border-2 #43654C">
                <h4 class="primary-color-text">DAR</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            @foreach ($years as $year)
                                <th>{{ $year }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($liquidityRatioData as $quarter => $ratios)
                            <tr>
                                <td>{{ $quarter }}</td>
                                @foreach ($years as $year)
                                    <td>{{ isset($ratios['DAR'][$year]) ? $ratios['DAR'][$year] : '-' }} {{ $ratios['unit_dar'][$year]}}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card p-3 border-2 #43654C">
                <h4 class="primary-color-text">DER</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            @foreach ($years as $year)
                                <th>{{ $year }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($liquidityRatioData as $quarter => $ratios)
                            <tr>
                                <td>{{ $quarter }}</td>
                                @foreach ($years as $year)
                                    <td>{{ isset($ratios['DER'][$year]) ? $ratios['DER'][$year] : '-' }} {{ $ratios['unit_der'][$year]}}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
