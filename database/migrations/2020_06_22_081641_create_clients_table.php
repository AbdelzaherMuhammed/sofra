<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClientsTable extends Migration {

	public function up()
	{
		Schema::create('clients', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('name');
			$table->string('phone');
			$table->string('email');
			$table->string('image');
			$table->string('password');
			$table->integer('neighborhood_id');
			$table->string('pin_code')->unique()->nullable();
			$table->string('api_token')->unique()->nullable();

		});
	}

	public function down()
	{
		Schema::drop('clients');
	}
}
