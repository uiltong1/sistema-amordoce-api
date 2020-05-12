<?php

use Illuminate\Database\Seeder;
use App\Vendas;
class Vendasseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Vendas::create([
            'numpedido' => '123',
            'id_cliente'=>'123',
            'data_pedido' => date("Y-m-d H:i:s"),
            'valor_compra' => '120.00',
        ]);
    }
}
