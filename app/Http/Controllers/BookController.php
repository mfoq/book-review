<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Cache;


class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = $request->input('title');
        $filter = $request->input('filter', ''); //value or default value as empty

        $books = Book::when($title, fn($query, $title) => $query->title($title));

        $books = match($filter){
            'popular_last_month' => $books->PopularLastMonth(),
            'popular_last_6months' => $books->PopularLastSixMonths(),
            'highest_rated_last_month' => $books->HighestRatedLastMonth(),
            'highest_rated_last_6months' => $books->HighestRatedLastSixMonths(),
            default => $books->latest()->WithAvgRating()->WithReviewsCount(),
        };


        // $books = $books->get();
        // $books = Cache::remember('books', 3600, fn() => $books->get());

        $cacheKey = 'books:' . $filter . ':' . $title;
        $books = cache()->remember($cacheKey, 3, fn() => $books->paginate());



        return view('books.index', ['books' => $books ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {

        $cacheKey = 'book:' . $id;
        $book = cache()->remember(
            $cacheKey,
            3600, fn() =>
             Book::with([
            'reviews' => fn($khara) => $khara->latest()
         ])->WithAvgRating()->WithReviewsCount()->findOrFail($id)
        );
        return view('books.show', ['book' => $book]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
