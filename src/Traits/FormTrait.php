<?php
/**
 *  Copyright (c) 2016-2021 Puerto Parrot. Written by Dimitri Mostrey for https// www.puertoparrot.com
 *  Contact me at admin@puertoparrot.com or dmostrey@yahoo.com
 */

namespace Dimimo\Tagger\Traits;

use App\Models\Tag;
use Illuminate\Support\Str;
use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;

/**
 * Trait FormTrait
 *
 * @package Dimimo\Tagger\Traits
 */
trait FormTrait
{
    use FormBuilderTrait;

    /**
     * Create the form for the tags
     *
     * @param FormBuilder  $formBuilder
     *
     * @return Form $form
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function articleTagsForm(FormBuilder $formBuilder)
    {
        $tag = new Tag();
        $form = $formBuilder->create('App\Http\Forms\TagsForm', ['method' => 'POST',
                                                                 'class'  => 'form_group',
                                                                 'url'    => route('articles.tags.post'),
                                                                 'model'  => $tag,
                                                                 'data'   => ['tag_pairs' => $this->getTagPairs(), 'article' => $this->article],
        ]);
        $form->add('article_id', 'hidden', ['value' => $this->id, 'attr' => ['id' => 'article_id'], 'template' => 'vendor.laravel-form-builder.text']);
        $form->add('submit', 'submit', ['label' => 'Add tags', 'attr' => ['class' => 'btn btn-primary btn-block']]);

        return $form;
    }

    /**
     * This is needed to distinguish the difference between existing tags in the database and new tags
     *
     * @return array
     */
    protected function getTagPairs()
    {
        $keys = array_keys($this->tags);
        sort($keys);
        $tags['existing'] = $tags['new'] = [];
        $tags['list'] = $this->article->tags->pluck('name', 'id')->toArray();
        foreach ($keys as $key) {
            if ($tag = Tag::where([['slug', Str::slug($key)], ['lang', $this->article->lang]])->get()->first()) {
                $tags['existing'][$tag->id] = "{$tag->name} ({$this->tags[$key]})";
            } else {
                $tags['new'][$key] = "{$key} ({$this->tags[$key]})";
            }
        }
        $tags['list'] = array_unique($tags['list'], SORT_STRING);
        asort($tags['existing'], SORT_STRING);

        return $tags;
    }
}
