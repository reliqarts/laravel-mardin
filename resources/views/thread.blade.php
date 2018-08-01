@php
$miId = empty($miId) ? 'mardin-inbox-thread' : $miId;
@endphp

<div id="{{ $miId }}" 
    data-mardin-inbox-thread="true"
    @if (isset($thread))
    data-thread="{{ $thread->toJson() }}" 
    @endif
    @if (isset($recipients))
    data-recipients="{{ is_array($recipients) ? json_encode($recipients) : [] }}"
    @endif
    @if (isset($subject))
    data-subject="{!! $subject !!}"
    @endif
    @if (isset($infoLine))
    data-info-line="{!! $infoLine !!}"
    @endif
    ></div>