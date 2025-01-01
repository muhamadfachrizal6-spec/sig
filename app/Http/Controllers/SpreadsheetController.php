<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\DividendData;
use App\Models\FinancialPositionData;
use App\Models\LiquidityRatioData;
use App\Models\MarketShare;
use App\Models\ProfitabilityRatioData;
use App\Models\RelativeRatioData;
use App\Models\RevenueData;
use SheetDB\SheetDB;

class SpreadsheetController extends Controller
{
    public function index()
    {
        try {
            $values = new SheetDB('6skh3cxgsnm88');
            $data = json_decode(json_encode($values->get()), true);

            // Lewati heaDER (baris pertama)
            $dataRows = array_slice($data, 1);

            $kodeSahamList = [];
            $dividendDataList = [];
            $financialPositionList = [];
            $liquidityRatioList = [];
            $profitabilityRatioList = [];
            $relativeRatioList = [];
            $marketShareList = [];
            $revenueDataList = [];
            $netProfitData = [];
            $priceData = [];

            // Fungsi untuk membersihkan data
            function cleanValue($value)
            {
                $value = str_replace('%', '', $value);  // Hapus tanda %
                $value = str_replace('.', '', $value);  // Hapus tanda .
                $value = str_replace(',', '', $value);  // Hapus tanda ,
                $value = str_replace('(', '', $value);  // Hapus (
                $value = str_replace(')', '', $value);  // Hapus )
                $value = str_replace(' ', '', $value);  // Hapus spasi
                return $value;
            }

            function cleanValueDecimal($value)
            {
                // Hapus tanda %, spasi, dan karakter tidak diperlukan
                $value = str_replace(['%', '(', ')', ' '], '', $value);

                // Ganti koma dengan titik untuk format desimal
                $value = str_replace(',', '.', $value);

                // Pastikan hanya angka atau desimal yang diambil
                return is_numeric($value) ? floatval($value) : 0;
            }

            foreach ($dataRows as $row) {
                // Ambil data DARi spreadsheet
                $kodeSaham = $row[2] ?? null;
                $marketCap = isset($row[44]) ? (is_numeric(cleanValue($row[44])) ? cleanValue($row[44]) : 0) : 0;

                $year = isset($row[3]) && is_numeric($row[3]) ? $row[3] : 0;
                $validQuarters = ['Q1', 'Q2', 'Q3', 'Q4'];
                $quarter = isset($row[4]) && in_array($row[4], $validQuarters) ? $row[4] : null;

                $dividendPerSheet = isset($row[39]) ? (is_numeric(cleanValue($row[39])) ? cleanValue($row[39]) / 100 : 0) : 0;
                $yield = isset($row[40]) ? cleanValueDecimal($row[40]) : 0;

                $asset = isset($row[7]) ? (is_numeric(cleanValue($row[7])) ? cleanValue($row[7]) : 0) : 0;
                $liability = isset($row[10]) ? (is_numeric(cleanValue($row[10])) ? cleanValue($row[10]) : 0) : 0;
                $equality = isset($row[13]) ? (is_numeric(cleanValue($row[13])) ? cleanValue($row[13]) : 0) : 0;

                $DAR = isset($row[41]) ? cleanValueDecimal($row[41]) : 0;
                $DER = isset($row[42]) ? cleanValueDecimal($row[42]) : 0;

                $ROE = isset($row[34]) ? cleanValueDecimal($row[34]) : 0;
                $GPM = isset($row[36]) ? cleanValueDecimal($row[36]) : 0;
                $NPM = isset($row[38]) ? cleanValueDecimal($row[38]) : 0;

                $EPS = isset($row[24]) ? cleanValueDecimal($row[24]) : 0;
                $PER = isset($row[25]) ? cleanValueDecimal($row[25]) : 0;
                $BVPS = isset($row[26]) ? (is_numeric(cleanValue($row[26])) ? cleanValue($row[26]) : 0) : 0;
                $PBV = isset($row[27]) ? cleanValueDecimal($row[27]) : 0;

                $revenue = isset($row[15]) ? (is_numeric(cleanValue($row[15])) ? cleanValue($row[15]) : 0) : 0;
                $netProfit = isset($row[19]) ? (is_numeric(cleanValue($row[19])) ? cleanValue($row[19]) : 0.0) : 0.0;
                $grossProfit = isset($row[17]) ? (is_numeric(cleanValue($row[17])) ? cleanValue($row[17]) : 0) : 0;

                $price = isset($row[23]) ? (is_numeric(cleanValue($row[23])) ? cleanValue($row[23]) : 0) : 0;

                // Menyimpan data netProfit per kode saham, tahun, dan kuartal
                if ($kodeSaham && $quarter) {
                    if (!isset($netProfitData[$kodeSaham][$year])) {
                        $netProfitData[$kodeSaham][$year] = [];
                    }
                    $netProfitData[$kodeSaham][$year][$quarter] = $netProfit;
                }
                if ($kodeSaham && $quarter) {
                    if (!isset($priceData[$kodeSaham][$year])) {
                        $priceData[$kodeSaham][$year] = [];
                    }
                    $priceData[$kodeSaham][$year][$quarter] = $price;
                }

                // Pastikan kode saham, year, dan quarter valid
                if ($kodeSaham) {
                    // Simpan ke kode saham list untuk data perusahaan
                    $kodeSahamList[$kodeSaham][] = [
                        'kode_saham' => $kodeSaham,
                        'market_cap' => $marketCap,
                        'year' => $year,
                        'quarter' => $quarter,

                        'dividend_per_sheet' => $dividendPerSheet,
                        'yield' => $yield,

                        'asset' => $asset,
                        'liability' => $liability,
                        'equality' => $equality,

                        'DAR' => $DAR,
                        'DER' => $DER,

                        'ROE' => $ROE,
                        'GPM' => $GPM,
                        'NPM' => $NPM,

                        'EPS' => $EPS,
                        'PER' => $PER,
                        'BVPS' => $BVPS,
                        'PBV' => $PBV,

                        'revenue' => $revenue,
                        'net_profit' => $netProfit,
                        'gross_profit' => $grossProfit,

                        'price' => $price
                    ];
                }
            }

            // Proses data DARi $kodeSahamList
            foreach ($kodeSahamList as $kodeSaham => $entries) {

                $company = Company::updateOrCreate(
                    ['ticker' => $kodeSaham],
                    [
                        'name' => $kodeSaham,
                        'category' => 'test',
                    ]
                );

                foreach ($entries as $entry) {
                    $year = $entry['year'];
                    $quarter = $entry['quarter'];
                    $netProfitCurrent = $entry['net_profit'];
                    $priceCurent = $entry['price'];

                    // Ambil net profit tahun sebelumnya untuk kuartal yang sama (misal Q3 2023 dan Q3 2022)
                    if (isset($netProfitData[$kodeSaham][$year - 1][$quarter])) {
                        $netProfitPrevious = $netProfitData[$kodeSaham][$year - 1][$quarter];
                        
                    } else {
                        $netProfitPrevious = 0;
                    }
                    // Ambil price tahun sebelumnya untuk kuartal yang sama (misal Q3 2023 dan Q3 2022)
                    if (isset($priceData[$kodeSaham][$year - 1][$quarter])) {
                        $pricePrevious = $priceData[$kodeSaham][$year - 1][$quarter];
                    } else {
                        $pricePrevious = 0;
                    }

                    // Periksa jika data net profit tahun sebelumnya ada dan bukan nol
                    if ($netProfitPrevious != 0.0) {
                        $growthNetProfit = ($netProfitCurrent - $netProfitPrevious) / abs($netProfitPrevious);
                        $formattedGrowthNetProfit = str_replace(',', '', number_format($growthNetProfit * 100, 2));
                    } else {
                        // Jika net profit tahun sebelumnya adalah 0 atau tidak ada data, set growth ke 0
                        $growthNetProfit = 0;
                        $formattedGrowthNetProfit = 0;
                    }

                    // Periksa jika data price tahun sebelumnya ada dan bukan nol
                    if ($pricePrevious != 0.0) {
                        $pricePopup = ($priceCurent - $pricePrevious) / abs($pricePrevious);
                        $formattedPricePopup = str_replace(',', '', number_format($pricePopup * 100, 2));
                    } else {
                        // Jika net profit tahun sebelumnya adalah 0 atau tidak ada data, set growth ke 0
                        $pricePopup = 0;
                        $formattedPricePopup = 0;
                    }

                    // Tambahkan data untuk disimpan ke dalam marketShareList
                    $marketShareList[] = [
                        'company_id' => $company->id,
                        'year' => $entry['year'],
                        'quarter' => $entry['quarter'],
                        'growth_net_profit' => $formattedGrowthNetProfit,
                        'price_popup' => $formattedPricePopup,
                        'price' => $entry['price'],
                        'market_cap' => $entry['market_cap'],
                    ];
                }

                // Loop setiap data untuk company_id ini
                foreach ($entries as $entry) {
                    $dividendDataList[] = [
                        'company_id' => $company->id,
                        'year' => $entry['year'],
                        'quarter' => $entry['quarter'],

                        'dividend_per_sheet' => $entry['dividend_per_sheet'],
                        'yield' => $entry['yield'],
                    ];
                }

                foreach ($entries as $entry) {
                    $financialPositionList[] = [
                        'company_id' => $company->id,
                        'year' => $entry['year'],
                        'quarter' => $entry['quarter'],

                        'asset' => $entry['asset'],
                        'liability' => $entry['liability'],
                        'equality' => $entry['equality'],
                    ];
                }

                foreach ($entries as $entry) {
                    $liquidityRatioList[] = [
                        'company_id' => $company->id,
                        'year' => $entry['year'],
                        'quarter' => $entry['quarter'],

                        'DAR' => $entry['DAR'],
                        'DER' => $entry['DER'],
                    ];
                }

                foreach ($entries as $entry) {
                    $profitabilityRatioList[] = [
                        'company_id' => $company->id,
                        'year' => $entry['year'],
                        'quarter' => $entry['quarter'],

                        'ROE' => $entry['ROE'],
                        'GPM' => $entry['GPM'],
                        'NPM' => $entry['NPM'],
                    ];
                }

                foreach ($entries as $entry) {
                    $relativeRatioList[] = [
                        'company_id' => $company->id,
                        'year' => $entry['year'],
                        'quarter' => $entry['quarter'],

                        'EPS' => $entry['EPS'],
                        'PER' => $entry['PER'],
                        'BVPS' => $entry['BVPS'],
                        'PBV' => $entry['PBV'],
                    ];
                }

                foreach ($entries as $entry) {
                    $revenueDataList[] = [
                        'company_id' => $company->id,
                        'year' => $entry['year'],
                        'quarter' => $entry['quarter'],

                        'revenue' => $entry['revenue'],
                        'net_profit' => $entry['net_profit'],
                        'gross_profit' => $entry['gross_profit'],
                    ];
                }
            }

            // Simpan batch data ke tabel `dividend_data`
            foreach ($dividendDataList as $dividend) {
                DividendData::updateOrCreate(
                    [
                        'company_id' => $dividend['company_id'],
                        'year' => $dividend['year'],
                        'quarter' => $dividend['quarter'],
                    ],
                    [
                        'dividend_per_sheet' => $dividend['dividend_per_sheet'],
                        'yield' => $dividend['yield'],
                    ]
                );
            }

            // Simpan batch data ke tabel `financial_position`
            foreach ($financialPositionList as $financialPosition) {
                FinancialPositionData::updateOrCreate(
                    [
                        'company_id' => $financialPosition['company_id'],
                        'year' => $financialPosition['year'],
                        'quarter' => $financialPosition['quarter'],
                    ],
                    [
                        'asset' => $financialPosition['asset'],
                        'liability' => $financialPosition['liability'],
                        'equality' => $financialPosition['equality'],
                    ]
                );
            }

            // Simpan batch data ke tabel `liquidity_ratio`
            foreach ($liquidityRatioList as $liquidityRatio) {
                LiquidityRatioData::updateOrCreate(
                    [
                        'company_id' => $liquidityRatio['company_id'],
                        'year' => $liquidityRatio['year'],
                        'quarter' => $liquidityRatio['quarter'],
                    ],
                    [
                        'DAR' => $liquidityRatio['DAR'],
                        'DER' => $liquidityRatio['DER'],
                    ]
                );
            }

            // Simpan batch data ke tabel `profitability_ratio`
            foreach ($profitabilityRatioList as $profitabilityRatio) {
                ProfitabilityRatioData::updateOrCreate(
                    [
                        'company_id' => $profitabilityRatio['company_id'],
                        'year' => $profitabilityRatio['year'],
                        'quarter' => $profitabilityRatio['quarter'],
                    ],
                    [
                        'ROE' => $profitabilityRatio['ROE'],
                        'GPM' => $profitabilityRatio['GPM'],
                        'NPM' => $profitabilityRatio['NPM'],
                    ]
                );
            }

            // Simpan batch data ke tabel `relative_ratio`
            foreach ($relativeRatioList as $relativeRatio) {
                RelativeRatioData::updateOrCreate(
                    [
                        'company_id' => $relativeRatio['company_id'],
                        'year' => $relativeRatio['year'],
                        'quarter' => $relativeRatio['quarter'],
                    ],
                    [
                        'EPS' => $relativeRatio['EPS'],
                        'PER' => $relativeRatio['PER'],
                        'BVPS' => $relativeRatio['BVPS'],
                        'PBV' => $relativeRatio['PBV'],
                    ]
                );
            }

            // Simpan batch data ke tabel `revenue`
            foreach ($revenueDataList as $revenue) {
                RevenueData::updateOrCreate(
                    [
                        'company_id' => $revenue['company_id'],
                        'year' => $revenue['year'],
                        'quarter' => $revenue['quarter'],
                    ],
                    [
                        'revenue' => $revenue['revenue'],
                        'net_profit' => $revenue['net_profit'],
                        'gross_profit' => $revenue['gross_profit'],
                    ]
                );
            }

            // Simpan batch data ke tabel `market_share`
            foreach ($marketShareList as $marketShare) {
                MarketShare::updateOrCreate(
                    [
                        'company_id' => $marketShare['company_id'],
                        'year' => $marketShare['year'],
                        'quarter' => $marketShare['quarter'],
                    ],
                    [
                        'growth_net_profit' => $marketShare['growth_net_profit'],
                        'price_popup' => $marketShare['price_popup'],
                        'price' => $marketShare['price'],
                        'market_cap' => $marketShare['market_cap'],
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan ke database',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}