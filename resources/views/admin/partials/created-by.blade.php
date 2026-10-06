@php
    $creator = $record->creator;
    $creatorRole = $creator?->roles->first();
@endphp

@if ($creator)
    <span class="d-block">{{ $creator->name }}</span>
    <small class="text-muted">({{ $creatorRole ? str($creatorRole->name)->headline() : 'No Role' }})</small>
@else
    <span class="text-muted">System / Legacy</span>
@endif
