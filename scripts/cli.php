<?php

require_once 'vendor/autoload.php';

use DiDom\Document;

libxml_use_internal_errors(true);

$urls = array(
    'giavang1' => 'https://www.24h.com.vn/ajax/box_ban_tin_gia_vang/index/1/',
    'giavang2' => 'https://www.24h.com.vn/ajax/box_ban_tin_gia_vang/index/2/'
);

$today       = date('Y-m-d');
$url1        = $urls['giavang1'] . $today;
$url2        = $urls['giavang2'] . $today;
$domDocument = new DOMDocument();
$document    = new Document();
$result      = array();

$loaiVang1 = [
    'Tp.Hồ Chí Minh'    => [
        'common'            => '',
        '10K'               => '',
        '14K'               => '',
        '18K'               => '',
        '24K'               => '',
        'SJC10c'            => '',
        'SJC1c'             => '',
        'SJC99.99'          => '',
        'SJC99.99N'         => ''
    ],
    'Hà Nội'            => [
        'SJC'               => ''
    ],
    'Nha Trang'         => [
        'SJC'               => ''
    ]
];

$loaiVang2 = [
    'Vàng TG ($)'               => '',
    'SJC TP HCM'                => '',
    'SJC Hà Nội'                => '',
    'SJC Đà Nẵng'               => '',
    'DOJI HN'                   => '',
    'DOJI SG'                   => '',
    'Phú Qúy SJC'               => '',
    'VIETINBANK GOLD'           => '',
    'MARITIME BANK'             => '',
    'PNJ TP.HCM'                => '',
    'PNJ Hà Nội'                => '',
    'SCB'                       => '',
    'EXIMBANK'                  => '',
    'Ngọc Hải (NHJ) TP.HCM'     => '',
    'Ngọc Hải (NHJ) Tiền Giang' => '',
    'BẢO TÍN MINH CHÂU'         => '',
    'TPBANK GOLD'               => ''
];

// Could load the page
if ($domDocument->loadHTMLFile($url1)) {
    echo "$url1.\n";
    $document->loadHtml($domDocument->saveHTML());

    if ($document->has('#div_ban_tin_gia_vang_1')) {
        $rows = $document->find('table tr');

        $i=3;
        foreach ($loaiVang1['Tp.Hồ Chí Minh'] as $key => $value) {
            $cell = $rows[$i]->find('td');
            $loaiVang1['Tp.Hồ Chí Minh'][$key] = [
                'giamua' => trim($cell[1]->text()),
                'giaban' => trim($cell[2]->text())
            ];
            $i++;
        }

        $cell = $rows[13]->find('td');
        $loaiVang1['Hà Nội']['SJC'] = [
            'giamua' => trim($cell[1]->text()),
            'giaban' => trim($cell[2]->text())
        ];

        $cell = $rows[15]->find('td');
        $loaiVang1['Nha Trang']['SJC'] = [
            'giamua' => trim($cell[1]->text()),
            'giaban' => trim($cell[2]->text())
        ];
    }
}

if ($domDocument->loadHTMLFile($url2)) {
    echo "$url2.\n";
    $document->loadHtml($domDocument->saveHTML());

    if ($document->has('#div_ban_tin_gia_vang_2')) {
        $rows = $document->find('table tr');

        $i=2;
        foreach ($loaiVang2 as $key => $value) {
            $cell = $rows[$i]->find('td');

            $loaiVang2[$key] = [
                'giamua' => trim($cell[1]->text()),
                'giaban' => trim($cell[2]->text())
            ];
            $i++;
        }
    }
}

// Create a json file for blog site
file_put_contents("public/gia-vang-$today.json", json_encode($loaiVang1, JSON_PRETTY_PRINT));
file_put_contents("public/gia-vang-the-gioi-$today.json", json_encode($loaiVang2, JSON_PRETTY_PRINT));
