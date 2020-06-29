<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSettingsTable extends Migration {

	public function up()
	{
		Schema::create('settings', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('about_app');
			$table->string('commission_text');
			$table->decimal('commission');
		});
	}

	public function down()
	{
		Schema::drop('settings');
	}
}