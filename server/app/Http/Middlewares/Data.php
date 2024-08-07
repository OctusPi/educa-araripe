<?php
namespace App\Http\Middlewares;

use App\Models\Contract;
use App\Models\Item;
use App\Models\Organ;
use App\Models\Out;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Data
{

    public static function find(string $model, array $params = [], ?array $order = null, ?array $with = null, ?array $between = null){
        $query = self::query($model, $params, $order, $with, $between);
        
        if(!is_null($query)){
            return $query->get();
        }

        return [];
    }

    public static function findOne(string $model, array $params = [], ?array $order = null, ?array $with = null, ?array $between = null)
    {
        $query = self::query($model, $params, $order, $with, $between);
        
        if(!is_null($query)){
            return $query->first();
        }

        return null;
    }

    private static function query(string $model, array $params = [], ?array $order = null, ?array $with = null, ?array $between = null)
    {
        $user = Auth::user();

        if (!is_null($user)) {
            if ($user->profile == User::P_ADMIN || (!is_null($user->organs) && !is_null($user->units))) {
                $query = $model::query();

                $paramsAnd = self::paramsAND($params);
                $paramsOr  = self::paramsOR($params);

                if(!empty($paramsAnd)) {
                    $query->where(function($query) use ($paramsAnd){
                        foreach ($paramsAnd as $prm) {
                            $query->where($prm->column, $prm->operator, $prm->value);
                        }
                    });
                }

                if(!empty($paramsOr)) {
                    $query->where(function($query) use ($paramsOr){
                        foreach ($paramsOr as $prm) {
                            $query->orWhere($prm->column, $prm->operator, $prm->value);
                        }
                    });
                }

                if($user->profile != User::P_ADMIN) {
                    $mods = match ($model) {
                        Contract::class, Item::class => self::paramsGenericOrgan($user),
                        Organ::class => self::paramsOrgan($user),
                        Unit::class => self::paramsUnit($user),
                        User::class => self::paramsUser($user),
                        Out::class => self::paramsGeneric($user),
                        default => null
                    };

                    if(!is_null($mods)){
                        $query->where(function($query) use ($mods) {
                            foreach ($mods as $mod) {
                                $query->where($mod->column, $mod->operator, $mod->value);
                            }
                        });
                    }
                }

                //apply between
                if($between){
                    foreach ($between as $key => $value) {
                        $query->whereBetween($key, $value);
                    }
                }

                //apply order
                if ($order) {
                    $query->orderBy(...$order);
                }

                //apply with
                if ($with) {
                    $query->with($with);
                }

                return $query;
            }
        }

        return null;
    }

    private static function paramsOrgan($user): array
    {
        $params = [];
        $ids = array_column($user->organs, "id");

        if (!is_null($ids)) {
            foreach ($ids as $id) {
                $params[] = (object) ['column' => 'id', 'operator' => '=', 'value' => $id];
            }
        }

        return $params;
    }

    private static function paramsUnit($user): array
    {
        $params = [];
        $idsUnit = array_column($user->units, "id");

        if (!is_null($idsUnit)) {
            foreach ($idsUnit as $id) {
                $params[] = (object) ['column' => 'id', 'operator' => '=', 'value' => $id];
            }
        }

        return $params;
    }

    private static function paramsUser($user): array
    {
        return  [(object)['column' => 'organs', 'operator' => '=', 'value' => json_encode($user->organs)]];
    }

    private static function paramsGenericOrgan($user): array
    {
        $params = [];
        $idsOrgan = array_column($user->organs, "id");

        if (!is_null($idsOrgan)) {
            foreach ($idsOrgan as $id) {
                $params[] = (object) ['column' => 'organ', 'operator' => '=', 'value' => $id];
            }
        }

        return $params;
    }

    private static function paramsGenericUnit($user): array
    {
        $params = [];
        $idsUnit = array_column($user->organs, "id");

        if (!is_null($idsUnit)) {
            foreach ($idsUnit as $id) {
                $params[] = (object) ['column' => 'unit', 'operator' => '=', 'value' => $id];
            }
        }

        return $params;
    }

    private static function paramsGeneric($user): array
    {
        return array_merge(self::paramsGenericOrgan($user), self::paramsGenericUnit($user));
    }

    private static function paramsAND(?array $params):array
    {
        $ands = [];

        if (!is_null($params)) {
            foreach ($params as $key => $value) {
                if(is_array($value)) {
                    if(!isset($value['mode']) || $value['mode'] === 'AND') {
                        $ands[] = (object) [
                            'column' => $value['column'], 
                            'operator' => $value['operator'] ?? 'LIKE', 
                            'value' => $value['value']
                        ];
                    }
                }else{
                    $ands[] = (object) [
                        'column' => $key, 
                        'operator' => 'LIKE', 
                        'value' => $value
                    ];
                }
            }
        }

        return $ands;
    }

    private static function paramsOR(?array $params):array
    {
        $ors = [];

        foreach ($params as $param) {

            if(is_array($param)){
                if (isset($param['mode']) && $param['mode'] === 'OR') {
                    $ors[] = (object)[
                        'column' => $param['column'], 
                        'operator' => $param['operator'] ?? 'LIKE', 
                        'value' => $param['value']
                    ];
                }
            }
        }

        return $ors;
    }
}