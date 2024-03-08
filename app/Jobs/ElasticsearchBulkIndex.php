<?php

namespace App\Jobs;

use App\Elasticsearch\ElasticSearch;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ElasticsearchBulkIndex implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //get related settings
        $lastIDSettings = Setting::where([
            'option_key' => 'elastic_bulk_index_last_id',
            'active' => 1
        ])->first();

        if(!$lastIDSettings)
            return;

        $sendSMSSettings = Setting::where([
            'option_key' => 'elastic_bulk_index_send_sms',
            'active' => 1
        ])->first();

        if(!$sendSMSSettings)
            return;

        $postsCount = Post::count();
        $elasticBulkIndexLastId = $lastIDSettings->option_value;
        $shouldSendSMS = $sendSMSSettings->option_value;

        //check if all posts are indexed
        if($postsCount <= $elasticBulkIndexLastId){
            if($shouldSendSMS == 1){
                //if all posts are indexed and send sms option is true, then send sms
                SendSMS::dispatch();
                $this->sendSMSSwitch($sendSMSSettings);
            }

            return;
        }

        //trigger send sms settings to 1 to send sms after index completion
        $this->sendSMSSwitch($sendSMSSettings);

        //bulk index 1000 posts
        (new ElasticSearch())->postsBulkIndex($elasticBulkIndexLastId);
    }

    private function sendSMSSwitch($settings)
    {
        $settings->option_value = !($settings->option_value);
        $settings->save();
    }
}
