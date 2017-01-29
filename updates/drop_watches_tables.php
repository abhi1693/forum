<?php namespace Frontend\Forum\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class DropWatchesTables extends Migration
{
    public function up()
    {
        Schema::dropIfExists('forum_topic_watches');
        Schema::dropIfExists('forum_channel_watches');
    }

    public function down()
    {
        // ...
    }
}
