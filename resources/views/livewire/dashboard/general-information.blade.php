<div>
    <div class="row d-flex g-3">
        <div class="col-lg-6 d-flex">
            <div class="card p-3 border-2 w-100 #43654C shadow">
                <h4 class="primary-color-text">Company Profile</h4>
                <div class="header-company d-flex flex-row gap-4 p-3 align-items-center rounded border primary-border shadow">
                    <img src="{{ asset('assets/img/logo/sig2.png') }}" alt="Profile Image"
                        class="rounded border primary-border" width="50" height="50">
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
                            <p class="primary-color-text fw-bold">Volume Rata-rata</p>  
                            <p>test Jt</p>  
                        </div>  
                        <div class="col-6">  
                            <p class="primary-color-text fw-bold">Growth Net (YoY)</p>  
                            <p>{{ $growthNetProfit ?? '-' }}%</p>  
                        </div>  
                    </div>  
                </div>  
            </div>
        </div>
        <div class="col-lg-6 d-flex">
            <div class="card p-3 border-2 w-100 #43654C shadow">
                <h4 class="primary-color-text">Description</h4>
                <p>{{ $company->description ?? 'Description not available.' }}</p>
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
                                    <td>{{ number_format($revenues[$year], 0, '', '.') ?? '-' }} M</td>
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
                        @foreach ($grossProfitData as $quarter => $profits)
                            <tr>
                                <td>{{ $quarter }}</td>
                                @foreach ($years as $year)
                                    <td>{{ number_format($profits[$year], 0, '', '.') ?? '-' }} M</td>
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
                        @foreach ($netProfitData as $quarter => $profits)
                            <tr>
                                <td>{{ $quarter }}</td>
                                @foreach ($years as $year)
                                    <td>{{ number_format($profits[$year], 0, '', '.') ?? '-' }} M</td>
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
                                    <td>{{ isset($positions['asset'][$year]) ? number_format($positions['asset'][$year], 0, '', '.') : '-' }}
                                        M</td>
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
                                    <td>{{ isset($positions['liability'][$year]) ? number_format($positions['liability'][$year], 0, '', '.') : '-' }}
                                        M</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card p-3 border-2 #43654C">
                <h4 class="primary-color-text">Equality</h4>
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
                                    <td>{{ isset($positions['equality'][$year]) ? number_format($positions['equality'][$year], 0, '', '.') : '-' }}
                                        M</td>
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
                <h4 class="primary-color-text">Dividend per Sheet</h4>
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
                                    <td>{{ $dividends['dividend_per_sheet'][$year] ?? '-'  }}%</td>
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
                                    <td>{{ $dividends['yield'][$year] ?? '-'  }}%</td>
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
                                    <td>{{ $ratios['ROE'][$year] ?? '-' }}%</td>
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
                                    <td>{{ $ratios['GPM'][$year] ?? '-' }}%</td>
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
                                    <td>{{ $ratios['NPM'][$year] ?? '-' }}%</td>
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
                                    <td>{{ $ratios['EPS'][$year] ?? '-' }}</td>
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
                                    <td>{{ $ratios['PER'][$year] ?? '-' }}</td>
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
                                    <td>{{ $ratios['BVPS'][$year] ?? '-' }}</td>
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
                                    <td>{{ $ratios['PBV'][$year] ?? '-' }}</td>
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
                                    <td>{{ $ratios['DAR'][$year] ?? '-' }}%</td>
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
                                    <td>{{ $ratios['DER'][$year] ?? '-' }}%</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
