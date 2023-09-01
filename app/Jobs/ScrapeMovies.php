<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Goutte\Client;
use App\Models\Movie;

class ScrapeMovies implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $movie;
    public $insertedCount = 0;
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
       
        // $this->movie = $movie;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
       
        $url = "https://www.imdb.com/chart/top";
        
        $client = new Client();
        $scrapeData = $client->request('GET', $url);
        $moviesData = [];

        $existingTitles = Movie::pluck('title')->toArray();
         

        try {
            $insert = $this->insertedCount ;
            $scrapeData->filter('.ipc-metadata-list-summary-item')->each(function ($value) use (&$moviesData, $existingTitles, &$insert) {
                if ($insert >= 10) {
                    return; 
                }

                $title = $value->filter('.ipc-title__text')->text();

                if (!in_array($title, $existingTitles)) {
                    $year = $value->filter('.cli-title-metadata-item')->eq(0)->text();
                    $rating = $value->filter('.ipc-rating-star--base'); 
                    $rating = $rating->attr('aria-label');

                
                    preg_match('/([\d.]+)/', $rating, $matches);
                    if (!empty($matches)) {
                        $rating = $matches[0];
                    }

                    $url = "https://www.imdb.com" . $value->filter('.ipc-title-link-wrapper')->attr('href');

                    $movieData = [
                        'title' => $title,
                        'year' => $year,
                        'rating' => $rating,
                        'url' => $url,
                    ];

                    try {
                        \DB::transaction(function () use ($movieData) {
                            Movie::create($movieData);
                            $insert++;
                        });
                    } catch (\Exception $e) {
                        \Log::error('Error inserting data for movie: ' . $e->getMessage());
                    }
                }
            });
            } catch (\Exception $e) {
                \Log::error('An error occurred during scraping: ' . $e->getMessage());
            }



           
    }
}
