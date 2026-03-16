<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Str;
use Illuminate\Support\Arr;
use Exception;
use Config;
use Illuminate\Database\Eloquent\Builder;
use Datetime;

class EnvController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function QueryBuilder(Builder $builder, $params, $count = false)
    {
        $builder = $builder->whereRaw('1 = 1');

        if($params->filters != null){
            foreach ($params->filters as $key => $p) {
                foreach ($p->constraints as $k => $v) {
                    if($v->value != '' && !is_array($v->value) && !is_object($v->value)){
                        switch ($v->matchMode) {
                            case 'startsWith':
                                $builder = $builder->where($key,'LIKE',$v->value.'%');
                                break;
                            case 'contains':
                                $builder = $builder->where($key,'LIKE','%'.$v->value.'%');
                                break;
                            case 'notContains':
                                $builder = $builder->where($key,'NOT LIKE','%'.$v->value.'%');
                                break;
                            case 'endsWith':
                                $builder = $builder->where($key,'LIKE','%'.$v->value);
                                break;
                            case 'equals':
                                $builder = $builder->where($key, $v->value);
                                break;
                            case 'notEquals':
                                $builder = $builder->where($key,'<>', $v->value);
                                break;
                            case 'in':
                                $builder = $builder->whereIn($key, $v->value);
                                break;
                            case 'lt':
                                $builder = $builder->where($key,'<', intval($v->value));
                                break;
                            case 'lte':
                                $builder = $builder->where($key,'<=', intval($v->value));
                                break;
                            case 'gt':
                                $builder = $builder->where($key,'>', intval($v->value));
                                break;
                            case 'gte':
                                $builder = $builder->where($key,'>=', intval($v->value));
                                break;
                            case 'between':
                                $builder = $builder->where($key, intval($v->value));
                                break;
                            case 'dateIs':
                                $time = strtotime($v->value.' UTC');
                                $dateInLocal = date("Y-m-d", $time);
                                $builder = $builder->whereRaw('CAST('.$key.' AS DATE) = CAST(? AS DATE)', [$dateInLocal]);
                                break;
                            case 'dateIsNot':
                                $time = strtotime($v->value.' UTC');
                                $dateInLocal = date("Y-m-d", $time);
                                $builder = $builder->whereRaw('CAST('.$key.' AS DATE) <> CAST(? AS DATE)', [$dateInLocal]);
                                break;
                            case 'dateBefore':
                                $time = strtotime($v->value.' UTC');
                                $dateInLocal = date("Y-m-d", $time);
                                $builder = $builder->whereRaw('CAST('.$key.' AS DATE) < CAST(? AS DATE)', [$dateInLocal]);
                                break;
                            case 'dateAfter':
                                $time = strtotime($v->value.' UTC');
                                $dateInLocal = date("Y-m-d", $time);
                                $builder = $builder->whereRaw('CAST('.$key.' AS DATE) > CAST(? AS DATE)', [$dateInLocal]);
                                break;
                            
                            default:
                                $builder = $builder->where($key,'LIKE','%'.$v->value.'%');
                                break;
                        }
                    }
                    if($v->value != '' && (is_array($v->value) || is_object($v->value))){
                        switch ($v->matchMode) {
                            
                            case 'equals':
                                $builder = $builder->whereIn($key, collect($v->value)->map(function ($prestation) {
                                    return collect($prestation)
                                        ->values()
                                        ->only([0])
                                        ->all();
                                })->collapse()->toArray());
                                break;
                            case 'in':
                                $builder = $builder->whereIn($key, $v->value);
                                break;
                            
                            
                            default:
                                break;
                        }
                    }
                
                }
            }
        }
        

        if(!$count && $params->first !== null){
            $builder = $builder->skip($params->first)->take(/*$params->first+*/$params->rows);
        }

        if($params->sortField != '' || $params->sortField != null){
            switch ($params->sortOrder) {
                case '1':
                    $builder = $builder->orderBy($params->sortField,'ASC');
                    break;

                case '-1':
                    $builder = $builder->orderBy($params->sortField,'DESC');
                    break;
                
                default:
                    $builder = $builder->orderBy($params->sortField,'ASC');
                    break;
            }
        }

        if($count){
            return $builder->count();
        }else{
            return $builder;
        }
    }

}
