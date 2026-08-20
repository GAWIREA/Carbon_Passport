<?php

namespace App\Data;

class ActivityCatalog
{
    /**
     * Full catalog: category → activity_key → definition.
     *
     * Each definition:
     *   label       : Human-readable activity name (Indonesian)
     *   unit        : Standard measurement unit
     *   co2_per_unit: kg CO₂ per unit (for emission) or kg CO₂ saved per unit (for saving)
     *   type        : 'emission' | 'saving'
     *   limit       : Max plausible amount per day (anti-cheat)
     *
     * Emission factors sourced from:
     *   - IPCC 2006 / MEMR Indonesia 2023 (PLN grid: 0.87 kg CO₂/kWh)
     *   - Lemigas Indonesia (fuel combustion factors)
     *   - Poore & Nemecek 2018 (food LCA, Science)
     *   - DEFRA / ICAO (transport per passenger-km)
     *
     * @return array<string, array<string, array{label: string, unit: string, co2_per_unit: float, type: string, limit: float}>>
     */
    public static function all(): array
    {
        return [
            'transportasi' => [
                'mobil'    => ['label' => 'Perjalanan Mobil',          'unit' => 'km',    'co2_per_unit' => 0.21,  'type' => 'emission', 'limit' => 500],
                'motor'    => ['label' => 'Perjalanan Motor',          'unit' => 'km',    'co2_per_unit' => 0.10,  'type' => 'emission', 'limit' => 300],
                'bus'      => ['label' => 'Perjalanan Bus',            'unit' => 'km',    'co2_per_unit' => 0.05,  'type' => 'emission', 'limit' => 500],
                'truk'     => ['label' => 'Perjalanan Truk',           'unit' => 'km',    'co2_per_unit' => 0.62,  'type' => 'emission', 'limit' => 1000],
                'kapal'    => ['label' => 'Perjalanan Kapal/Perahu',   'unit' => 'km',    'co2_per_unit' => 0.19,  'type' => 'emission', 'limit' => 1000],
                'pesawat'  => ['label' => 'Perjalanan Pesawat',        'unit' => 'km',    'co2_per_unit' => 0.25,  'type' => 'emission', 'limit' => 20000],
            ],

            'energi' => [
                'listrik_pln'  => ['label' => 'Penggunaan Listrik PLN',   'unit' => 'kWh', 'co2_per_unit' => 0.87, 'type' => 'emission', 'limit' => 10000],
                'listrik_plts' => ['label' => 'Penggunaan Listrik PLTS',  'unit' => 'kWh', 'co2_per_unit' => 0.83, 'type' => 'saving',   'limit' => 5000],
                'genset'       => ['label' => 'Penggunaan Genset',         'unit' => 'kWh', 'co2_per_unit' => 0.65, 'type' => 'emission', 'limit' => 1000],
            ],

            'bahan_bakar' => [
                'solar'        => ['label' => 'Konsumsi Solar (Diesel)', 'unit' => 'liter', 'co2_per_unit' => 2.68, 'type' => 'emission', 'limit' => 10000],
                'bensin'       => ['label' => 'Konsumsi Bensin',         'unit' => 'liter', 'co2_per_unit' => 2.31, 'type' => 'emission', 'limit' => 5000],
                'lpg'          => ['label' => 'Konsumsi LPG',            'unit' => 'kg',    'co2_per_unit' => 2.98, 'type' => 'emission', 'limit' => 500],
                'minyak_tanah' => ['label' => 'Konsumsi Minyak Tanah',   'unit' => 'liter', 'co2_per_unit' => 2.40, 'type' => 'emission', 'limit' => 500],
            ],

            'limbah' => [
                'sampah_organik'  => ['label' => 'Sampah Organik',      'unit' => 'kg', 'co2_per_unit' => 0.25, 'type' => 'emission', 'limit' => 500],
                'sampah_plastik'  => ['label' => 'Sampah Plastik',      'unit' => 'kg', 'co2_per_unit' => 0.06, 'type' => 'emission', 'limit' => 500],
                'sampah_kertas'   => ['label' => 'Sampah Kertas',       'unit' => 'kg', 'co2_per_unit' => 0.13, 'type' => 'emission', 'limit' => 500],
                'sampah_landfill' => ['label' => 'Sampah ke Landfill',  'unit' => 'kg', 'co2_per_unit' => 0.50, 'type' => 'emission', 'limit' => 2000],
            ],

            'air' => [
                'air_bersih'  => ['label' => 'Konsumsi Air Bersih',        'unit' => 'm³', 'co2_per_unit' => 0.34, 'type' => 'emission', 'limit' => 10000],
                'air_limbah'  => ['label' => 'Air Limbah yang Diolah',     'unit' => 'm³', 'co2_per_unit' => 0.71, 'type' => 'emission', 'limit' => 5000],
            ],

            'energi_terbarukan' => [
                'plts'         => ['label' => 'Produksi Listrik PLTS',           'unit' => 'kWh', 'co2_per_unit' => 0.87, 'type' => 'saving', 'limit' => 50000],
                'pltmh'        => ['label' => 'Produksi Listrik PLTMH',          'unit' => 'kWh', 'co2_per_unit' => 0.87, 'type' => 'saving', 'limit' => 50000],
                'turbin_angin' => ['label' => 'Produksi Listrik Turbin Angin',   'unit' => 'kWh', 'co2_per_unit' => 0.87, 'type' => 'saving', 'limit' => 50000],
                'biogas'       => ['label' => 'Produksi Biogas',                 'unit' => 'm³',  'co2_per_unit' => 2.27, 'type' => 'saving', 'limit' => 1000],
            ],

            'makanan' => [
                'daging_merah'   => ['label' => 'Konsumsi Daging Merah',    'unit' => 'kg',    'co2_per_unit' => 27.0, 'type' => 'emission', 'limit' => 10],
                'unggas'         => ['label' => 'Konsumsi Unggas',          'unit' => 'kg',    'co2_per_unit' => 6.9,  'type' => 'emission', 'limit' => 10],
                'makanan_laut'   => ['label' => 'Konsumsi Makanan Laut',    'unit' => 'kg',    'co2_per_unit' => 6.1,  'type' => 'emission', 'limit' => 10],
                'produk_susu'    => ['label' => 'Konsumsi Produk Susu',     'unit' => 'liter', 'co2_per_unit' => 3.2,  'type' => 'emission', 'limit' => 20],
                'makanan_nabati' => ['label' => 'Konsumsi Makanan Nabati',  'unit' => 'kg',    'co2_per_unit' => 2.0,  'type' => 'emission', 'limit' => 20],
                'food_waste'     => ['label' => 'Food Waste',               'unit' => 'kg',    'co2_per_unit' => 2.5,  'type' => 'emission', 'limit' => 50],
            ],
        ];
    }

