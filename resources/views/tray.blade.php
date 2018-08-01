@php
$mardinBase = '';
$mardinAd = false;
$mardinUserId = '';
$miId = empty($miId) ? 'mardin-inbox-tray' : $miId;

if (auth()->check()) {
    $mardinUserId = auth()->user()->id;
    $mardinBase = basename(route('messages'));

    if ($mardinAdClient = config('mardin.ad.client')) {
        $mardinAd = true;
        $mardinAd = [
            'client' => $mardinAdClient,
            'slotId' => config('mardin.ad.slot_id'),
        ];
    }
}
@endphp

<div 
    id="{{ $miId }}"
    data-mardin-inbox-tray="true"
    data-mardin-user="{{ $mardinUserId }}"
    data-mardin-base="{{ $mardinBase }}"
    data-mardin-ad="{{ is_array($mardinAd) ? json_encode($mardinAd) : false }}"
    ></div>