<?php

namespace Database\Seeders;

use App\Models\Collector;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            'created_by' => 'Admin',
            'updated_by' => null
        ]);

        Collector::create([
            'fullname' => 'Erickson Pomaren',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Christian Pinca',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Jason Policarpio',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Carlo Taperla',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Boy Cerbito',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Benje Tamayo',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Patrick Lanuza',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        // END MA1-MA8

        // VALENZUELA AREA
        Collector::create([
            'fullname' => 'James Ojeda',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Mharlson Tupaz',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Manolito Merabite',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Kenneth Dayro',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);

        Collector::create([
            'fullname' => 'Adrian Marcial',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);

        Collector::create([
            'fullname' => 'Gerson Reyes',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Rodel Valenzuela',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);

        // END VALENZUELA AREA

        // CALOOCAN AREA
        Collector::create([
            'fullname' => 'Aeron James Lipan',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Lynnard Harvey Medina',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Kristoffer Ki',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Jordan Hibo',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);

        Collector::create([
            'fullname' => 'Jesus Napiza',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);

        Collector::create([
            'fullname' => 'Aldron Paulo Rañeses',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Floyd Tumandao',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);

        Collector::create([
            'fullname' => 'Raby De Asis',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        // END CALOOCAN AREA

        // FC
        Collector::create([
            'fullname' => 'Ronel Bravo',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Rexter Honolario',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Norwel Vero',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'Kenneth Acquin',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);

        Collector::create([
            'fullname' => 'Bon Jove Flore',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);

        Collector::create([
            'fullname' => 'Ramoncito Enriquez',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);
        Collector::create([
            'fullname' => 'John Michael',
            'created_by' => 'Admin',
            'updated_by' => null
        ]);    
    }
}
