<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentResult extends Model
{
    protected $table = 'agent_jobs';

    protected $fillable = [
        'user_id', 'job_id', 'status', 'result', 'message'
    ];
}
