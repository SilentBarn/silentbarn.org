<?php

namespace Actions\Posts;

use \Db\Sql\Relationships as Relationships,
    \Db\Sql\Categories as Categories;

class Category extends \Base\Action
{
    /**
     * Takes in a list of category slugs and saves them to
     * a post.
     *
     * @param \Db\Sql\Posts $post
     * @param array $slugs
     */
    public function saveToPost( $post, $slugs )
    {
        // remove all categories from the post
        //
        if ( is_string( $slugs ) )
        {
            $slugs = [ $slugs ];
        }

        $rels = Relationships::getByObjectAndPropertyType(
            $post->id,
            POST,
            CATEGORY );

        foreach ( $rels as $rel )
        {
            $rel->delete();
        }

        // get the categories passed in
        //
        $categories = Categories::getBySlugs( $slugs );

        // save new category relationships to the post
        //
        foreach ( $categories as $category )
        {
            $rel = new Relationships();
            $rel->initialize();
            $rel->object_id = $post->id;
            $rel->object_type = POST;
            $rel->property_id = $category->id;
            $rel->property_type = CATEGORY;
            $rel->key = 'post_category';
            $rel->save();
        }

        return TRUE;
    }
}