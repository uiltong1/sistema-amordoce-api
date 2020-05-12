<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    $senha  = Hash::make('1234');
        User::create([
            'name' => 'Administrador',
            'user'=>'administrador',
            'password'=>$senha
        ]);
    }
}
