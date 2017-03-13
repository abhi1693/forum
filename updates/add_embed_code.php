<?php namespace Frontend\Forum\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddEmbedCode extends Migration
{
    public function up()
    {
        Schema::table('forum_channels', function($table)
        {
            $table->string('embed_code')->nullable()->index();
        });

        Schema::table('forum_topics', function($table)
        {
            $table->string('embed_code')->nullable()->index();
        });
    }

    public function down()
    {
        Schema::table('forum_channels', function($table)
        {
            $table->dropColumn('embed_code');
        });

        Schema::table('forum_topics', function($table)
        {
            $table->dropColumn('embed_code');
        });
    }
}
