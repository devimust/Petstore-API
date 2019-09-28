<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Tag;
use App\Pet;
use App\Category;
use Validator;

class PetController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request, $id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return $this->sendInvalidInput();
        }

        $pet = Pet::find($id);
        if (!$pet) {
            return $this->sendError(
                'pet record not found'
            );
        }

        return $this->sendResponse(
            Pet::formatResponse($pet)
        );
    }

    /**
     *
     */
    public function post(Request $request)
    {
        $id           = $request->input('id');
        $categoryObj  = $request->input('category');
        $name         = $request->input('name');
        $photoUrls    = $request->input('photoUrls');
        $tags         = $request->input('tags');
        $status       = $request->input('status');

        $pet = Pet::find($id);
        if ($pet) {
            return $this->sendError(
                'pet record exist'
            );
        }

        $pet = Pet::create([
            'id'        => $id,
            'name'      => $name,
            'photoUrls' => implode('|', $photoUrls),
            'status'    => $status
        ]);
        if (!$pet) {
            return $this->sendInvalidInput();
        }

        $category = Category::find($categoryObj['id']);
        if (!$category) {
            $category = Category::create([
                'id' => $categoryObj['id'],
                'name' => $categoryObj['name']
            ]);
        }
        $pet->category()->associate($category);
        $pet->save();

        $tagIds = [];
        foreach ($tags as $tagObject) {
            $tag = Tag::find($tagObject['id']);
            if (!$tag) {
                $tag = Tag::create([
                    'id' => $tagObject['id'],
                    'name' => $tagObject['name']
                ]);
            }
            $tagIds[] = $tag->id;
        }

        $pet->tags()->sync($tagIds);

        return $this->sendResponse(
            Pet::formatResponse($pet)
        );
    }
}
