<?php

namespace Modules\Ihelpers\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use DOMDocument;
use Illuminate\Support\Facades\Storage;

class CreateSitemap implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Curl the document
     * @param string $url
     * @param int $timeout
     * @return string $data
     */
    private $route;
    private function curl($url, $timeout)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    /**
     *
     * @param string $url
     * @param array $tags array ('description', 'keywords')
     * @param int $timeout seconds
     * @return mixed false| array
     */
    public function getMeta($url, $tags = array('description', 'keywords'), $timeout = 0)
    {
        set_time_limit(0);
        try {
            $html = $this->curl($url, $timeout);
            if (!$html) {
                return false;
            }

            $doc = new DOMDocument();
            @$doc->loadHTML($html);
            $nodes = $doc->getElementsByTagName('title');

            // Get and display what you need:

            $ary = [];

            $ary['title'] = $nodes->item(0)->nodeValue;
            // $metas = $doc->getElementsByTagName('meta');

            // for ($i = 0; $i < $metas->length; $i++) {
            //     $meta = $metas->item($i);
            //
            //     foreach($tags as $tag) {
            //         if ($meta->getAttribute('name') == $tag) {
            //             $ary[$tag] = $meta->getAttribute('content');
            //         }
            //     }
            // }
            return $ary;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            //return $e->getMessage();
        }

    }//getMeta()

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($route)
    {
    $this->route=$route;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            \Log::info('Get meta: '.$this->route);
            dd($this->getMeta($this->route));
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            dd($e->getMessage());
        }
    }
}
