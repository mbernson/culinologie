@extends('app')

@section('content')

<div class="container">
	<div class="row">
		<div class="col-sm-12 col-md-12 col-lg-12">
            <h1>Kookboeken</h1>

            @if(Auth::check())
            <p><a class="btn btn-success" data-toggle="collapse" href="#cookbookform" aria-expanded="false" aria-controls="cookbookform" role="button"><i class="fa fa-plus"></i> Nieuw kookboek</a> </p>
            <div class="collapse" id="cookbookform">
                <div class="well">
                    <form method="post" action="/cookbooks">
                        <div class="form-group">
                            <label for="title">Titel</label>
                            <input type="text" class="form-control" name="title" id="title" placeholder="Mijn recepten">
                        </div>

                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <button type="submit" class="btn btn-primary">Kookboek aanmaken</button>
                    </form>
                </div>
            </div>
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
