<?php

namespace App\WeiXin;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    //
    protected $table='message';

    protected $primaryKey='mid';

    public $timestamps = false;

    protected $guarded = [];
}
