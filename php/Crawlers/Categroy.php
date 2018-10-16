<?php

namespace App\Crawlers;

use Symfony\Component\DomCrawler\Crawler;
use App\Models\Industry;
use App\Models\Category;
use App\Models\UserCategory;

class Categroy extends CrawlerBase
{
    public $requestOptions = [
        "timeout" => 30,
        "proxy" => false,
        "user_agent" => 'Chrome',
        "header" => [
          "Content-Type" => 'application/json; charset=UTF-8',
          "cookie" => 'api_uid=rBQQlltNsSR+h1QXBiNWAg==; PASS_ID=e47687a37521465ea0251080b2d8841f_779592225_13685055; api_uid=rBQQlltNsSR+h1QXBiNWAg==; PASS_ID=e47687a37521465ea0251080b2d8841f_779592225_13685055',
        ],
    ];

    //行业列表
    private $industry_url = "https://mms.pinduoduo.com/earth/api/merchant/queryCategoryAndProperty";

    //一二级分类
    private $first_url = "https://mms.pinduoduo.com/earth/api/merchant/querySubByStapleId";

    //三级分类
    private $end_cat_url = "https://mms.pinduoduo.com/vodka/v2/mms/categories?parentId=%s";
    private $man_cat_url = "http://apiv4.yangkeduo.com/operations?pdduid=5320468833";
    private $woman_cat_url = "http://apiv4.yangkeduo.com/operations?pdduid=3459782036673";

    public function run()
    {
        // $this->industry();
        // $this->firstCategory();
        // $this->thirdCategory();
        // $this->newFirst();
        // $this->firstCategory();
        // $this->newSecond();
        // $this->manCat();
        $this->changeCid();
    }

    public function changeCid()
    {
        $firstCat = UserCategory::where('pid', 0)->get();
        foreach ($firstCat as $cat) {
            $category = Category::where('name', $cat['name'])
                              ->where('level', 1)
                              ->first();
            if (empty($category)) {
                dump('未匹配分类' . $cat['name']);
            } else {
                dump('匹配分类' . $cat['name']);
                $cat->cid = $category->id;
                $cat->save();
            }
        }
    }

    public function manCat($sex = 2)
    {
        if ($sex == 1) {
            $getData = $this->httpGet($this->man_cat_url);
        } else {
            $getData = $this->httpGet($this->woman_cat_url);
        }

        foreach ($getData as $data) {
            $first = UserCategory::firstOrNew([
              'name' => $data['opt_name'],
              'sex' => $sex,
            ]);
            $first->name = $data['opt_name'];
            $first->sex = $sex;
            $first->picture = $data['image_url'];
            $first->cat_desc = $data['opt_desc'];
            $first->pid = 0;
            $first->save();
            foreach ($data['children'] as $row) {
                $second = UserCategory::firstOrNew([
                  'name' => $row['opt_name'],
                  'sex' => $sex,
                  'pid' => $first->id,
                ]);
                $second->name = $row['opt_name'];
                $second->sex = $sex;
                $second->pid = $first->id;
                $second->picture = $row['image_url'];
                $second->cat_desc = $row['opt_desc'];
                $second->save();
            }
            dump($data['opt_name']);
        }
    }

    public function newFirst($pid = 0, $industry_id = 0)
    {
        $url = sprintf($this->end_cat_url, $pid);
        $getData = $this->httpGet($url);
        foreach ($getData['result'] as $row) {
            $Cat = Category::firstOrNew([
            'id' => $row['id'],
            ]);
            $Cat->id = $row['id'];
            $Cat->name = $row['cat_name'];
            $Cat->level = $row['level'];
            $Cat->pid = $row['parent_id'];
            $Cat->is_hidden = $row['is_hidden'];
            $Cat->is_deleted = $row['is_deleted'];
            $Cat->cat_desc = $row['cat_desc'];
            $Cat->notice = $row['level'];
            $Cat->picture = $row['image_url'];
            $Cat->industry_id = $industry_id;
            $Cat->save();
            dump($Cat->name);
        }
    }

    public function newSecond()
    {
        $cat = Category::where('level', 2)->get();
        foreach ($cat as $row) {
            $this->newFirst($row['id'], $row['industry_id']);
            sleep(2);
        }
    }

    public function industry()
    {
        $url = $this->industry_url;

        $getData = $this->httpPost($url);

        $industrys = $getData['result']['categoryList']['common'];
        foreach ($industrys as $row) {
            $industry = Industry::firstOrNew(['id' => $row['categoryId']]);
            $industry->id = $row['categoryId'];
            $industry->name = $row['categoryName'];
            $industry->save();
        }
    }

    public function firstCategory()
    {
        $industrys = Industry::get();
        foreach ($industrys as $industry) {
            $this->eachFirst($industry);
            sleep(5);
        }
    }

    public function thirdCategory()
    {
        $seconds = Category::where('level', 2)->get();
        foreach ($seconds as $second) {
            $this->eachThird($second);
            sleep(3);
        }
    }

    public function eachThird($second)
    {
        $url = sprintf($this->end_cat_url, $second->id);
        $getData = $this->httpGet($url);
        foreach ($getData as $value) {
            // code...
        }
    }

    public function eachFirst($industry)
    {
        $url = $this->first_url;
        $getData = $this->httpPost($url, [
            'body' => '{"stapleId":' . $industry->id . '}',
        ]);
        foreach ($getData['result'] as $row) {
            $firstCat = Category::where('name', $row['categoryName'])->first();
            // $firstCat->name = $row['categoryName'];
            if ($firstCat) {
                $firstCat->industry_id = $industry->id;
                $firstCat->save();
            } else {
                dump('未找到分类' . $row['categoryName']);
            }

            // $firstCat->level = 1;
            // $firstCat->pid = 0;
            // $firstCat->save();
            // foreach ($row['subCategoryList'] as $r) {
            //     $secendCat = Category::firstOrNew([
            //     'id' => $r['id'], ]);
            //     $secendCat->id = $r['id'];
            //     $secendCat->name = $r['catName'];
            //     $secendCat->level = 2;
            //     $secendCat->industry_id = $industry->id;
            //     $secendCat->pid = $firstCat->id;
            //     $secendCat->save();
            // }
        }
        dump('完成' . $industry->id . '分类');
    }
}
