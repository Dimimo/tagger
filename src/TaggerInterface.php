<?php
/**
 *  Copyright (c) 2016-2021 Puerto Parrot. Written by Dimitri Mostrey for https// www.puertoparrot.com
 *  Contact me at admin@puertoparrot.com or dmostrey@yahoo.com
 */

namespace Dimimo\Tagger;

use App\Models\Article;
use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\FormBuilder;

interface TaggerInterface
{
    /**
     * Call when working with an article (Tagger:article($id))
     * The tags are created here
     *
     * @param Article  $article
     * @param int  $tag_frequency
     *
     * @return Tagger|null
     */
    public function article(Article $article, $tag_frequency = 4);

    /**
     * Create the form for the tags
     *
     * @param FormBuilder  $formBuilder
     *
     * @return Form $form
     */
    public function articleTagsForm(FormBuilder $formBuilder);
}
