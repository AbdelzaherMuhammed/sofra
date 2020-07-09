<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrdersTable extends Migration {

	public function up()
	{
		Schema::create('orders', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('address');
			$table->integer('payment_method_id')->unsigned();
			$table->decimal('cost');
			$table->decimal('total');
			$table->decimal('net');
			$table->decimal('commission');
			$table->enum('state', array('pending', 'accepted', 'delivered', 'rejected', 'declined'));
			$table->integer('client_id');
			$table->integer('resturant_id')->unsigned();
			$table->string('notes')->nullable();

		});
	}

	public function down()
	{
		Schema::drop('orders');
	}
}
