<?php

namespace Database\Seeders;

use App\Models\Collector;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CollectorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // MANILA AREA
        //MA1-MA8
        Collector::create([
            'fullname' => 'Ferdinand Medina',
            'email' => 'ferdinandmedina@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);

        Collector::create([
            'fullname' => 'Erickson Pomaren',
            'email' => 'ericksonpomaren@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Christian Pinca',
            'email' => 'christianpinca@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Jason Policarpio',
            'email' => 'jasonpolicarpio@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Carlo Taperla',
            'email' => 'carlotaperla@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Boy Cerbito',
            'email' => 'boycerbito@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Benje Tamayo',
            'email' => 'benjetamayo@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Patrick Lanuza',
            'email' => 'patricklanuza@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        // END MA1-MA8

        // VALENZUELA AREA
        Collector::create([
            'fullname' => 'James Ojeda',
            'email' => 'jamesojeda@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Mharlson Tupaz',
            'email' => 'mharlsontupaz@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Manolito Merabite',
            'email' => 'manolitomerabite@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Kenneth Dayro',
            'email' => 'kennethdayro@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);

        Collector::create([
            'fullname' => 'Adrian Marcial',
            'email' => 'adrianmarcial@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);

        Collector::create([
            'fullname' => 'Gerson Reyes',
            'email' => 'gersonreyes@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Rodel Valenzuela',
            'email' => 'rodelvalenzuela@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);

        // END VALENZUELA AREA

        // CALOOCAN AREA
        Collector::create([
            'fullname' => 'Aeron James Lipan',
            'email' => 'aeronjameslipan@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Lynnard Harvey Medina',
            'email' => 'lynnardharveymedina@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Kristoffer Ki',
            'email' => 'kristofferki@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Jordan Hibo',
            'email' => 'jordanhibo@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);

        Collector::create([
            'fullname' => 'Jesus Napiza',
            'email' => 'jesusnapiza@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);

        Collector::create([
            'fullname' => 'Aldron Paulo Rañeses',
            'email' => 'aldronpaulo@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Floyd Tumandao',
            'email' => 'floydtumandao@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);

        Collector::create([
            'fullname' => 'Raby De Asis',
            'email' => 'rabydeasis@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        // END CALOOCAN AREA

        // FC
        Collector::create([
            'fullname' => 'Ronel Bravo',
            'email' => 'ronelbravo@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Rexter Honolario',
            'email' => 'rexterhonolario@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Norwel Vero',
            'email' => 'norwelvero@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Kenneth Acquin',
            'email' => 'kennethacquin@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);

        Collector::create([
            'fullname' => 'Bon Jove Flore',
            'email' => 'bonjoveflore@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);

        Collector::create([
            'fullname' => 'Ramoncito Enriquez',
            'email' => 'ramoncitoenriquez@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'John Michael',
            'email' => 'johnmichael@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'collector',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
    }
}
