@extends(config('mardin.views.master_template'))

@section(config('mardin.views.master_section'))
<div class="mardin thread holder wrapper">
    @include('mardin::thread', ['miId' => 'mardin-inbox-thread'])
</div>
@endsection