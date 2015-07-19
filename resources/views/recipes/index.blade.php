@extends('app')

@section('content')

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-4 col-md-3 col-lg-2">
            <h3>Filters</h3>
            <form>
                <div class="form-group">
                    <label for="query">Zoek op titel</label>
                    <input type="text" class="form-control" name="title" placeholder="Titel" value="{{ $params['title'] }}" />
                </div>

                <div class="form-group">
                    <label for="query">Zoek op inhoud</label>
                    <input type="text" class="form-control" name="query" placeholder="Zoekwoord" value="{{ $params['query'] }}" />
                </div>

                <div class="form-group">
                    <label for="lang[]">Taal</label>
                    <select multiple class="form-control" name="lang[]">
                        @foreach($available_languages as $code)
                        <option value="{{ $code }}" {{ in_array($code, $chosen_languages) ? 'selected' : '' }}>{{ $languages[$code] or $code }}</option>
                        @endforeach
                    </select>
                </div>

                @if(!$hide_cookbooks)
                <div class="form-group">
                    <label for="cookbook">Kookboek</label>
                    <select class="form-control" name="cookbook">
                        <option value="*">Alle kookboeken</option>
                        @foreach($cookbooks as $cb)
                        <option value="{{ $cb->slug }}" {{ $params['cookbook'] == $cb->slug ? 'selected' : '' }}>{{ $cb->title }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="form-group">
                    <label for="category">Categorie</label>
                    <select class="form-control" name="category">
                        <option value="*">Alle categorieën</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $params['category'] == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <a href="/recipes?lang[]=nl" class="btn btn-sm btn-danger pull-left">Reset filters</a>
                <button type="submit" class="btn btn-sm btn-primary pull-right">Toepassen</button>
                <div class="clearfix visible-xs-block"></div>
            </form>
        </div>
		<div class="col-sm-8 col-md-9 col-lg-10">
            <h1>Recepten</h1>

            <p>
	            <a class="btn btn-success" href="/recipes/create" role="button"><i class="fa fa-plus"></i> Nieuw recept</a>
	            <a class="btn btn-default pull-right" href="/recipes/random" role="button"><i class="fa fa-random"></i> Verras me</a>
            </p>

            @if(count($recipes) == 0)
            <div class="well text-center no-results">
                <h1>Geen resultaten</h1>
                <p>Er zijn helaas geen recepten gevonden die voldoen aan de zoekcriteria.</p>
                <p></p><a href="/recipes?lang[]=nl" class="btn btn-lg btn-danger">Reset filters</a></p>
            </div>
            @else
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Volgnr.</th>
                    <th>Titel</th>
                    <th>Categorie</th>
                    <th>Kookboek</th>
                </tr>
                @foreach($recipes as $recipe)
                <tr>
                    <td><a href="/recipes/{{ $recipe->tracking_nr }}?lang={{ $recipe->language }}">{{ $recipe->tracking_nr }}</a></td>
                    <td><a href="/recipes/{{ $recipe->tracking_nr }}?lang={{ $recipe->language }}">{{ $recipe->title }}</a></td>
                    <td>{{ $recipe->category }}</td>
                    <td><a href="/cookbooks/{{ $recipe->cookbook }}/recipes">{{ $recipe->cookbook }}</a></td>
                </tr>
                @endforeach
            </table>
            <p>{{ $count }} resultaten.</p>
            @endif
        </div>
	</div>
	<div class="row">
		<div class="col-md-10 col-md-offset-2 center">
            {!! $recipes->render() !!}
        </div>
	</div>
</div>
@endsection
