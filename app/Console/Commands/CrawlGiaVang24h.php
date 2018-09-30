<?php

namespace App\Console\Commands;

use App\Vang1;
use App\Vang2;
use DiDom\Document;
use Illuminate\Console\Command;

class CrawlGiaVang24h extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl Vang24 page';

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
        $date = $this->option('date');
        if (empty($date)) {
            $date = date('Y-m-d');
        }

        $urls = [
            'https://www.24h.com.vn/ajax/box_ban_tin_gia_vang/index/1/',
            'https://www.24h.com.vn/ajax/box_ban_tin_gia_vang/index/2/'
        ];

        $url1        = $urls[0] . $date;
        $url2        = $urls[1] . $date;
        $domDocument = new \DOMDocument();

        if (!@$domDocument->loadHTMLFile($url1)) {
            $this->error("Invalid document");
        }

        $test1 = Vang1::where('date', $date)->get();

        $test2 = Vang1::where('date', $date)->first();

        // count(Vang1::where('date', $date)->get()) == 0 =>> ssdsd

        if (count(Vang1::where('date', $date)->get()) == 0) {
            $document = new Document($url1, true);

            if ($document->has('#div_ban_tin_gia_vang_1')) {
                $rows = $document->find('table tr');

                for ($i = 3; $i < 12; $i++) {
                    $cell = $rows[$i]->find('td');

                    Vang1::insert([
                        'type'  => empty($cell[0]->text()) ? 'common' : $cell[0]->text(),
                        'buy'   => !is_numeric($cell[1]->text()) ? 0 : $cell[1]->text(),
                        'sell'  => !is_numeric($cell[2]->text()) ? 0 : $cell[2]->text(),
                        'city'  => 'Tp.Hồ Chí Minh',
                        'date'  => $date
                    ]);
                }

                $cell = $rows[13]->find('td');
                Vang1::insert([
                    'type'  => $cell[0]->text(),
                    'buy'   => !is_numeric($cell[1]->text()) ? 0 : $cell[1]->text(),
                    'sell'  => !is_numeric($cell[2]->text()) ? 0 : $cell[2]->text(),
                    'city'  => 'Hà Nội',
                    'date'  => $date
                ]);

                $cell = $rows[15]->find('td');
                Vang1::insert([
                    'type'  => $cell[0]->text(),
                    'buy'   => !is_numeric($cell[1]->text()) ? 0 : $cell[1]->text(),
                    'sell'  => !is_numeric($cell[2]->text()) ? 0 : $cell[2]->text(),
                    'city'  => 'Nha Trang',
                    'date'  => $date
                ]);
            }
        }

        //
        if (!@$domDocument->loadHTMLFile($url2)) {
            $this->error("Invalid document");
        }

        if (count(Vang2::where('date', $date)->get()) == 0) {
            $document = new Document($url2, true);

            if ($document->has('#div_ban_tin_gia_vang_2')) {
                $rows = $document->find('table.tb-giaVang tr');

                for ($i = 2; $i < count($rows); $i++) {
                    $cell = $rows[$i]->find('td');
                    Vang2::insert([
                        'type'  => $cell[0]->text(),
                        'buy'   => str_replace('.', '', $cell[1]->text()),
                        'sell'  => str_replace('.', '', $cell[2]->text()),
                        'date'  => $date
                    ]);
                }
            }
        }

        $this->info("Success.");
    }
}
