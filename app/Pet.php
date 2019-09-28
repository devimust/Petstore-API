<?php

namespace App;

use Cartalyst\Tags\TaggableTrait;
use Cartalyst\Tags\TaggableInterface;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'photoUrls', 'status', 'category_id'
    ];

    /**
     * Enable one-to-many relationship object to a Category.
     */
    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    /**
     * Enable many-to-many relationship object to Tags.
     */
    public function tags()
    {
        return $this->belongsToMany('App\Tag', 'tagged');
    }

    /**
     * Format the response data for a specified pet.
     * @param mixed $pet
     * @return json
     */
    public static function formatResponse($pet)
    {
        // strip dates in response data
        $category = $pet->category;
        unset($category['created_at']);
        unset($category['updated_at']);

        // strip dates and pivot info in response data
        $tags = $pet->tags;
        foreach($tags as &$tagValue) {
            unset($tagValue['created_at']);
            unset($tagValue['updated_at']);
            unset($tagValue['pivot']);
        }

        // format output just like the received input
        return [
            'id'        => $pet->id,
            'category'  => $category,
            'name'      => $pet->name,
            'photoUrls' => explode('|', $pet->photoUrls),
            'tags'      => $tags,
            'status'    => $pet->status
        ];
    }
}
