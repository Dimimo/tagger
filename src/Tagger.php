<?php
/**
 *  Copyright (c) 2016-2021 Puerto Parrot. Written by Dimitri Mostrey for https// www.puertoparrot.com
 *  Contact me at admin@puertoparrot.com or dmostrey@yahoo.com
 */

namespace Dimimo\Tagger;

use App\Models\Article;
use Dimimo\Tagger\Traits\FormTrait;
use Dimimo\Tagger\Traits\StopWordTrait;
use Illuminate\Support\Facades\Request;

/**
 * Class Tagger
 *
 * @package Dimimo\Tagger
 */
class Tagger implements TaggerInterface
{
    use StopWordTrait, FormTrait;
    /**
     * @var array $config
     */
    public array $config = [];
    /**
     * The underlying tagger implementation.
     *
     * @var TaggerInterface
     */
    public TaggerInterface $tagger;
    /**
     * The model of the source of body. Important when inserting into the database
     *
     * @var string $model
     */
    public string $model;
    /**
     * @var Article $article
     */
    public Article $article;
    /**
     * The language setting. Important for the database
     *
     * @var string $lang
     */
    public string $lang = 'en';
    /**
     * The is of the model requested. Important for the database
     *
     * @var int $id
     */
    public int $id;
    /**
     * The body of the text we are working with, the raw version (with html)
     *
     * @var string $body
     */
    public string $body;
    /**
     * The selected tags from the body, the frequency is mentioned as ['tag name' => 'frequency']
     *
     * @var array $tags
     */
    public array $tags;

    /**
     * Creates new instance of Tagger, initiate the config
     *
     * @param array  $config
     */
    public function __construct(array $config = [])
    {
        $this->init($config);
    }

    /**
     * @param array  $config
     */
    private function init(array $config)
    {
        $this->lang = $this->getLang();
        $this->config = array_replace(config('tagger'), $config);
    }

    /**
     * @return string
     */
    private function getLang(): string
    {
        if (Request::exists('lang'))
        {
            return Request::get('lang');
        }
        if (session('lang'))
        {
            return session('lang');
        }

        return $this->lang;
    }

    /**
     * Call when working with an article (Tagger:article($id))
     * The tags are created here
     *
     * @param Article $article
     * @param int     $tag_frequency
     *
     * @return Tagger|null
     */
    public function article(Article $article, int $tag_frequency = 4): ?Tagger
    {
        $tagger          = new Tagger();
        $tagger->article = $article;
        $tagger->model   = 'Article';
        $tagger->lang    = $article->lang;
        $tagger->body    = $article->body;
        $tagger->id      = $article->id;
        $tagger->createTags($tag_frequency);

        return $tagger;
    }

    /**
     * @param int $tag_frequency
     *
     * @return $this|string
     */
    private function createTags(int $tag_frequency = 4): string|static
    {
        $stop_words = self::stopWords();
        //first, let's clean up the body text from html tags and new lines
        $body = trim(preg_replace("/\n|\r|\r\n/", " ", strip_tags($this->body)));
        //a word boundary search, note that number are ignored, this is non-case sensitive
        preg_match_all("/(\b[a-zA-Z]{4,}\b)/", $body, $words);
        //filter the stop words out (common English words)
        $words = array_diff($words[0], $stop_words);
        //counts the words, reverses the array to 'word' => number
        $this->tags = array_count_values($words);
        //get rid of all values smaller than the $tag_frequency
        $this->tags = array_filter($this->tags, function ($n, $k) use ($tag_frequency, $stop_words) {
            if ($n >= $tag_frequency) {
                if (in_array(strtolower($k), $stop_words)) {
                    return null;
                }

                return $n;
            }

            return null;
        }, ARRAY_FILTER_USE_BOTH);
        //sort the numbers reversed, keep the keys, return the array
        arsort($this->tags);

        return $this;
    }

    /**
     * Dynamically proxy method calls to the underlying tagger.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->tagger->{$method}(...$parameters);
    }
}
