<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = [
            [
                'company_name' => 'Square Pharmaceuticals Ltd.',
                'name' => 'Disprin',
                'generic_name' => 'Aspirin',
                'weight' => '500mg',
                'suggestion_price' => 4.50,
                'type' => 'tablet',
                'description' => 'Pain relief tablet for mild to moderate pain and fever.',
            ],
            [
                'company_name' => 'Square Pharmaceuticals Ltd.',
                'name' => 'Neurobion Forte',
                'generic_name' => 'Vitamin B1, B6, B12',
                'weight' => 'Tablet',
                'suggestion_price' => 16.00,
                'type' => 'tablet',
                'description' => 'Vitamin supplement for nerve health and energy.',
            ],
            [
                'company_name' => 'Incepta Pharmaceuticals Ltd.',
                'name' => 'Cataflam',
                'generic_name' => 'Diclofenac Potassium',
                'weight' => '50mg',
                'suggestion_price' => 9.00,
                'type' => 'tablet',
                'description' => 'Analgesic and anti-inflammatory medicine for pain relief.',
            ],
            [
                'company_name' => 'Incepta Pharmaceuticals Ltd.',
                'name' => 'Ciplen',
                'generic_name' => 'Ciprofloxacin',
                'weight' => '500mg',
                'suggestion_price' => 12.00,
                'type' => 'tablet',
                'description' => 'Broad-spectrum antibiotic for bacterial infections.',
            ],
            [
                'company_name' => 'Beximco Pharmaceuticals Ltd.',
                'name' => 'Napa',
                'generic_name' => 'Paracetamol',
                'weight' => '500mg',
                'suggestion_price' => 3.25,
                'type' => 'tablet',
                'description' => 'Common fever reducer and pain reliever.',
            ],
            [
                'company_name' => 'Beximco Pharmaceuticals Ltd.',
                'name' => 'Lexotanil',
                'generic_name' => 'Bromazepam',
                'weight' => '3mg',
                'suggestion_price' => 13.00,
                'type' => 'tablet',
                'description' => 'Anxiolytic used for nervous tension and anxiety.',
            ],
            [
                'company_name' => 'Renata Limited',
                'name' => 'Solapol',
                'generic_name' => 'Paracetamol',
                'weight' => '500mg',
                'suggestion_price' => 4.75,
                'type' => 'tablet',
                'description' => 'Fever and pain relief available in tablet form.',
            ],
            [
                'company_name' => 'Renata Limited',
                'name' => 'Gliclazide',
                'generic_name' => 'Gliclazide',
                'weight' => '80mg',
                'suggestion_price' => 11.00,
                'type' => 'tablet',
                'description' => 'Oral antidiabetic medication for type 2 diabetes.',
            ],
            [
                'company_name' => 'Eskayef Pharmaceuticals Ltd.',
                'name' => 'Fepraz',
                'generic_name' => 'Omeprazole',
                'weight' => '20mg',
                'suggestion_price' => 7.50,
                'type' => 'capsule',
                'description' => 'Gastric acid reducer for ulcers and reflux.',
            ],
            [
                'company_name' => 'Eskayef Pharmaceuticals Ltd.',
                'name' => 'Cefobid',
                'generic_name' => 'Cefuroxime',
                'weight' => '500mg',
                'suggestion_price' => 24.50,
                'type' => 'tablet',
                'description' => 'Cephalosporin antibiotic for bacterial infections.',
            ],
            [
                'company_name' => 'ACI Limited',
                'name' => 'Human Insulin',
                'generic_name' => 'Insulin Human',
                'weight' => '100IU/ml',
                'suggestion_price' => 125.00,
                'type' => 'injection',
                'description' => 'Insulin preparation for blood sugar control.',
            ],
            [
                'company_name' => 'ACI Limited',
                'name' => 'Sensigard',
                'generic_name' => 'Linezolid',
                'weight' => '600mg',
                'suggestion_price' => 95.00,
                'type' => 'tablet',
                'description' => 'Antibiotic for resistant gram-positive infections.',
            ],
            [
                'company_name' => 'Opsonin Pharma Ltd.',
                'name' => 'Advil',
                'generic_name' => 'Ibuprofen',
                'weight' => '400mg',
                'suggestion_price' => 5.25,
                'type' => 'tablet',
                'description' => 'Pain relief and anti-inflammatory medication.',
            ],
            [
                'company_name' => 'Opsonin Pharma Ltd.',
                'name' => 'Retrovir',
                'generic_name' => 'Zidovudine',
                'weight' => '300mg',
                'suggestion_price' => 48.00,
                'type' => 'capsule',
                'description' => 'Antiretroviral medicine for HIV treatment.',
            ],
            [
                'company_name' => 'Healthcare Pharmaceuticals Ltd.',
                'name' => 'CPAV',
                'generic_name' => 'Cefpodoxime Proxetil',
                'weight' => '200mg',
                'suggestion_price' => 21.50,
                'type' => 'tablet',
                'description' => 'Third-generation cephalosporin antibiotic.',
            ],
            [
                'company_name' => 'Healthcare Pharmaceuticals Ltd.',
                'name' => 'Cardace',
                'generic_name' => 'Ramipril',
                'weight' => '5mg',
                'suggestion_price' => 18.00,
                'type' => 'tablet',
                'description' => 'ACE inhibitor used to treat hypertension.',
            ],
            [
                'company_name' => 'ACME Laboratories Ltd.',
                'name' => 'Paracetamol',
                'generic_name' => 'Paracetamol',
                'weight' => '500mg',
                'suggestion_price' => 3.00,
                'type' => 'tablet',
                'description' => 'Common analgesic and antipyretic medicine.',
            ],
            [
                'company_name' => 'ACME Laboratories Ltd.',
                'name' => 'Doxycyclin',
                'generic_name' => 'Doxycycline',
                'weight' => '100mg',
                'suggestion_price' => 15.00,
                'type' => 'capsule',
                'description' => 'Tetracycline-group antibiotic for infections.',
            ],
            [
                'company_name' => 'Aristopharma Ltd.',
                'name' => 'Alphintern',
                'generic_name' => 'Paracetamol',
                'weight' => '500mg',
                'suggestion_price' => 4.00,
                'type' => 'tablet',
                'description' => 'Pain and fever relief combination.',
            ],
            [
                'company_name' => 'Aristopharma Ltd.',
                'name' => 'Ciplox',
                'generic_name' => 'Ciprofloxacin',
                'weight' => '500mg',
                'suggestion_price' => 13.50,
                'type' => 'tablet',
                'description' => 'Antibiotic for urinary and respiratory infections.',
            ],
            [
                'company_name' => 'Drug International Ltd.',
                'name' => 'Amlodac',
                'generic_name' => 'Amlodipine',
                'weight' => '5mg',
                'suggestion_price' => 12.00,
                'type' => 'tablet',
                'description' => 'Calcium channel blocker for hypertension.',
            ],
            [
                'company_name' => 'Drug International Ltd.',
                'name' => 'Cotrimoxazole',
                'generic_name' => 'Trimethoprim + Sulfamethoxazole',
                'weight' => '480mg',
                'suggestion_price' => 10.00,
                'type' => 'tablet',
                'description' => 'Broad-spectrum antibiotic for infections.',
            ],
            [
                'company_name' => 'General Pharmaceuticals Ltd.',
                'name' => 'Clexane',
                'generic_name' => 'Enoxaparin',
                'weight' => '40mg/ml',
                'suggestion_price' => 280.00,
                'type' => 'injection',
                'description' => 'Blood thinner injection used to prevent clotting.',
            ],
            [
                'company_name' => 'General Pharmaceuticals Ltd.',
                'name' => 'Diclogesic',
                'generic_name' => 'Diclofenac Sodium',
                'weight' => '50mg',
                'suggestion_price' => 8.25,
                'type' => 'tablet',
                'description' => 'Pain and inflammation relief medication.',
            ],
            [
                'company_name' => 'General Pharmaceuticals Ltd.',
                'name' => 'Dolo',
                'generic_name' => 'Paracetamol',
                'weight' => '500mg',
                'suggestion_price' => 3.00,
                'type' => 'tablet',
                'description' => 'Common analgesic and antipyretic medicine.',
            ],
            // 3 bion 
            [
                'company_name' => 'Orion Pharma Ltd.',
                'name' => 'Bion',
                'generic_name' => 'Paracetamol',
                'weight' => '500mg',
                'suggestion_price' => 3.00,
                'type' => 'tablet',
                'description' => 'Common analgesic and antipyretic medicine.',
            ],
            [
                'company_name' => 'Orion Pharma Ltd.',
                'name' => 'Bion Forte',
                'generic_name' => 'Paracetamol + Caffeine',
                'weight' => '500mg + 65mg',
                'suggestion_price' => 4.50,
                'type' => 'tablet',
                'description' => 'Pain relief tablet with added caffeine for enhanced effect.',
            ],
            [
                'company_name' => 'Orion Pharma Ltd.',
                'name' => 'Bion Extra',
                'generic_name' => 'Paracetamol + Caffeine + Codeine',
                'weight' => '500mg + 65mg + 8mg',
                'suggestion_price' => 6.00,
                'type' => 'tablet',
                'description' => 'Stronger pain relief tablet with codeine for severe pain.',
            ],
            [
                'company_name' => 'Beacon Pharmaceuticals Ltd.',
                'name' => 'Beacon',
                'generic_name' => 'Paracetamol',
                'weight' => '500mg',
                'suggestion_price' => 3.00,
                'type' => 'tablet',
                'description' => 'Common analgesic and antipyretic medicine.',
            ],
            [
                'company_name' => 'Beacon Pharmaceuticals Ltd.',
                'name' => 'Beacon Forte',
                'generic_name' => 'Paracetamol + Caffeine',
                'weight' => '500mg + 65mg',
                'suggestion_price' => 4.50,
                'type' => 'tablet',
                'description' => 'Pain relief tablet with added caffeine for enhanced effect.',
            ],
            [
                'company_name' => 'Beacon Pharmaceuticals Ltd.',
                'name' => 'Beacon Extra',
                'generic_name' => 'Paracetamol + Caffeine + Codeine',
                'weight' => '500mg + 65mg + 8mg',
                'suggestion_price' => 6.00,
                'type' => 'tablet',
                'description' => 'Stronger pain relief tablet with codeine for severe pain.',
            ],
            // alben-DS, ab-ds, abz, adze, ah, al-ds, alba, albamax ds, albazol ds 
            [
                'company_name' => 'Popular Pharmaceuticals Ltd.',
                'name' => 'Alben-DS',
                'generic_name' => 'Paracetamol',
                'weight' => '500mg',
                'suggestion_price' => 3.00,
                'type' => 'tablet',
                'description' => 'Common analgesic and antipyretic medicine.',
            ],
            [
                'company_name' => 'Popular Pharmaceuticals Ltd.',
                'name' => 'Ab-ds',
                'generic_name' => 'Paracetamol',
                'weight' => '500mg',
                'suggestion_price' => 3.00,
                'type' => 'tablet',
                'description' => 'Common analgesic and antipyretic medicine.',
            ],

        ];

        foreach ($medicines as $medicine) {
            $companyId = DB::table('medicine_companies')
                ->where('name', $medicine['company_name'])
                ->value('id');

            if (! $companyId) {
                continue;
            }

            $data = [
                'generic_name' => $medicine['generic_name'],
                'weight' => $medicine['weight'],
                'suggestion_price' => $medicine['suggestion_price'],
                'type' => $medicine['type'],
                'description' => $medicine['description'],
                'company_id' => $companyId,
                'updated_at' => now(),
            ];

            DB::table('medicines')->updateOrInsert(
                ['name' => $medicine['name'], 'company_id' => $companyId],
                array_merge($data, ['created_at' => now()])
            );
        }
    }
}
