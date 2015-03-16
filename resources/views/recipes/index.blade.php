@extends('app')

@section('content')

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-4 col-md-3 col-lg-2">
            <h3>Filters</h3>
            <form>
                <div class="form-group">
                    <label for="query">Titel</label>
                    <input type="text" class="form-control" name="title" placeholder="Zoek op titel" value="{{ $params['title'] }}" />
                </div>

                <div class="form-group">
                    <label for="query">Inhoud</label>
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
            @if(Auth::check())
            <p> <a class="btn btn-success" href="/recipes/create" role="button"><i class="fa fa-plus"></i> Nieuw recept</a> </p>
            @endif
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Volgnr.</th>
                    <th>Titel</th>
                    <th>Categorie</th>
                    <th>Kookboek</th>
                </tr>
                @foreach($recipes as $recipe)
                <tr>
                    <td>{{ $recipe->tracking_nr }}</td>
                    <td><a href="/recipes/{{ $recipe->tracking_nr }}?lang={{ $recipe->language }}">{{ $recipe->title }}</a></td>
                    <td>{{ $recipe->category }}</td>
                    <td><a href="/cookbooks/{{ $recipe->cookbook }}/recipes">{{ $recipe->cookbook }}</a></td>
                </tr>
                @endforeach
            </table>
            <p>{{ $count }} resultaten.</p>
        </div>
	</div>
	<div class="row">
		<div class="col-md-10 col-md-offset-2 center">
            {!! $recipes->render() !!}
        </div>
	</div>
</div>
@endsection
