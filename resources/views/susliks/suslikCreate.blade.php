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

            <div class="form-group{{ $errors->has('place_of_work') ? ' has-error' : '' }}">
                <label for="place_of_work" class="col-md-4 control-label">Место работы</label>

                <div class="col-md-6">
                    <input id="place_of_work" type="text" class="form-control" name="place_of_work" value="{{ old('place_of_work') }}" required>

                    @if ($errors->has('place_of_work'))
                        <span class="help-block">
                            <strong>{{ $errors->first('place_of_work') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('position') ? ' has-error' : '' }}">
                <label for="position" class="col-md-4 control-label">Должность</label>

                <div class="col-md-6">
                    <input id="position" type="text" class="form-control" name="position" value="{{ old('position') }}" required>

                    @if ($errors->has('position'))
                        <span class="help-block">
                            <strong>{{ $errors->first('position') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                <label for="position" class="col-md-4 control-label">Категория</label>
                <div class="col-md-6">
                    <select name="category" id="category" class="form-control">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>

                    @if ($errors->has('category'))
                        <span class="help-block">
                            <strong>{{ $errors->first('category') }}</strong>
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