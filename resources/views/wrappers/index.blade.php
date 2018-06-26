@extends(config('mardin.views.master_template'))

@section(config('mardin.views.master_section'))
<div class="mardin tray holder">
    @include('mardin::tray', ['miId' => 'mardin-inbox-tray'])
</div>
<div class="mardin inbox holder wrapper">
    @include('mardin::inbox', ['miId' => 'mardin-inbox'])
</div>
@endsection