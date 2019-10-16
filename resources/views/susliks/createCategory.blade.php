@extends('layouts.adminApp')

@section('content')
    <h1>Создание категории</h1>
    <div class="col-sm-12">
        <textarea name="listOfCategories" cols="20" rows="10" disabled style="resize: none;">
            @foreach ($categories as $category)
            {{ $category->name }}
            @endforeach
        </textarea>
        <form class="form-horizontal" method="POST" action="{{ route('auth.storeCategory') }}">
            {{ csrf_field() }}

            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name" class="col-md-4 control-label">Наименование</label>

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
                        Создать категорию
                    </button>
                </div>
            </div>
        </form>
    </div>    
@endsection