@extends('app')

@section('content')

<div class="container-fluid">
	<div class="row">
		<div class="col-md-2">
                    <h3>Filters</h3>
                    <form>
                        <div class="form-group">
                            <label for="title">Titel</label>
                            <input type="text" class="form-control" name="title" placeholder="Match titel" value="{{ $title }}" />
                        </div>

                        <div class="form-group">
                            <label for="lang">Taal</label>
                            <select class="form-control" name="lang">
                                @foreach($languages as $code => $lang)
                                <option value="{{ $code }}" {{ $params['lang'] == $code ? 'selected' : '' }}>{{ $lang }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($cookbooks != null)
                        <div class="form-group">
                            <label for="lang">Kookboek</label>
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

                        <button type="submit" class="btn btn-primary">Toepassen</button>
                    </form>
                </div>
		<div class="col-md-10">
                <h1>Recepten</h1>
                <p> <a class="btn btn-success" href="/recipes/create" role="button">Nieuw recept</a> </p>
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
                        <td>{{ $recipe->cookbook }}</td>
                    </tr>
                    @endforeach
                </table>
                </div>
	</div>
	<div class="row">
		<div class="col-md-10 col-md-offset-2 center">
                    {!! $recipes->render() !!}
                </div>
	</div>
</div>
@endsection
