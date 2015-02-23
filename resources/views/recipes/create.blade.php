@extends('app')

@section('content')

@if($recipe->exists)
<form class="container" method="POST" action="/recipes/{{ $recipe->tracking_nr }}?lang={{ $recipe->language }}">
    <input type="hidden" name="_method" value="PUT">
@else
<form class="container" method="POST" action="/recipes">
    <input type="hidden" name="_method" value="POST">
@endif

	<div class="row">
            <div class="col-md-12">
                @if($recipe->exists)
                <h1>Recept bewerken</h1>
                @else
                <h1>Nieuw recept</h1>
                @endif
            </div>
        </div>
	<div class="row">
		<div class="col-md-9">
                    <div class="form-group">
                        <label for="title">Titel</label>
                        <input type="text" class="form-control" name="title" placeholder="Spaghetti bolognese" value="{{ $recipe->title }}" />
                    </div>

                    <div class="form-group">
                        <label for="people">Aantal personen</label>
                        <input type="number" class="form-control" name="people" value="{{ $recipe->people or 2 }}" />
                    </div>

                    <div class="form-group">
                        <label for="ingredients">Ingrediënten</label>
                        <textarea name="ingredients" rows="6" class="form-control" placeholder="Zet elk ingrediënt op een eigen regel">{{ $recipe->textIngredients() }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="directions">Bereiding</label>
                        <textarea name="directions" rows="12" class="form-control">{{ $recipe->description }}</textarea>
                        <p><em>Maak gebruik van <a href="#">Markdown syntax</a> bij het schrijven.</em></p>
                    </div>

                    <div class="form-group">
                        <label for="presentation">Finishing touches (niet verplicht)</label>
                        <textarea name="presentation" rows="6" class="form-control">{{ $recipe->presentation }}</textarea>
                    </div>

                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="lang">Taal</label>
                        <select class="form-control" name="lang">
                            @foreach($languages as $code => $lang)
                            <option value="{{ $code }}" {{ $recipe->language == $code ? 'selected' : '' }}>{{ $lang }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="cookbook">Kookboek</label>
                        <select class="form-control" name="cookbook">
                            @foreach($cookbooks as $book)
                            <option value="{{ $book->slug }}" {{ $recipe->cookbook == $book->slug ? 'selected' : '' }}>{{ $book->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="category">Categorie</label>
                        <select class="form-control" name="category">
                            <option value="">Geen categorie</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ $recipe->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <p class="center"><strong>of geef een nieuwe categorie op:</strong></p>

                    <div class="form-group">
                        <label for="category_alt">Nieuwe categorie</label>
                        <input type="text" class="form-control" name="category_alt" placeholder="Soepen" />
                    </div>

                    <div class="form-group">
                        <label for="temperature">Temperatuur</label>
                        <select class="form-control" name="temperature">
                            @foreach($temperatures as $temp => $title)
                            <option value="{{ $temp }}" {{ $recipe->temperature == $temp ? 'selected' : '' }}>{{ $title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="season">Seizoen</label>
                            <select class="form-control" name="season">
                            @if($recipe->exists)
                            <option value="{{ $recipe->season }}" selected>{{ $seasons[$recipe->season] or $recipe->season }}</option>
                            @endif
                            @foreach($seasons as $code => $season)
                            <option value="{{ $code }}">{{ $season }}</option>
                            @endforeach
                        </select>
                    </div> 

                    <div class="form-group">
                        <label for="year">Jaar</label>
                        <input type="number" class="form-control" name="year" value="{{ $recipe->year or date('Y') }}" />
                    </div>

                    <button type="submit" class="btn btn-lg btn-success">Recept opslaan</button>
            </div>
	</div>
</form>

@stop
