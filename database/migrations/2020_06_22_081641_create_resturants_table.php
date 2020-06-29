<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResturantsTable extends Migration {

	public function up()
	{
		Schema::create('resturants', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('name');
			$table->double('minimum_charge');
			$table->double('delivery_fees');
			$table->enum('status', array('open', 'closed'));
			$table->string('image');
			$table->integer('neighborhood_id')->unsigned();
			$table->string('email');
			$table->string('password');
			$table->string('delivery_time');
			$table->string('phone');
			$table->string('whatsapp');
			$table->string('api_token')->nullable()->unique();
			$table->string('pin_code')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('resturants');
	}
}
