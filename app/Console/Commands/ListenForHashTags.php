<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use TwitterStreamingApi;

class ListenForHashTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitter:listen-for-hash-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen for hashtags being used on Twitter';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //setLocale('nl') -- only dutch tweets
        TwitterStreamingApi::publicStream()
            ->whenHears('#gropsv', function (array $tweet) {
              //check if we have a place or coordinates

                // insert the tweets into the database
                DB::table('tweets')->insert([
                    'userName' => "{$tweet['user']['screen_name']}",
                    'tweet' => "{$tweet['text']}",
                    'avatar_url' => "{$tweet['user']['profile_image_url_https']}",
                    'created_at' => \Carbon\Carbon::now()->getPreciseTimestamp(4),
                    'place' => 'nergens'
                    ]
                );
                // dump the tweet into the terminal
                dump("{$tweet['user']['screen_name']} tweeted {$tweet['text']}");

            })
            ->startListening();
    }
}
