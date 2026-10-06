<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['language', 'card_position', 'title_position'])]
class HomeCardTitlePosition extends Model {}
