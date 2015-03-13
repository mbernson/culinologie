@extends('app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <ol class="breadcrumb">
                <li><a href="/help">Help</a></li>
                @if(isset($trail))
                @foreach($trail as $crumb)
                <li>{{ ucfirst(str_replace('-', ' ', $crumb)) }}</li>
                @endforeach
                @endif
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            {!! $content !!}
        </div>
    </div>
</div>

@endsection
