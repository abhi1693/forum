<?php namespace Frontend\Forum\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateTopicWatchesTable extends Migration
{
    public function up()
    {
        Schema::create('forum_topic_watches', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('topic_id')->unsigned()->index()->nullable();
            $table->integer('member_id')->unsigned()->index()->nullable();
            $table->integer('count_posts')->index()->default(0);
            $table->dateTime('watched_at')->index();
        });
    }

    public function down()
    {
        Schema::dropIfExists('forum_topic_watches');
    }
}
