@extends('layouts.adminApp')

@section('content')
<div class="col-sm-9 tabs-content">
   <h1>Создание суслика</h1>
    <div class="row">
        <div class="col-sm-12">
            <form class="form-horizontal" method="POST" action="{{ route('auth.susliks.store') }}">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="name" class="control-label">Имя:</label>
                        <input id="name" type="text" class="form-control input-create-poll" name="name" value="{{ old('name') }}" required
                               autofocus>
                        @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                </div>

                <div class="form-group{{ $errors->has('place_of_work') ? ' has-error' : '' }}">
                    <label for="place_of_work" class="control-label">Место работы:</label>
                        <input id="place_of_work" type="text" class="form-control input-create-poll" name="place_of_work" value="{{ old('place_of_work') }}" required>
                        @if ($errors->has('place_of_work'))
                            <span class="help-block">
                                <strong>{{ $errors->first('place_of_work') }}</strong>
                            </span>
                        @endif
                </div>

                <div class="form-group{{ $errors->has('position') ? ' has-error' : '' }}">
                    <label for="position" class="control-label">Должность:</label>
                        <input id="position" type="text" class="form-control input-create-poll" name="position" value="{{ old('position') }}" required>

                        @if ($errors->has('position'))
                            <span class="help-block">
                                <strong>{{ $errors->first('position') }}</strong>
                            </span>
                        @endif
                </div>

                <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                    <label for="position" class="control-label">Категория:</label>
                    <select name="category" id="category" class="form-control selectpoll">
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

                <div class="form-group{{ $errors->has('link') ? ' has-error' : '' }}">
                    <label for="link" class="control-label">Ссылка:</label>
                    <input id="link" type="text" class="form-control input-create-poll" name="link" value="{{ old('link') }}" required>
                    @if ($errors->has('link'))
                        <span class="help-block">
                            <strong>{{ $errors->first('link') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group{{ $errors->has('birthday') ? ' has-error' : '' }}">
                    <label for="birthday" class="control-label">Дата рождения</label>
                    <input id="birthday" type="date" class="form-control input-create-poll" name="birthday" value="{{ old('birthday') }}" required>
                    @if ($errors->has('birthday'))
                        <span class="help-block">
                            <strong>{{ $errors->first('birthday') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
                    <label for="photo" class="control-label">Фото:</label>
                    <input id="photo" type="file" class="form-control input-create-poll" name="photo" value="{{ old('photo') }}" required>
                    @if ($errors->has('photo'))
                        <span class="help-block">
                            <strong>{{ $errors->first('photo') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <div class="col-md-offset-4">
                        <button type="submit" class="btn-card">
                            Создать суслика
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div> 
</div>   
@endsection