    /**
     * Get the catalog in a JS-friendly flat structure:
     * { category: { activity_key: { label, unit, co2_per_unit, type, limit } } }
     */
    public static function forJs(): array
    {
        return self::all();
    }

    /**
     * Get a specific activity definition, or null if not found.
     *
     * @return array{label: string, unit: string, co2_per_unit: float, type: string, limit: float}|null
     */
    public static function find(string $category, string $activityKey): ?array
    {
        return self::all()[$category][$activityKey] ?? null;
    }

    /**
     * Get all valid category keys.
     *
     * @return list<string>
     */
    public static function categoryKeys(): array
    {
        return array_keys(self::all());
    }

    /**
     * Get human-readable category labels with icons.
     *
     * @return array<string, string>
     */
    public static function categoryLabels(): array
    {
        return [
            'transportasi'       => '🚗 Transportasi',
            'energi'             => '⚡ Konsumsi Energi',
            'bahan_bakar'        => '⛽ Bahan Bakar',
            'limbah'             => '🗑️ Limbah',
            'air'                => '💧 Air',
            'energi_terbarukan'  => '🌱 Energi Terbarukan',
            'makanan'            => '🍽️ Makanan',
        ];
    }

    /**
     * Get all valid activity keys across all categories (for validation).
     *
     * @return list<string>
     */
    public static function allActivityKeys(): array
    {
        $keys = [];
        foreach (self::all() as $activities) {
            $keys = array_merge($keys, array_keys($activities));
        }

        return $keys;
    }

    /**
     * Get all activity keys that belong to a specific category (for validation).
     *
     * @return list<string>
     */
    public static function activityKeysForCategory(string $category): array
    {
        return array_keys(self::all()[$category] ?? []);
    }
}
