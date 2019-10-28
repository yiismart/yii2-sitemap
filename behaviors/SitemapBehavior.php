<?php

namespace smart\sitemap\behaviors;

use Closure;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use smart\db\ActiveRecord;
use smart\sitemap\models\Sitemap;

/**
 * Entity is the endpoint in the site tha have own address in the web.
 * SEO and Sitemap is worked with entities.
 */
class SitemapBehavior extends Behavior
{
    const CHANGEFREQ_ALWAYS = 'always';
    const CHANGEFREQ_HOURLY = 'hourly';
    const CHANGEFREQ_DAILY = 'daily';
    const CHANGEFREQ_WEEKLY = 'weekly';
    const CHANGEFREQ_MONTHLY = 'monthly';
    const CHANGEFREQ_YEARLY = 'yearly';
    const CHANGEFREQ_NEVER = 'never';

    /**
     * @var boolean|string|Closure
     * If its string value then try to find owner attribute with this name
     */
    public $active = 'active';

    /**
     * @var string|Closure owner attribute name or user function($model)
     * URL of the page. This URL must begin with the protocol (such as http) and end with a trailing slash, if your web server requires it. This value must be less than 2,048 characters.
     */
    public $loc;

    /**
     * @var string|null|Closure owner attribute name or user function($model)
     * The date of last modification of the file. This date should be in W3C Datetime format. This format allows you to omit the time portion, if desired, and use YYYY-MM-DD.
     * 
     * If not set, will be generated automatically.
     */
    public $lastmod;

    /**
     * @var string|Closure owner attribute name or user function($model)
     * How frequently the page is likely to change. This value provides general information to search engines and may not correlate exactly to how often they crawl the page.
     */
    public $changefreq;

    /**
     * @var string|Closure owner attribute name or user function($model)
     * The priority of this URL relative to other URLs on your site. Valid values range from 0.0 to 1.0. 
     * This value does not affect how your pages are compared to pages on other sites—it only lets the search engines know which pages you deem most important for the crawlers.
     */
    public $priority;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->loc === null) {
            throw new InvalidConfigException('Property "loc" must be set.');
        }
    }

    /**
     * {@inheritdoc}
     * Attach events for sitemap functionality
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'onAfterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'onAfterSave',
            ActiveRecord::EVENT_AFTER_DELETE => 'onAfterDelete',
        ];
    }

    /**
     * After save event
     * @param yii\base\Event $event 
     * @return void
     */
    public function onAfterSave($event)
    {
        $owner = $this->owner;

        $active = true;
        if ($this->active instanceof Closure) {
            $active = call_user_func($this->active, $owner);
        } elseif (is_string($this->active)) {
            $active = $owner->{$this->active};
        }

        if ($active) {
            $this->sitemapSave();
        } else {
            $this->sitemapDelete();
        }
    }

    /**
     * After delete event
     * @param yii\base\Event $event 
     * @return void
     */
    public function onAfterDelete($event)
    {
        $this->sitemapDelete();
    }

    /**
     * Save sitemap
     * @return void
     */
    private function sitemapSave()
    {
        $owner = $this->owner;

        $model = Sitemap::findByOwner($owner);
        if ($model === null) {
            $model = new Sitemap;
            $model->setOwner($owner);
        }

        if ($this->loc instanceof Closure) {
            $model->loc = call_user_func($this->loc, $owner);
        } else {
            $model->loc = $owner->{$this->loc};
        }

        if ($this->lastmod instanceof Closure) {
            $model->lastmod = call_user_func($this->lastmod, $owner);
        } elseif ($this->lastmod !== null) {
            $model->lastmod = $owner->{$this->lastmod};
        } else {
            $model->lastmod = date(DATE_W3C);
        }

        if ($this->changefreq instanceof Closure) {
            $model->changefreq = call_user_func($this->changefreq, $owner);
        } elseif ($this->changefreq !== null) {
            $model->changefreq = $owner->{$this->changefreq};
        }

        if ($this->priority instanceof Closure) {
            $model->priority = call_user_func($this->priority, $owner);
        } elseif ($this->priority !== null) {
            $model->priority = $owner->{$this->priority};
        }

        $model->save(false);
    }

    /**
     * Delete sitemap
     * @return void
     */
    private function sitemapDelete()
    {
        $model = Sitemap::findByOwner($this->owner);

        if ($model !== null) {
            $model->delete();
        }
    }
}
