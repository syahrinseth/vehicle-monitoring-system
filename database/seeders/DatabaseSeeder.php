<?php

namespace Database\Seeders;

use App\Models\DigitalSticker;
use App\Models\Registration;
use App\Models\Student;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Services\QRCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Vehicle Types ──────────────────────────────────────────
        $types = [
            ['name' => 'Motorcycle', 'code' => 'MOTO'],
            ['name' => 'Car',        'code' => 'CAR'],
            ['name' => 'Van',        'code' => 'VAN'],
            ['name' => 'Truck',      'code' => 'TRUCK'],
        ];
        foreach ($types as $type) {
            VehicleType::firstOrCreate(['code' => $type['code']], $type);
        }

        // ── Admin ──────────────────────────────────────────────────
        User::firstOrCreate(['email' => 'admin@vms.test'], [
            'name'     => 'System Admin',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'phone'    => '0111234567',
            'is_active' => true,
        ]);

        // ── Institute Authority ───────────────────────────────────
        User::firstOrCreate(['email' => 'authority@vms.test'], [
            'name'     => 'Prof. Dr. Ahmad',
            'password' => Hash::make('password'),
            'role'     => 'institute_authority',
            'phone'    => '0129876543',
            'is_active' => true,
        ]);

        // ── Guards ────────────────────────────────────────────────
        $guard = User::firstOrCreate(['email' => 'guard@vms.test'], [
            'name'     => 'Guard Hassan',
            'password' => Hash::make('password'),
            'role'     => 'guard',
            'phone'    => '0133456789',
            'is_active' => true,
        ]);

        // ── Student 1 (Approved with valid sticker) ───────────────
        $studentUser1 = User::firstOrCreate(['email' => 'student1@vms.test'], [
            'name'     => 'Ali bin Abu',
            'password' => Hash::make('password'),
            'role'     => 'student',
            'phone'    => '0174567890',
            'is_active' => true,
        ]);

        $student1 = Student::firstOrCreate(['user_id' => $studentUser1->id], [
            'matric_number'    => 'CS2024001',
            'phone'            => '0174567890',
            'gender'           => 'male',
            'ic_number'        => '020101123456',
            'address'          => 'Blok A, Asrama Bunga Raya',
            'emergency_contact' => '0112345678',
        ]);

        $vehicle1 = Vehicle::firstOrCreate(['registration_number' => 'WXY 1234'], [
            'student_id'      => $student1->id,
            'vehicle_type_id' => VehicleType::where('code', 'MOTO')->first()->id,
            'color'           => 'Black',
            'manufacturer'    => 'Honda',
            'model'           => 'EX5',
            'year'            => 2022,
        ]);

        $reg1 = Registration::firstOrCreate(
            ['student_id' => $student1->id, 'vehicle_id' => $vehicle1->id, 'status' => 'approved'],
            [
                'status'       => 'approved',
                'submitted_at' => now()->subDays(10),
                'verified_by'  => User::where('role', 'admin')->first()->id,
                'verified_at'  => now()->subDays(8),
                'approved_by'  => User::where('role', 'institute_authority')->first()->id,
                'approved_at'  => now()->subDays(7),
            ]
        );

        if (! $reg1->digitalSticker) {
            app(QRCodeService::class)->generateForRegistration(
                $reg1,
                now()->toDateString(),
                now()->addYear()->toDateString()
            );
        }

        // ── Student 2 (Pending registration) ─────────────────────
        $studentUser2 = User::firstOrCreate(['email' => 'student2@vms.test'], [
            'name'     => 'Siti binti Rahman',
            'password' => Hash::make('password'),
            'role'     => 'student',
            'phone'    => '0185678901',
            'is_active' => true,
        ]);

        $student2 = Student::firstOrCreate(['user_id' => $studentUser2->id], [
            'matric_number'    => 'CS2024002',
            'phone'            => '0185678901',
            'gender'           => 'female',
            'ic_number'        => '030202654321',
            'address'          => 'Blok B, Asrama Anggerik',
            'emergency_contact' => '0198765432',
        ]);

        $vehicle2 = Vehicle::firstOrCreate(['registration_number' => 'PQR 5678'], [
            'student_id'      => $student2->id,
            'vehicle_type_id' => VehicleType::where('code', 'CAR')->first()->id,
            'color'           => 'White',
            'manufacturer'    => 'Perodua',
            'model'           => 'Myvi',
            'year'            => 2021,
        ]);

        Registration::firstOrCreate(
            ['student_id' => $student2->id, 'vehicle_id' => $vehicle2->id],
            [
                'status'       => 'pending',
                'submitted_at' => now()->subDay(),
            ]
        );

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('Access URLs:');
        $this->command->info('  Admin Panel        → /admin');
        $this->command->info('  Student Portal     → /student');
        $this->command->info('  Guard Station      → /guard');
        $this->command->info('  Authority Panel    → /authority');
        $this->command->info('');
        $this->command->info('Test Credentials (password: password):');
        $this->command->info('  Admin              → admin@vms.test');
        $this->command->info('  Institute Authority→ authority@vms.test');
        $this->command->info('  Guard              → guard@vms.test');
        $this->command->info('  Student (approved) → student1@vms.test');
        $this->command->info('  Student (pending)  → student2@vms.test');
    }
}
