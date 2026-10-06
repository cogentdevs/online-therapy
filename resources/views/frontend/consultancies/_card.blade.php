<article class="consultancy-card">
    <a class="consultancy-card__image" href="{{ route('front.consultancies.show', $consultancy) }}">
        @if (filled($consultancy->image))
            <img src="{{ asset($consultancy->image) }}" alt="{{ $consultancy->title }}" loading="lazy">
        @else
            <span class="consultancy-card__image-placeholder"><i class="fa-regular fa-comments" aria-hidden="true"></i></span>
        @endif
    </a>
    <div class="consultancy-card__body">
        <h2><a href="{{ route('front.consultancies.show', $consultancy) }}">{{ $consultancy->title }}</a></h2>
        @if (filled($consultancy->short_description))
            <p>{{ $consultancy->short_description }}</p>
        @endif
        <dl class="consultancy-card__details front-ui">
            <div><dt>Duration</dt><dd>{{ $consultancy->formattedDuration() }}</dd></div>
            <div><dt>Medium</dt><dd>{{ $consultancy->formattedMedium() }}</dd></div>
        </dl>
        <a class="front-taza-button consultancy-card__button" href="{{ route('front.consultancies.show', $consultancy) }}">
            {{ $consultancy->button_label ?: 'View Details' }}
        </a>
    </div>
</article>
