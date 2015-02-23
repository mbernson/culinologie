@extends('app')

@section('content')

<form class="container" method="POST" action="/recipes">
	<div class="row">
            <div class="col-md-12">
                <h1>Nieuw recept</h1>
            </div>
        </div>
	<div class="row">
		<div class="col-md-9">
                    <input type="hidden" name="_method" value="POST">


                    <div class="form-group">
                        <label for="title">Titel</label>
                        <input type="text" class="form-control" name="title" placeholder="Spaghetti bolognese" />
                    </div>

                    <div class="form-group">
                        <label for="people">Aantal personen</label>
                        <input type="number" class="form-control" name="people" value="2" />
                    </div>

                    <div class="form-group">
                        <label for="ingredients">Ingrediënten</label>
                        <textarea name="ingredients" rows="6" class="form-control" placeholder="Zet elk ingrediënt op een eigen regel"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="directions">Bereiding</label>
                        <textarea name="directions" rows="12" class="form-control"></textarea>
                        <p><em>Maak gebruik van <a href="#">Markdown syntax</a> bij het schrijven.</em></p>
                    </div>

                    <div class="form-group">
                        <label for="presentation">Finishing touches (niet verplicht)</label>
                        <textarea name="presentation" rows="6" class="form-control"></textarea>
                    </div>



                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />

                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="lang">Taal</label>
                        <select class="form-control" name="lang">
                            @foreach($languages as $code => $lang)
                            <option value="{{ $code }}">{{ $lang }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="cookbook">Kookboek</label>
                        <select class="form-control" name="cookbook">
                            @foreach($cookbooks as $book)
                            <option value="{{ $book->slug }}">{{ $book->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="category">Categorie</label>
                        <select class="form-control" name="category">
                            <option value="">Geen categorie</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="temperature">Temperatuur</label>
                        <select class="form-control" name="temperature">
                            @foreach($temperatures as $temp => $title)
                            <option value="{{ $temp }}">{{ $title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="season">Seizoen</label>
                            <select class="form-control" name="season">
                            @foreach($seasons as $code => $season)
                            <option value="{{ $code }}">{{ $season }}</option>
                            @endforeach
                        </select>
                    </div> 

                    <div class="form-group">
                        <label for="year">Jaar</label>
                        <input type="number" class="form-control" name="year" value="{{ date('Y') }}" />
                    </div>

                    <button type="submit" class="btn btn-lg btn-success">Recept opslaan</button>
            </div>
	</div>
</form>

@stop
