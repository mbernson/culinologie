@extends('app')

@section('content')

<div class="container">
	<div class="row">
		<div class="col-md-3">
                    <p>
                    @if(Session::has('return_url'))
                        <a href="{{ Session::get('return_url') }}" class="btn btn-default">&larr; Terug</a>
                    @endif
                        <a href="/recipes/{{ $recipe->tracking_nr }}/edit?lang={{ $recipe->language }}" class="btn btn-success">Bewerken</a>
                    </p>
                </div>

		<div class="col-md-9">
                    <h1>{{ $recipe->title }}</h1>

                    @if($recipe->people != 0)
                    <p>Voor {{ $recipe->people }} personen.</p>
                    @endif
                </div>
        </div>
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

                    <h2>Informatie</h2>

                    <table class="table">
                        <tr>
                            <th>Kookboek</th>
                            <td><a href="/cookbooks/{{ $cookbook->slug }}/recipes">{{ $cookbook->title }}</a></td>
                        </tr>
                        <tr>
                            <th>Categorie</th>
                            <td><a href="/recipes/?category={{ $recipe->category }}">{{ $recipe->category }}</a></td>
                        </tr>
                        <tr>
                            <th>Temperatuur</th>
                            <td>{{ $temperatures[$recipe->temperature] or $recipe->temperature }}</td>
                        </tr>
                        <tr>
                            <th>Seizoen</th>
                            <td>{{ $seasons[$recipe->season] or $recipe->season }}</td>
                        </tr>
                        <tr>
                            <th>Jaar toegevoegd</th>
                            <td>{{ $recipe->year }}</td>
                        </tr>
                        <!--
                        <tr>
                            <th>Toegevoegd</th>
                            <td>{{ $recipe->updated_at->format('d M Y, H:i') }}</td>
                        </tr>
                        -->
                        <tr>
                            <th>Laatst gewijzigd</th>
                            <td>{{ $recipe->updated_at->format('d M Y, H:i') }}</td>
                        </tr>
                    </table>
                </div>
		<div class="col-md-9">
                    <h2>Bereiding</h2>

                    {!! $recipe->getHtmlDescription() !!}

                    @if(!empty($recipe->presentation))
                    <h2>Finishing touches</h2>

                    {!! $recipe->getHtmlPresentation() !!}
                    @endif
                </div>
	</div>
</div>

@endsection
