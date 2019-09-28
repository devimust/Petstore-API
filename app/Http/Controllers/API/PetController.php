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
     * Display a listing of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function read(Request $request, $id)
    {
        // perform input validation on $id
        if (!is_numeric($id) || $id < 0) {
            return $this->sendInvalidInput();
        }

        // check whether pet record exist
        $pet = Pet::find($id);
        if (!$pet) {
            return $this->sendError(
                'pet record not found'
            );
        }

        // return the pet record
        return $this->sendJsonResponse(
            Pet::formatResponse($pet)
        );
    }

    /**
     * Create a new record of the App\Pet model.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $id           = $request->input('id');
        $categoryObj  = $request->input('category');
        $name         = $request->input('name');
        $photoUrls    = $request->input('photoUrls');
        $tags         = $request->input('tags');
        $status       = $request->input('status');

        // perform input validation on $id
        if (!is_numeric($id) || $id < 0) {
            return $this->sendInvalidInput();
        }

        // check whether pet record exist
        $pet = Pet::find($id);
        if ($pet) {
            return $this->sendError(
                'pet record exist'
            );
        }

        // try to create the record
        $pet = Pet::create([
            'id'        => $id,
            'name'      => $name,
            'photoUrls' => implode('|', $photoUrls),
            'status'    => $status
        ]);
        if (!$pet) {
            return $this->sendInvalidInput();
        }

        // ensure category is created and/or assigned correctly
        $category = Category::find($categoryObj['id']);
        if (!$category) {
            $category = Category::create([
                'id' => $categoryObj['id'],
                'name' => $categoryObj['name']
            ]);
        }
        $pet->category()->associate($category);
        $pet->save();

        // ensure tags are created and/or assigned correctly
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

        // return the pet record
        return $this->sendJsonResponse(
            Pet::formatResponse($pet)
        );
    }

    /**
     * Delete a listing of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id)
    {
        // perform input validation on $id
        if (!is_numeric($id) || $id < 0) {
            return $this->sendInvalidInput('Invalid ID supplied', 400);
        }

        // check whether pet record exist
        $pet = Pet::find($id);
        if (!$pet) {
            return $this->sendError(
                'pet record not found'
            );
        }

        // delete the pet record
        $pet->delete();

        return $this->sendResponse('');
    }
}
