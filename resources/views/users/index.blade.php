@extends('app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <h1>Gebruikers</h1>

                @if(Auth::check() && Auth::user()->isAdmin())
                    <p><a class="btn btn-success" data-toggle="collapse" href="#userform" aria-expanded="false" aria-controls="userform" role="button"><i class="fa fa-plus"></i> Nieuwe gebruiker</a></p>
                    <div class="collapse" id="userform">
                        <div class="well">
                            <form class="form-horizontal" role="form" method="POST" action="/users">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Name</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">E-Mail Address</label>
                                    <div class="col-md-6">
                                        <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Password</label>
                                    <div class="col-md-6">
                                        <input type="password" class="form-control" name="password">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Confirm Password</label>
                                    <div class="col-md-6">
                                        <input type="password" class="form-control" name="password_confirmation">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary">Gebruiker aanmaken</button>
                                    </div>
                                </div>

                                {!! csrf_field() !!}
                            </form>
                        </div>
                    </div>
                @endif

                <table class="table table-striped table-bordered">
                    <tr>
                        <th>ID</th>
                        <th>Naam</th>
                        <th>Email</th>
                        <th>Geregistreerd op</th>
                        <th></th>
                        <th></th>
                    </tr>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->getKey() }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->created_at }}</td>
                            <td>
                                @if($user->isApproved())
                                <button class="btn btn-sm btn-success">Goedgekeurd!</button>
                                @else
                                <form method="post" action="/users/{{ $user->getKey() }}/approve">
                                    {!! csrf_field() !!}
                                    <input type="submit" class="btn btn-sm btn-primary" value="Goedkeuren" />
                                </form>
                                @endif
                            </td>
                            <td>
                                <form method="post" action="/users/{{ $user->getKey() }}">
                                    {!! csrf_field() !!}
                                    <input type="hidden" name="_method" value="delete" />
                                    <input type="submit" class="btn btn-sm btn-danger" value="Verwijderen" onclick="if(!confirm('Weet je het zeker? Alle kookboeken en recepten van deze gebruiker worden ook verwijderd.')) { return false; }" />
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-2 center">
                {!! $users->render() !!}
            </div>
        </div>
    </div>
@endsection
