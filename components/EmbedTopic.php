<?php namespace Frontend\Forum\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Frontend\Forum\Models\Topic as TopicModel;
use Frontend\Forum\Models\Channel as ChannelModel;
use Exception;

class EmbedTopic extends ComponentBase
{
    /**
     * @var boolean Determine if this component is being used by the EmbedChannel component.
     */
    public $embedMode = true;

    public function componentDetails()
    {
        return [
            'name'        => 'Embed Topic',
            'description' => 'Attach a topic to any page'
        ];
    }

    public function defineProperties()
    {
        return [
            'embedCode' => [
                'title'       => 'Embed Code',
                'description' => 'A unique code for the generated topic or channel. A routing parameter can also be used',
                'type'        => 'string',
            ],
            'channelSlug' => [
                'title'       => 'Target Channel',
                'description' => 'Specify the channel to create the new topic or channel in',
                'type'        => 'dropdown'
            ],
            'memberPage' => [
                'title'       => 'Member Page',
                'description' => 'Page name to use for clicking on a Member',
                'type'        => 'dropdown',
                'group'       => 'Links',
            ],
        ];
    }

    public function getChannelSlugOptions()
    {
        return ChannelModel::listsNested('title', 'slug', ' - ');
    }

    public function getMemberPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function init()
    {
        $mode = $this->property('mode');
        $code = $this->property('embedCode');

        if (!$code) {
            throw new Exception('No code specified for the Forum Embed component');
        }

        $channel = ($channelSlug = $this->property('channelSlug'))
            ? ChannelModel::whereSlug($channelSlug)->first()
            : null;

        if (!$channel) {
            throw new Exception('No channel specified for Forum Embed component');
        }

        $properties = $this->getProperties();

        /*
         * Proxy as topic
         */
        if ($topic = TopicModel::forEmbed($channel, $code)->first()) {
            $properties['slug'] = $topic->slug;
        }

        $component = $this->addComponent('Frontend\Forum\Components\Topic', $this->alias, $properties);

        /*
         * If a topic does not already exist, generate it when the page ends.
         * This can be disabled by the page setting embedMode to FALSE, for example,
         * if the page returns 404 a topic should not be generated.
         */
        if (!$topic) {
            $this->controller->bindEvent('page.end', function() use ($component, $channel, $code) {
                if ($component->embedMode !== false) {
                    $topic = TopicModel::createForEmbed($code, $channel, $this->page->title);
                    $component->setProperty('slug', $topic->slug);
                    $component->onRun();
                }
            });
        }

        /*
         * Set the embedding mode: Single topic
         */
        $component->embedMode = 'single';
    }
}
