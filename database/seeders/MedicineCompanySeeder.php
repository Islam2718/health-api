<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicineCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            ['name' => 'Square Pharmaceuticals Ltd.'],
            ['name' => 'Incepta Pharmaceuticals Ltd.'],
            ['name' => 'Beximco Pharmaceuticals Ltd.'],
            ['name' => 'Renata Limited'],
            ['name' => 'Eskayef Pharmaceuticals Ltd.'],
            ['name' => 'ACI Limited'],
            ['name' => 'Opsonin Pharma Ltd.'],
            ['name' => 'Healthcare Pharmaceuticals Ltd.'],
            ['name' => 'ACME Laboratories Ltd.'],
            ['name' => 'Aristopharma Ltd.'],
            ['name' => 'Drug International Ltd.'],
            ['name' => 'General Pharmaceuticals Ltd.'],
            ['name' => 'Orion Pharma Ltd.'],
            ['name' => 'Beacon Pharmaceuticals Ltd.'],
            ['name' => 'Popular Pharmaceuticals Ltd.'],
            ['name' => 'Navana Pharmaceuticals Ltd.'],
            ['name' => 'Ibn Sina Pharmaceutical Industry Ltd.'],
            ['name' => 'Globe Pharmaceuticals Ltd.'],
            ['name' => 'Delta Pharma Ltd.'],
            ['name' => 'Ambee Pharmaceuticals Ltd.'],
            ['name' => 'Biopharma Ltd.'],
            ['name' => 'Novatek Pharmaceuticals Ltd.'],
            ['name' => 'Ad-din Pharmaceuticals Ltd.'],
            ['name' => 'Alco Pharma Ltd.'],
            ['name' => 'Apex Pharma Ltd.'],
            ['name' => 'Apollo Pharmaceutical Ltd.'],
            ['name' => 'Asiatic Laboratories Ltd.'],
            ['name' => 'Avarox Pharmaceuticals Ltd.'],
            ['name' => 'Aztec Pharmaceuticals Ltd.'],
            ['name' => 'Belsen Pharmaceuticals Ltd.'],
            ['name' => 'Bengal Drugs Ltd.'],
            ['name' => 'Bristol Pharmaceuticals Ltd.'],
            ['name' => 'Chemist Laboratories Ltd.'],
            ['name' => 'Cosmic Pharma Ltd.'],
            ['name' => 'Desh Pharmaceuticals Ltd.'],
            ['name' => 'Doctor Tims Pharmaceuticals Ltd.'],
            ['name' => 'Edruc Ltd.'],
            ['name' => 'Euro Pharma Ltd.'],
            ['name' => 'Everest Pharmaceuticals Ltd.'],
            ['name' => 'Fresenius Kabi Bangladesh Ltd.'],
            ['name' => 'Gaco Pharmaceuticals Ltd.'],
            ['name' => 'GlaxoSmithKline Bangladesh Ltd.'],
            ['name' => 'Hallmark Pharmaceuticals Ltd.'],
            ['name' => 'Hamdard Laboratories (Waqf) Bangladesh'],
            ['name' => 'Hudson Pharmaceuticals Ltd.'],
            ['name' => 'Jayson Pharmaceuticals Ltd.'],
            ['name' => 'Kemiko Pharmaceuticals Ltd.'],
            ['name' => 'Kumudini Pharma Ltd.'],
            ['name' => 'Labaid Pharma Ltd.'],
            ['name' => 'Libra Pharmaceuticals Ltd.'],
            ['name' => 'Medicon Pharmaceuticals Ltd.'],
            ['name' => 'Medimet Pharmaceuticals Ltd.'],
            ['name' => 'Millat Pharmaceuticals Ltd.'],
            ['name' => 'Modern Pharmaceuticals Ltd.'],
            ['name' => 'Mystic Pharmaceuticals Ltd.'],
            ['name' => 'NIPRO JMI Pharma Ltd.'],
            ['name' => 'Novo Healthcare and Pharma Ltd.'],
            ['name' => 'One Pharma Ltd.'],
            ['name' => 'Organic Health Care Ltd.'],
            ['name' => 'Pacific Pharmaceuticals Ltd.'],
            ['name' => 'Pharmasia Ltd.'],
            ['name' => 'Premier Pharmaceuticals Ltd.'],
            ['name' => 'Radiant Pharmaceuticals Ltd.'],
            ['name' => 'Reman Drug Laboratories Ltd.'],
            ['name' => 'Rephco Pharmaceuticals Ltd.'],
            ['name' => 'Rangs Pharmaceuticals Ltd.'],
            ['name' => 'Sandoz Bangladesh Ltd.'],
            ['name' => 'Sanofi Bangladesh Ltd.'],
            ['name' => 'Sharif Pharmaceuticals Ltd.'],
            ['name' => 'Silco Pharmaceuticals Ltd.'],
            ['name' => 'Silva Pharmaceuticals Ltd.'],
            ['name' => 'Skylab Pharmaceuticals Ltd.'],
            ['name' => 'Somatec Pharmaceuticals Ltd.'],
            ['name' => 'Standard Laboratories Ltd.'],
            ['name' => 'Sun Pharmaceutical (Bangladesh) Ltd.'],
            ['name' => 'Team Pharmaceuticals Ltd.'],
            ['name' => 'Techno Drugs Ltd.'],
            ['name' => 'The White Horse Pharmaceuticals Ltd.'],
            ['name' => 'Unimed Unihealth Pharmaceuticals Ltd.'],
            ['name' => 'United Pharmaceuticals Ltd.'],
            ['name' => 'Zenith Pharmaceuticals Ltd.'],
            ['name' => 'Ziska Pharmaceuticals Ltd.'],
            ['name' => 'Aranya Pharmaceuticals Ltd.'],
            ['name' => 'Bio-Pharma Laboratories Ltd.'],
            ['name' => 'Central Pharmaceuticals Ltd.'],
            ['name' => 'Crystal Pharmaceuticals Ltd.'],
            ['name' => 'Doctor’s Chemicals Works Ltd.'],
            ['name' => 'Eastern Pharmaceuticals Ltd.'],
            ['name' => 'Elixir Pharmaceuticals Ltd.'],
            ['name' => 'Essential Drugs Company Ltd.'],
            ['name' => 'Goodman Pharmaceuticals Ltd.'],
            ['name' => 'Greenland Pharmaceuticals Ltd.'],
            ['name' => 'Helios Pharmaceuticals Ltd.'],
            ['name' => 'JMI Syringes & Medical Devices Ltd.'],
            ['name' => 'Marksman Pharmaceutical Ltd.'],
            ['name' => 'Northern Pharmaceuticals Ltd.'],
            ['name' => 'Oyster Pharmaceuticals Ltd.'],
            ['name' => 'Peoples Pharma Ltd.'],
            ['name' => 'Prime Pharmaceuticals Ltd.'],
            ['name' => 'Rasa Pharmaceuticals Ltd.'],
            ['name' => 'Rupali Pharmaceuticals Ltd.'],
            ['name' => 'Shah Pharmaceuticals Ltd.'],
            ['name' => 'Star Pharmaceuticals Ltd.'],
            ['name' => 'Sun Pharma Advanced Research Company Ltd.'],
            ['name' => 'Tata Pharmaceuticals Ltd.'],
            ['name' => 'United Biotech Ltd.'],
            ['name' => 'Zenith Pharmaceuticals Ltd.'],            
        ];

        foreach ($companies as $company) {
            $existing = DB::table('medicine_companies')
                ->where('name', $company['name'])
                ->first();

            $data = [
                'logo' => null,
                'address' => null,
                'license_number' => null,
                'about' => null,
                'updated_at' => now(),
            ];

            if ($existing) {
                DB::table('medicine_companies')
                    ->where('name', $company['name'])
                    ->update($data);
            } else {
                DB::table('medicine_companies')
                    ->insert(array_merge($company, $data, ['created_at' => now()]));
            }
        }
    }
}
