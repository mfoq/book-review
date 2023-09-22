<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    public function reviews(){

        return $this->hasMany(Review::class);
    }

    public function scopeTitle(Builder $query, string $title): Builder
    {
        return $query->where('title', 'LIKE', '%'.$title.'%');
    }

    public function scopeWithReviewsCount(Builder $query, $from = null, $to = null): Builder{
        return $query->withCount(['reviews'=> fn(Builder $q) => $this->dateRangeFilter( $q, $from, $to)]);
    }

    public function scopeWithAvgRating(Builder $query, $from = null, $to = null): Builder {
        return $query->withAvg(['reviews'=> fn(Builder $q) => $this->dateRangeFilter( $q, $from, $to)],'rating');
    }

    public function scopePopularBooksInDescOrder(Builder $query, $from = null, $to = null): Builder {
        return $query->withReviewsCount()->orderBy('reviews_count', 'desc');
    }

    public function scopeHighestRated(Builder $query,  $from = null, $to = null): Builder {
        return $query->WithAvgRating()->orderBy('reviews_avg_rating', 'desc');
    }

    public function scopeMinReviews(Builder $query, $minReviews) : Builder{
        return $query->having('reviews_count', '>=', $minReviews);
    }

    private function dateRangeFilter(Builder $query,$from , $to){ //جينيرال فنكنشن
        if($from && !$to){
            return $query->where('create_at', '>=', $from);
        } elseif (!$from && $to){
            return $query->where('create_at', '<=' , $to);
        } elseif($from && $to){
            return $query->whereBetween('created_at', [$from, $to]);
        }
    }

    public function scopePopularLastMonth(Builder $query) : Builder {
        return $query->PopularBooksInDescOrder(now()->subMonth(), now())
                ->HighestRated(now()->subMonth(), now())
                ->MinReviews(2);
    }

    public function scopePopularLastSixMonths(Builder $query) : Builder {
        return $query->PopularBooksInDescOrder(now()->subMonths(6), now())
                ->HighestRated(now()->subMonths(6), now())
                ->MinReviews(5);
    }

    public function scopeHighestRatedLastMonth(Builder $query) : Builder {
        return $query->HighestRated(now()->subMonth(), now())
                ->PopularBooksInDescOrder(now()->subMonth(), now())
                ->MinReviews(2);
    }

    public function scopeHighestRatedLastSixMonths(Builder $query) : Builder {
        return $query->HighestRated(now()->subMonths(6), now())
                ->PopularBooksInDescOrder(now()->subMonths(6), now())
                ->MinReviews(5);
    }

    protected static function booted() : void
    {
        static::updated(fn(Book $book) => cache()->forget('book:' .  $book->id));
        static::deleted(fn(Book $book) => cache()->forget('book:' .  $book->id));
    }
}
