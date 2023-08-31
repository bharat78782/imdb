<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Movie;
use Goutte\Client;
use DataTables;
use App\Jobs\ScrapeMovies;

class ScrapeController extends Controller
{

     public function movie(Request $request)
     {
        if ($request->ajax()) {
            $data = Movie::select('*')->latest();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('url', function($row){
                           $url = $row->url; 
                           $btn = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
    
                            return $btn;
                    })
                    ->rawColumns(['url'])
                    ->make(true);
        }

        return view('movie.index');
     }

     public function movieList()
     {
        $data = Movie::paginate(10);
        return view('movie.movie-list',compact('data'));
     }
    public function scrape(Request $request)
        {
            
            // return $request->all();
            $url = "https://www.imdb.com/chart/top";
    
            $client = new Client();
            $crawler = $client->request('GET', $url);
            $moviesData = [];

            $existingTitles = Movie::pluck('title')->toArray();
            $insertedCount = 0;

            try {
                $crawler->filter('.ipc-metadata-list-summary-item')->each(function ($node) use (&$moviesData, $existingTitles, &$insertedCount) {
                    if ($insertedCount >= 10) {
                        return; // Stop collecting data after inserting 10 entries
                    }

                    $title = $node->filter('.ipc-title__text')->text();

                    // Check if the title is already in the database
                    if (!in_array($title, $existingTitles)) {
                        $year = $node->filter('.cli-title-metadata-item')->eq(0)->text();
                        $ratingNode = $node->filter('.ipc-rating-star--base'); // Adjust the selector
                        $rating = $ratingNode->attr('aria-label'); // Get the 'aria-label' attribute

                        // Extract the numeric part of the rating from the 'aria-label' attribute
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
                            Movie::create($movieData);
                            // dispatch(new ScrapeMovies($movieData));
                            $insertedCount++;
                            if($request->type == 0)
                            {
                                return array("message"=>'Data inserted successfully for movie','type'=>'success');
                            }else{
                                return array("message"=>'Data inserted successfully for movie','type'=>'success');

                            }
                        } catch (\Exception $e) {
                            return array("message"=>'Error inserting data for movie'. $e->getMessage(),'type'=>'error');
                        }
                    }
                });
            } catch (\Exception $e) {
               
                return array("message"=>'An error occurred during scraping'. $e->getMessage(),'type'=>'error');
            }
         
            
          
        }

        public function withOutJsScrape(Request $request)
        {
            
            // return $request->all();
            $url = "https://www.imdb.com/chart/top";
    
            $client = new Client();
            $crawler = $client->request('GET', $url);
            $moviesData = [];

            $existingTitles = Movie::pluck('title')->toArray();
            $insertedCount = 0;

            try {
                $crawler->filter('.ipc-metadata-list-summary-item')->each(function ($node) use (&$moviesData, $existingTitles, &$insertedCount) {
                    if ($insertedCount >= 10) {
                        return; // Stop collecting data after inserting 10 entries
                    }

                    $title = $node->filter('.ipc-title__text')->text();

                    // Check if the title is already in the database
                    if (!in_array($title, $existingTitles)) {
                        $year = $node->filter('.cli-title-metadata-item')->eq(0)->text();
                        $ratingNode = $node->filter('.ipc-rating-star--base'); // Adjust the selector
                        $rating = $ratingNode->attr('aria-label'); // Get the 'aria-label' attribute

                        // Extract the numeric part of the rating from the 'aria-label' attribute
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
                            // Movie::create($movieData);
                            dispatch(new ScrapeMovies($movieData));
                            $insertedCount++;
                           
                             return redirect()->back()->with(array("message"=>'Data inserted successfully for movie','type'=>'success'));
                           
                        } catch (\Exception $e) {
                             return redirect()->back()->with(array("message"=>'Error inserting data for movie'. $e->getMessage(),'type'=>'error'));
                        }
                    }
                });
            } catch (\Exception $e) {
               
                return  redirect()->back()->with(array("message"=>'An error occurred during scraping'. $e->getMessage(),'type'=>'error'));
            }
         
            
          
        }
}
