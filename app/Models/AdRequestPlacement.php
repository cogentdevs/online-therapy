<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['ad_request_id', 'page_name', 'place'])]
class AdRequestPlacement extends Model
{
    protected $attributes = ['status' => 'pending'];

    public function adRequest(): BelongsTo
    {
        return $this->belongsTo(AdRequest::class);
    }

    public function ad(): HasOne
    {
        return $this->hasOne(Ad::class);
    }

    public function displayLabel(): string
    {
        $page = Ad::PAGE_PLACEMENTS[$this->page_name] ?? null;
        $placeLabel = $page['places'][$this->place] ?? null;

        return $placeLabel === null ? 'تشہیری مقام' : $page['label'].' — '.$placeLabel;
    }
}
