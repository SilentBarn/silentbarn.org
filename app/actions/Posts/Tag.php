<?php

namespace Actions\Posts;

use \Db\Sql\Tags as Tags,
    \Db\Sql\Relationships as Relationships;

class Tag extends \Base\Action
{
    /**
     * Takes in a list of tag names and saves them to
     * a post.
     *
     * @param \Db\Sql\Posts $post
     * @param array $names
     */
    public function saveToPost( $post, $names )
    {
        // remove existing tags
        //
        $rels = Relationships::getByObjectAndPropertyType(
            $post->id,
            POST,
            TAG );

        foreach ( $rels as $rel )
        {
            $rel->delete();
        }

        // for each tag that came in, save it to the database
        //
        $tags = array();

        foreach ( $names as $name )
        {
            $tag = Tags::findFirstBySlug( Tags::slugify( $name ) );

            if ( ! $tag )
            {
                $tag = new Tags();
                $tag->initialize();
                $tag->name = $name;
                $tag->slug = $tag->generateSlug();
                $tag->save();
            }

            $tags[] = $tag;
        }

        // save new tag relationships to the post
        //
        foreach ( $tags as $tag )
        {
            $rel = new Relationships();
            $rel->initialize();
            $rel->object_id = $post->id;
            $rel->object_type = POST;
            $rel->property_id = $tag->id;
            $rel->property_type = TAG;
            $rel->key = 'post_tag';
            $rel->save();
        }

        return TRUE;
    }
}