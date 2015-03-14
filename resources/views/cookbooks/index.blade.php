@extends('app')

@section('content')

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12 col-md-12 col-lg-12">
            <h1>Kookboeken</h1>

            @if(Auth::check())
            <!--
            <p> <a class="btn btn-success" href="/cookbooks/create" role="button">Nieuw kookboek</a> </p>
            -->
            @endif

            <table class="table table-striped table-bordered">
                <tr>
                    <th>Titel</th>
                    <th>Eigenaar</th>
                    <th>Aantal recepten</th>
                </tr>
                @foreach($cookbooks as $cookbook)
                <tr>
                    <td><a href="/cookbooks/{{ $cookbook->slug }}/recipes">{{ $cookbook->title }}</a></td>
                    <td>{{ $cookbook->owner->name }}</td>
                    <td>{{ $cookbook->recipes_count }}</td>
                </tr>
                @endforeach
            </table>
        </div>
	</div>
	<div class="row">
		<div class="col-md-10 col-md-offset-2 center">
            {!! $cookbooks->render() !!}
        </div>
	</div>
</div>
@endsection
