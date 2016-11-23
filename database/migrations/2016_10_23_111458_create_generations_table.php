<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenerationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('generations', function(Blueprint $table)
		{
			$table->increments('id');
            $table->int('generation_id')->nullable();
            $table->string('type');
            $table->string('uploadUrl');
            $table->integer('adwords_batch_job_id');
			$table->integer('adwords_campaign_id');
			$table->integer('templategroupeannonce_id');
			$table->integer('feed_id');
			$table->integer('adwords_feed_id');
			$table->integer('operations_count');
			$table->string('status');
			$table->integer('completion_percentage');
			$table->timestamp('ended_at')->nullable();
			$table->integer('adgroups_count');
			$table->integer('spare_ads_count');
			$table->integer('customized_ads_count');
			$table->integer('keywords_count');
			$table->mediumText('errors');
			$table->boolean('enabled');
			$table->softDeletes();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('generations');
	}

}
