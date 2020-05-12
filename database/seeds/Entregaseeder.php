<?php

use Illuminate\Database\Seeder;
use App\Entrega;
class Entregaseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Entrega::create([
            'placa' => 'xxxx123',
            'motorista'=>'Carlos',
            'data_pedido' => '20-10-2019',
            'observacao' => 'Teste',
            'status'=>'A'
        ]);
    }
}
