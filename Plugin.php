<?php namespace Frontend\Forum;

use Event;
use Backend;
use Frontend\User\Models\User;
use Frontend\Forum\Models\Member;
use System\Classes\PluginBase;
use Frontend\User\Controllers\Users as UsersController;

/**
 * Forum Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = ['Frontend.User'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Forum',
            'description' => 'Forum for Tux Tips',
            'author'      => 'Alexey Bobkov, Samuel Georges',
            'icon'        => 'icon-comments',
            'homepage'    => 'https://github.com/rainlab/forum-plugin'
        ];
    }

    public function boot()
    {
        User::extend(function($model) {
            $model->hasOne['forum_member'] = ['Frontend\Forum\Models\Member'];

            $model->bindEvent('model.beforeDelete', function() use ($model) {
                $model->forum_member && $model->forum_member->delete();
            });
        });

        UsersController::extendFormFields(function($widget, $model, $context) {
            // Prevent extending of related form instead of the intended User form
            if (!$widget->model instanceof \Frontend\User\Models\User) {
                return;
            }
            if ($context != 'update') {
                return;
            }
            if (!Member::getFromUser($model)) {
                return;
            }

            $widget->addFields([
                'forum_member[is_moderator]' => [
                    'label'   => 'Forum moderator',
                    'type'    => 'checkbox',
                    'tab'     => 'Account',
                    'span'    => 'auto',
                    'comment' => 'Place a tick in this box if this user can moderate the entire forum'
                ],
                'forum_member[is_banned]' => [
                    'label'   => 'Banned from forum',
                    'type'    => 'checkbox',
                    'tab'     => 'Account',
                    'span'    => 'auto',
                    'comment' => 'Place a tick in this box if this user is banned from posting to the forum'
                ]
            ], 'primary');
        });
    }

    public function registerComponents()
    {
        return [
           '\Frontend\Forum\Components\Channels'     => 'forumChannels',
           '\Frontend\Forum\Components\Channel'      => 'forumChannel',
           '\Frontend\Forum\Components\Topic'        => 'forumTopic',
           '\Frontend\Forum\Components\Topics'       => 'forumTopics',
           '\Frontend\Forum\Components\Member'       => 'forumMember',
           '\Frontend\Forum\Components\EmbedTopic'   => 'forumEmbedTopic',
           '\Frontend\Forum\Components\EmbedChannel' => 'forumEmbedChannel'
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'Forum Channels',
                'description' => 'Manage available forum channels',
                'icon'        => 'icon-comments',
                'url'         => Backend::url('frontend/forum/channels'),
                'category'    => 'Forum',
                'order'       => 500
            ]
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'frontend.forum::mail.topic_reply'   => 'Notification to followers when a post is made to a topic.',
            'frontend.forum::mail.member_report' => 'Notification to moderators when a member is reported to be a spammer.'
        ];
    }
}
