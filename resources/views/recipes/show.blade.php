@extends('app')

@section('content')

<div class="container">
	<div class="row">
		<div class="col-md-3">
                    <h2>Ingrediënten</h2>

                    @foreach($ingredient_groups as $title => $group)
                        <h4>{{ $title }}</h4>
                        <ul>
                        @foreach($group as $in)
                            <li>{{ $in->text }}</li>
                        @endforeach
                        </ul>
                    @endforeach

                </div>
		<div class="col-md-9">
                    <h1>{{ $recipe->title }}</h1>

                    <h2>Bereiding</h2>

                    {!! $recipe->description_html !!}

                    <h2>Finishing touches</h2>

                    {!! $recipe->presentation_html !!}
                </div>
	</div>
	<div class="row">
            <div class="col-md-10 col-md-offset-1">
            </div>
	</div>
</div>

@endsection
