<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatHidden extends Model
{
    protected $table = 'chat_hidden';
    protected $fillable = ['user_id', 'other_user_id'];
}
