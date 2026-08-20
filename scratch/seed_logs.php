<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user_id = 1;

$activities = [
  ['category'=>'transportasi', 'activity_type'=>'motor', 'amount'=>15, 'unit'=>'km', 'co2_eq'=>1.8, 'co2_saved'=>0],
  ['category'=>'transportasi', 'activity_type'=>'sepeda', 'amount'=>5, 'unit'=>'km', 'co2_eq'=>0, 'co2_saved'=>1.2],
  ['category'=>'makanan', 'activity_type'=>'daging_sapi', 'amount'=>1, 'unit'=>'porsi', 'co2_eq'=>3.5, 'co2_saved'=>0],
  ['category'=>'makanan', 'activity_type'=>'sayuran', 'amount'=>1, 'unit'=>'porsi', 'co2_eq'=>0.5, 'co2_saved'=>2.0],
  ['category'=>'energi_listrik', 'activity_type'=>'ac', 'amount'=>4, 'unit'=>'jam', 'co2_eq'=>2.0, 'co2_saved'=>0]
];

for($i=1; $i<=10; $i++) {
  $act = $activities[array_rand($activities)];
  \App\Models\CarbonLog::create([
    'user_id' => $user_id,
    'category' => $act['category'],
    'activity_type' => $act['activity_type'],
    'amount' => $act['amount'],
    'unit' => $act['unit'],
    'co2_equivalent' => $act['co2_eq'],
    'co2_saved' => $act['co2_saved'],
    'points_earned' => rand(5,20),
    'xp_earned' => rand(10,50),
    'date' => now()->subDays($i)->toDateString()
  ]);
}

echo "10 Carbon logs generated.\n";
