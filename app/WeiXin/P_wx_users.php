<?php

namespace App\WeiXin;

use Illuminate\Database\Eloquent\Model;

class P_wx_users extends Model
{
    protected $table='p_wx_users';

    protected $primaryKey='uid';

    public $timestamps = false;

    protected $guarded = [];
}
