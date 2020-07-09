<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNotificationsTable extends Migration {

	public function up()
	{
		Schema::create('notifications', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('title');
			$table->string('content');
			$table->string('notifiable_type');
			$table->integer('notifiable_id');
			$table->integer('order_id');
			$table->tinyInteger('is_read')->default('0');
		});
	}

	public function down()
	{
		Schema::drop('notifications');
	}
}
