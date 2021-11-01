<?php

namespace App\Http\Controllers;

use App\Laravue\JsonResponse;
use App\Models\Author;
use App\Rules\SocialMediaObject;
use App\Services\AuthorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $authors = Author::pipe();
        if(get_parent_class($authors) === 'Illuminate\Pagination\AbstractPaginator'){
            $authors = $authors->getCollection();
        }
        if(request()->filled('with') &&
            (request('with') == 'tokenTransactions' ||
                (gettype(request('with') == 'array') && in_array('tokenTransactions', request('with')))
            )
        ){
            foreach($authors as $idx => $author){
                $myId = $author->id;
                $authors[$idx]['total_tokens'] = $author->tokenTransactions->reduce(function($carry, $item)use($myId){
                    $descriptor = json_decode($item->descriptor, true);
                    return $carry + floor($item->token_amount * $descriptor['author_split'][$myId]);
                }, 0);
                unset($authors[$idx]->tokenTransactions);
            }
        }
        return response()->json(new JsonResponse([
            'items' => $authors,
            'total' => Author::pipeCount()
        ]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, AuthorService $authorService)
    {
        $validated = $request->validate([
            'name' => ['string', 'required', 'max:140'],
            'description' => ['string', 'required'],
            'social_media_links' => [new SocialMediaObject(), 'nullable'],
            'email' => ['email', 'required', 'max:140'],
            'profile_picture_url' => ['is_uri_or_url', 'required'],
            'user_id' => ['integer', 'required']
        ]);
        $authorService->setData($request->all());
        $retval = $authorService->create();
        return response()->json($retval, 200);
        // return response()->json($validated, 200);

        // $validated['profile_picture_url'] = $this->storeFileFromRequest($request, 'profile_picture_url', 'public/media/authors');

        // return response()->json(Author::create($validated), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function show(Author $author)
    {
        return response()->json(new JsonResponse($author));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function edit(Author $author)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Author $author, AuthorService $authorService)
    {
        $validated = $request->validate([
            'name' => ['string', 'required', 'max:140'],
            'description' => ['string', 'required'],
            'social_media_links' => [new SocialMediaObject(), 'nullable'],
            'email' => ['email', 'required', 'max:140'],
            'profile_picture_url' => ['is_uri_or_url', 'nullable'],
            'user_id' => ['integer', 'required']
        ]);
        $authorService->setData($request->all());
        $authorService->setRecord($author);
        $retval = $authorService->update();
        return response()->json($retval, 200);

        // if(!empty($validated['profile_picture_url'])){
        //     $validated['profile_picture_url'] = $this->storeFileFromRequest($request, 'profile_picture_url', 'public/media/authors');
        // }

        // return response()->json($author->update($validated), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function destroy(Author $author)
    {
        return response()->json($author->delete());
    }
}
