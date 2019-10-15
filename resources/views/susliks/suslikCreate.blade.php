@extends('layouts.adminApp')

@section('content')
    <h1>Создание суслика</h1>
    <div class="col-sm-12">
        <form class="form-horizontal" method="POST" action="{{ route('auth.susliks.store') }}">
            {{ csrf_field() }}

            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name" class="col-md-4 control-label">Имя</label>

                <div class="col-md-6">
                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required
                           autofocus>

                    @if ($errors->has('name'))
                        <span class="help-block">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6 col-md-offset-4">
                    <button type="submit" class="btn btn-primary">
                        Создать суслика
                    </button>
                </div>
            </div>
        </form>
    </div>    
@endsection