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

    /**
     * Create a new job instance.
     */
    public function __construct($movie)
    {
        $this->movie = $movie;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $jobId = $this->job->getJobId();
        Log::info("Processing job with ID: $jobId");

        $movieData = [
            'title' =>"as",
            'year' => "as",
            'rating' => "asa",
            'url' => "asas",
        ];
        Movie::Create($movieData);
        // return;
    $url = "https://www.imdb.com/chart/top";
    
    $client = new Client();
    $crawler = $client->request('GET', $url);
    $moviesData = [];

    $existingTitles = Movie::pluck('title')->toArray();
    $insertedCount = 0;

     try {
        $crawler->filter('.ipc-metadata-list-summary-item')->each(function ($node) use (&$moviesData, $existingTitles, &$insertedCount) {
            if ($insertedCount >= 10) {
                return; 
            }

            $title = $node->filter('.ipc-title__text')->text();

            if (!in_array($title, $existingTitles)) {
                $year = $node->filter('.cli-title-metadata-item')->eq(0)->text();
                $ratingNode = $node->filter('.ipc-rating-star--base'); 
                $rating = $ratingNode->attr('aria-label');

               
                preg_match('/([\d.]+)/', $rating, $matches);
                if (!empty($matches)) {
                    $rating = $matches[0];
                }

                $url = "https://www.imdb.com" . $node->filter('.ipc-title-link-wrapper')->attr('href');

                $movieData = [
                    'title' => $title,
                    'year' => $year,
                    'rating' => $rating,
                    'url' => $url,
                ];

                try {
                    \DB::transaction(function () use ($movieData) {
                        Movie::create($movieData);
                        $insertedCount++;
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
