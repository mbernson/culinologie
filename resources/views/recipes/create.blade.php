@extends('app')

@section('content')

<div class="container">
	<div class="row">
		<form class="col-md-10 col-offset-1" method="POST" action="/recipes">
                    <input type="hidden" name="_method" value="POST">

                    <h1>Nieuw recept</h1>

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
                        <textarea name="directions" rows="10" class="form-control"></textarea>
                        <p><em>Maak gebruik van <a href="#">Markdown syntax</a> bij het schrijven.</em></p>
                    </div>

                    <div class="form-group">
                        <label for="presentation">Finishing touches (niet verplicht)</label>
                        <textarea name="presentation" rows="4" class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="lang">Taal</label>
                        <select class="form-control" name="lang">
                            <option value="uk">Engels (Groot Brittanië)</option>
                            <option value="us">Engels (Amerikaans)</option>
                            <option value="nl" selected>Nederlands</option>
                            <option value="cs">Spaans</option>
                            <option value="ct">Catalaans</option>
                        </select>
                    </div>

                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />

                    <button type="submit" class="btn btn-success">Opslaan</button>
                </form>
	</div>
</div>

@stop
