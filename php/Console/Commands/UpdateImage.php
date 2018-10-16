<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\DownloadImageHelper as DownloadImage;

class UpdateImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('开始拉取');
        $this->saveImage();
        $this->info('拉取结束');
    }

    private function saveImage()
    {
        $downloadImage = new DownloadImage();
        \App\Models\UserCategory::chunk(100, function ($categories) use ($downloadImage) {
            foreach ($categories as $category) {
                $filepath = $category->picture;
                if (!empty($filepath)) {
                    if ( preg_match('/^http:\/\//', $filepath) ) {
                        $res = $downloadImage->saveImage($filepath);
                        $this->info($category->id);
                        if ($res['filename']) {
                            $category->local_image_url = $res['filename'];
                            $category->save();
                        }
                    }
                }
            }
        });
    }
}
