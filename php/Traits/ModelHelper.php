<?php

namespace App\Traits;

use App\Exceptions\Handler;
use Cache;

trait ModelHelper
{
    public static $check_instance = null;

    public function saveData($data)
    {
        $denyNull = self::getProperty("denyNull");
        foreach ($data as $k => $v) {
            if ($denyNull && in_array($k, $denyNull) && empty($v)) {
                throw new \Exception('【 ' . $k . ' 】 The value must not be empty');
            } else {
                $this->$k = $v;
            }
        }
        $this->save();
        if (isset($data['order_id'])) {
            $this->order_id = $data['order_id'];
        }

        return $this;
    }

    public function getNullValue()
    {
        $dbs = $this->newQuery()->fromQuery("SHOW FIELDS FROM " . \DB::getTablePrefix() . $this->getTable())->toArray();
        $rs = [];
        foreach ($dbs as $row) {
            switch ($row['Type']) {
              case strpos($row['Type'], 'int'):
                $rs[$row['Field']] = -100;
                break;
              case strpos($row['Type'], 'decimal'):
                $rs[$row['Field']] = -100;
                break;
              case strpos($row['Type'], 'enum'):
                $rs[$row['Field']] = 0;
                break;
              case strpos($row['Type'], 'json'):
                $rs[$row['Field']] = [];
                break;
              case strpos($row['Type'], 'varchar'):
                $rs[$row['Field']] = '';
                break;
              default:
                $rs[$row['Field']] = '';
                break;
            }
        }

        return $rs;
    }

    /**
    *   剔除有数据情况下的null
    */
    public function deleteNullValue($object_now){
        if(empty($object_now)) return ;
        $dbs = $this->newQuery()->fromQuery("SHOW FIELDS FROM " . \DB::getTablePrefix() . $this->getTable())->toArray();
        foreach($dbs as $field){
            $now = $field['Field'];
            if(empty($object_now->$now)){
                $object_now->$now = $this->statusNow($field['Type']);
            }
        }
        
        return $object_now;
    }

    public function statusNow($type){
        switch ($type) {
          case strpos($type, 'int'):
            return -100;
            break;
          case strpos($type, 'decimal'):
            return -100;
            break;
          case strpos($type, 'enum'):
            return 0;
            break;
          case strpos($type, 'json'):
            return  [];
            break;
          case strpos($type, 'varchar'):
            return '';
            break;
          default:
            return '';
            break;
        }
    }

    public function getNullSelectValue($obj, $fields = [])
    {
        if (empty($fields) || empty($obj)) {
            return $this->getNullValue();
        }
        foreach ($fields as $key => $field) {
            $keys = explode('-',$key);
            if(!empty($keys[0])){
                if($keys[0] == 'int'){
                    $obj->$field = '0';
                }elseif($keys[0] == 'double'){
                    $obj->$field = '0.00';
                }elseif($keys[0] == 'array'){
                    $obj->$field = [];
                }else{
                    $obj->$field = '';
                }                
            }else{
                $obj->$field = '';
            }

        }
        
        return $obj;
    }

    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function baseCreate($data, $primary_keys = [])
    {
        $keys = [];

        if ($primary_keys) {
            foreach ($primary_keys as $key) {
                $keys[$key] = $data[$key];
            }

            $instance = self::firstOrNew($keys, $data);
        } else {
            $instance = new self();
            foreach ($data as $key => $value) {
                $instance[$key] = $value;
            }
        }

        $instance->save();

        return $instance;
    }

    public static function baseList()
    {
        $query = self::where("id", ">", 0)->orderBy("id", "desc");

        return self::page($query);
    }

    public static function whereList($data)
    {
        if (empty($data)) {
            return self::baseList();
        } else {
            $query = self::orderBy("id", "desc");
            foreach ($data as $key => $value) {
                $query->where($key, $value);
            }
        }

        return self::page($query);
    }

    public static function baseSearch($data)
    {
        $keyword = $data["keyword"];
        $query = self::where("id", ">", 0)->orderBy("id", "desc");

        if ($keyword) {
            $searchable = self::getProperty("searchable");
            $query->where(function ($subQuery) use ($searchable, $keyword) {
                foreach ($searchable ?? [] as $key) {
                    if ("tags" == $key) {
                        $tmp_keyword = str_replace('"', '', json_encode($keyword));
                        $tmp_keyword = str_replace('\u', '%', $tmp_keyword);
                    } else {
                        $tmp_keyword = $keyword;
                    }
                    $subQuery->orWhere($key, "like", "%$tmp_keyword%");
                }
            });
        }

        if ($data["condition"]) {
            foreach ($data["condition"] as $name => $value) {
                if ($value) {
                    $query->where($name, $value);
                }
            }
        }

        // dd($query->toSql());

        return self::page($query);
    }

    public static function page($query)
    {
        return $query->paginate(self::getProperty("per_page") ?? 10)->toArray();
    }

    public static function baseDetail($data)
    {
        return self::findOrFail($data["id"])->toArray();
    }

    public static function baseUpdate($data)
    {
        $instance = self::findOrFail($data["id"]);

        unset($data["id"]);
        unset($data["_url"]);

        foreach ($data as $key => $value) {
            $instance->$key = $value;
        }

        $instance->save();

        return $instance;
    }

    public static function baseDelete($data)
    {
        $instance = self::findOrFail($data["id"]);

        return $instance->delete();
    }

    public static function getProperty($name)
    {
        if (!self::$check_instance) {
            self::$check_instance = new self();
        }

        return self::$check_instance->$name ?? null;
    }

    /**
     *@function: 将二维数组转换成一维数组
     *@param:$arr 数组
     *@return:一位数组
     */
    public static function twoToOne($arr, $field)
    {
        $res = [];
        if ($arr && is_array($arr)) {
            $res = array_column($arr, $field);
        }

        return $res;
    }
}
