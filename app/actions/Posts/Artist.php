<?php

namespace Actions\Posts;

use \Db\Sql\Artists as Artists,
    \Db\Sql\Relationships as Relationships;

class Artist extends \Base\Action
{
    /**
     * Takes in a list of artist names and saves them to
     * a post.
     *
     * @param \Db\Sql\Posts $post
     * @param array $names
     */
    public function saveToPost( $post, $names )
    {
        // remove existing artists
        //
        $rels = Relationships::getByObjectAndPropertyType(
            $post->id,
            POST,
            ARTIST );

        foreach ( $rels as $rel )
        {
            $rel->delete();
        }

        // for each artist that came in, save it to the database
        //
        $artists = array();

        foreach ( $names as $name )
        {
            $artist = Artists::findFirstBySlug( Artists::slugify( $name ) );

            if ( ! $artist )
            {
                $artist = new Artists();
                $artist->initialize();
                $artist->name = $name;
                $artist->slug = $artist->generateSlug();
                $artist->save();
            }

            $artists[] = $artist;
        }

        // save new artist relationships to the post
        //
        foreach ( $artists as $artist )
        {
            $rel = new Relationships();
            $rel->initialize();
            $rel->object_id = $post->id;
            $rel->object_type = POST;
            $rel->property_id = $artist->id;
            $rel->property_type = ARTIST;
            $rel->key = 'post_artist';
            $rel->save();
        }

        return TRUE;
    }
}