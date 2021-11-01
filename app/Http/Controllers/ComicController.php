<?php

namespace App\Http\Controllers;

use App\Models\Comic;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Rule;
use \App\Laravue\JsonResponse;
use App\Models\Chapter;
use App\Models\TokenTransaction;
use App\Rules\ComicChapter;
use App\Services\ComicService;
use Illuminate\Support\Facades\App;

class ComicController extends Controller
{
    public function justComicIndex(){
        return response()->json(new JsonResponse([
            'items' => Comic::pipe()
        ]));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->filled('just_comics')){
            return $this->justComicIndex();
        }
        $comics = Comic::pipe();
        if(get_parent_class($comics) === 'Illuminate\Pagination\AbstractPaginator'){
            $comics = $comics->getCollection();
        }
        $ids = $comics->pluck('chapters')[0]->pluck('id')->toArray();
        $groupTransaction = TokenTransaction::where('transactionable_type', Chapter::class)
            ->whereIn('transactionable_id', $ids)
            ->get()
            ->groupBy('transactionable_id');
        foreach($comics as $idx => $comic){
            // $comics[$idx]['total_tokens'] = $comic->total_tokens;
            $comics[$idx]['total_tokens'] = $comic->chapters->reduce(function($acc, $el)use($groupTransaction){
                if(!empty($groupTransaction[$el->id])){
                    return $acc + $groupTransaction[$el->id]->sum('token_amount');
                }else{
                    return $acc;
                }
            }, 0);
        }
        return response()->json(new JsonResponse([
            'items' => $comics,
            'total' => Comic::pipeCount()
        ]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ComicService $comicService)
    {
        $validated = $request->validate([
            'author_split' => ['array', 'required'],
            'title' => ['string', 'required', 'max:140'],
            'description' => ['string', 'required'],
            'tags' => ['array', 'required'],
            'genres' => ['array', 'required'],
            'cover_url' => ['is_uri_or_url', 'required'],
            'release_date' => ['date', 'required'],
            'is_draft' => ['boolean', 'required'],
            'chapters' => [new ComicChapter()]
        ]);
        return response()->json($validated);
        $created = $comicService->createComicWithChaptersPages($request->all());

        return response()->json($created, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Comic  $comic
     * @return \Illuminate\Http\Response
     */
    public function show(Comic $comic)
    {
        $comic->load(['authors', 'chapters.pages']);
        return response()->json(new JsonResponse($comic));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Comic  $comic
     * @return \Illuminate\Http\Response
     */
    public function showTransactions(Comic $comic)
    {
        $comic->load(['transactions']);
        return response()->json(new JsonResponse($comic->transactions));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Comic  $comic
     * @return \Illuminate\Http\Response
     */
    public function edit(Comic $comic)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Comic  $comic
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comic $comic, ComicService $comicService)
    {
        $validated = $request->validate([
            'author_split' => ['array', 'required'],
            'title' => ['string', 'required', 'max:140'],
            'description' => ['string', 'required'],
            'tags' => ['array', 'required'],
            'genres' => ['array', 'required'],
            'cover_url' => ['is_uri_or_url', 'required'],
            'release_date' => ['date', 'required'],
            'is_draft' => ['boolean', 'required'],
            'chapters' => [new ComicChapter()]
        ]);
        $updated = $comicService->updateComicWithChaptersPages($request->all());

        return response()->json($updated, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comic  $comic
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comic $comic)
    {
        return response()->json($comic->delete());
    }
}
