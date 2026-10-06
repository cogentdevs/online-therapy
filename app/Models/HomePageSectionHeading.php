<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['language', 'section_name', 'content_position', 'short_title', 'main_title', 'short_detail'])]
class HomePageSectionHeading extends Model {}
