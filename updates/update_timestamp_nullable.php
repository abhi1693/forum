<?php namespace Frontend\Forum\Updates;

use October\Rain\Database\Updates\Migration;
use DbDongle;

class UpdateTimestampsNullable extends Migration
{
    public function up()
    {
        DbDongle::disableStrictMode();

        DbDongle::convertTimestamps('forum_channels');
        DbDongle::convertTimestamps('forum_members');
        DbDongle::convertTimestamps('forum_posts');
        DbDongle::convertTimestamps('forum_topic_followers');
        DbDongle::convertTimestamps('forum_topics');
    }

    public function down()
    {
        // ...
    }
}
