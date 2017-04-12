<?php
$mardinBase = $mardinUserId = '';
if (auth()->check()) {
    $mardinUserId = auth()->user()->id;
    $mardinBase = basename(route('messages'));
    $mardinAd = false;

    if ($mardinAdClient = config('mardin.ad.client')) {
        $mardinAd = true;
        $mardinAd = [
            'client' => $mardinAdClient,
            'slotId' => config('mardin.ad.slot_id'),
        ];
    }
}
?>

<div 
    id="{{ $miId }}"
    data-mardin-inbox-tray="true"
    data-mardin-user="{{ $mardinUserId }}"
    data-mardin-base="{{ $mardinBase }}"
    data-mardin-base="{{ $mardinBase }}"
    data-mardin-ad="{{ is_array($mardinAd) ? json_encode($mardinAd) : false }}"
    ></div>